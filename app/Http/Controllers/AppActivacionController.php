<?php

namespace App\Http\Controllers;

use App\Models\AppActivacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppActivacionController extends Controller
{
    public function index()
    {
        can('listar-app-activaciones');
        return view('appactivaciones.index');
    }

    /**
     * POST /appactivaciones/buscar
     * Busca empleados por cédula o nombre y retorna sus datos + código de activación vigente.
     */
    public function buscar(Request $request)
    {
        $q = trim($request->q ?? '');

        $empleados = DB::table('nm_empleados')
            ->select('emp_ced', 'emp_nom', 'emp_ape', 'emp_codh', 'emp_staact')
            ->where(function ($query) use ($q) {
                $query->where('emp_ced', 'like', "%{$q}%")
                      ->orWhere('emp_nom', 'like', "%{$q}%")
                      ->orWhere('emp_ape', 'like', "%{$q}%");
            })
            ->groupBy('emp_ced', 'emp_nom', 'emp_ape', 'emp_codh', 'emp_staact')
            ->orderBy('emp_ape')
            ->limit(50)
            ->get();

        // Adjuntar código de activación vigente para cada cédula
        $cedulas = $empleados->pluck('emp_ced')->unique()->values();

        $activaciones = AppActivacion::whereIn('emp_ced', $cedulas)
            ->orderBy('created_at', 'desc')
            ->get()
            ->keyBy('emp_ced');

        $resultado = $empleados->map(function ($emp) use ($activaciones) {
            $act = $activaciones->get($emp->emp_ced);
            $emp->activacion_id   = $act ? $act->id : null;
            $emp->codigo          = $act ? $act->codigo : null;
            $emp->activo          = $act ? $act->activo : null;
            $emp->activado_en     = $act ? optional($act->activado_en)->format('d/m/Y H:i') : null;
            $emp->dispositivo_id  = $act ? $act->dispositivo_id : null;
            return $emp;
        });

        return response()->json(['data' => $resultado]);
    }

    /**
     * POST /appactivaciones/generar
     * Genera un código nuevo para una cédula (solo si no tiene uno pendiente).
     */
    public function generar(Request $request)
    {
        $cedula = trim($request->emp_ced ?? '');

        if (!$cedula) {
            return response()->json(['message' => 'Cédula requerida.'], 422);
        }

        $existe = DB::table('nm_empleados')->where('emp_ced', $cedula)->exists();
        if (!$existe) {
            return response()->json(['message' => 'Empleado no encontrado.'], 404);
        }

        $pendiente = AppActivacion::where('emp_ced', $cedula)
            ->where('activo', false)
            ->whereNull('dispositivo_id')
            ->first();

        if ($pendiente) {
            return response()->json([
                'message' => 'Ya existe un código pendiente.',
                'codigo'  => $pendiente->codigo,
                'id'      => $pendiente->id,
            ]);
        }

        $activacion = AppActivacion::create([
            'emp_ced' => $cedula,
            'codigo'  => strtoupper(Str::random(8)),
            'activo'  => false,
        ]);

        return response()->json([
            'message' => 'Código generado.',
            'codigo'  => $activacion->codigo,
            'id'      => $activacion->id,
        ]);
    }

    /**
     * POST /appactivaciones/regenerar
     * Invalida el código existente (pendiente) y genera uno nuevo.
     * No afecta activaciones ya completadas (dispositivo vinculado).
     */
    public function regenerar(Request $request)
    {
        $cedula = trim($request->emp_ced ?? '');

        if (!$cedula) {
            return response()->json(['message' => 'Cédula requerida.'], 422);
        }

        // Eliminar solo los pendientes (no activados)
        AppActivacion::where('emp_ced', $cedula)
            ->where('activo', false)
            ->whereNull('dispositivo_id')
            ->delete();

        $activacion = AppActivacion::create([
            'emp_ced' => $cedula,
            'codigo'  => strtoupper(Str::random(8)),
            'activo'  => false,
        ]);

        return response()->json([
            'message' => 'Código regenerado.',
            'codigo'  => $activacion->codigo,
            'id'      => $activacion->id,
        ]);
    }

    /**
     * POST /appactivaciones/generar-todos
     * Genera códigos para todos los empleados activos que no tienen uno pendiente.
     * Equivale al comando artisan pero desde la interfaz web.
     */
    public function generarTodos()
    {
        $empleados = DB::table('nm_empleados')
            ->select('emp_ced')
            ->where('emp_staact', 1)
            ->whereNotIn('emp_ced', function ($q) {
                $q->select('emp_ced')
                  ->from('app_activaciones')
                  ->where(function ($inner) {
                      $inner->where('activo', true)->orWhereNull('dispositivo_id');
                  });
            })
            ->groupBy('emp_ced')
            ->get();

        $creados = 0;
        foreach ($empleados as $emp) {
            $existe = AppActivacion::where('emp_ced', $emp->emp_ced)
                ->where('activo', false)->whereNull('dispositivo_id')->exists();
            if (!$existe) {
                AppActivacion::create([
                    'emp_ced' => $emp->emp_ced,
                    'codigo'  => strtoupper(Str::random(8)),
                    'activo'  => false,
                ]);
                $creados++;
            }
        }

        return response()->json(['message' => "Se generaron {$creados} código(s) nuevo(s)."]);
    }

    /**
     * POST /appactivaciones/revocar
     * Revoca todos los tokens Sanctum activos de un empleado y elimina las activaciones
     * (útil si pierde el teléfono o hay que resetear el acceso).
     */
    public function revocar(Request $request)
    {
        $cedula = trim($request->emp_ced ?? '');

        if (!$cedula) {
            return response()->json(['message' => 'Cédula requerida.'], 422);
        }

        // Revocar tokens Sanctum si el usuario existe
        $usuario = \App\Models\Seguridad\Usuario::where('usuario', $cedula)->first();
        if ($usuario) {
            $usuario->tokens()->where('name', 'app-movil')->delete();
        }

        // Eliminar todas las activaciones (incluyendo activas) para forzar re-activación
        AppActivacion::where('emp_ced', $cedula)->delete();

        return response()->json(['message' => 'Acceso revocado. El empleado deberá activar la app nuevamente.']);
    }
}
