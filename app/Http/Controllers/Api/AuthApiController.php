<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppActivacion;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * POST /api/auth/activar
     *
     * Valida cédula + código de activación.
     * Si es válido, marca la activación, vincula el dispositivo y retorna un token Sanctum sin expiración.
     * Si el usuario no existe en la tabla `usuario`, lo crea automáticamente (igual que sendpass()).
     */
    public function activar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_ced'       => 'required|string',
            'codigo'        => 'required|string',
            'dispositivo_id'=> 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos incompletos.', 'errors' => $validator->errors()], 422);
        }

        $cedula       = trim($request->emp_ced);
        $codigo       = strtoupper(trim($request->codigo));
        $dispositivoId = trim($request->dispositivo_id);

        // Verificar que el empleado exista en nm_empleados
        $nmEmpleado = DB::table('nm_empleados')->where('emp_ced', $cedula)->first();
        if (!$nmEmpleado) {
            return response()->json(['message' => 'Cédula no encontrada.'], 404);
        }

        // Buscar código válido: nuevo (no activado) O reactivación desde el mismo dispositivo
        $activacion = AppActivacion::where('emp_ced', $cedula)
            ->where('codigo', $codigo)
            ->where(function ($q) use ($dispositivoId) {
                // Primera activación: código sin usar
                $q->where('activo', false)->whereNull('dispositivo_id');
                // Reactivación: mismo dispositivo (usuario cerró sesión y vuelve a entrar)
                $q->orWhere(function ($q2) use ($dispositivoId) {
                    $q2->where('activo', true)->where('dispositivo_id', $dispositivoId);
                });
            })
            ->first();

        if (!$activacion) {
            return response()->json(['message' => 'Código de activación inválido o ya utilizado.'], 401);
        }

        // Obtener o crear usuario en la tabla `usuario`
        $usuario = Usuario::where('usuario', $cedula)->first();
        if (!$usuario) {
            $usuario = Usuario::create([
                'usuario'  => $cedula,
                'nombre'   => trim($nmEmpleado->emp_nom . ' ' . $nmEmpleado->emp_ape),
                'email'    => $nmEmpleado->emp_email ?? ($cedula . '@app.local'),
                'password' => bcrypt($cedula . '_app'),
            ]);

            // Asignar rol empleado (rol 2)
            DB::table('usuario_rol')->insert([
                'usuario_id' => $usuario->id,
                'rol_id'     => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar sucursal 1 si existe
            $sucursalExiste = DB::table('sucursal')->where('id', 1)->exists();
            if ($sucursalExiste) {
                DB::table('sucursal_usuario')->insert([
                    'usuario_id'  => $usuario->id,
                    'sucursal_id' => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // Marcar activación como usada
        $activacion->update([
            'dispositivo_id' => $dispositivoId,
            'activado_en'    => now(),
            'activo'         => true,
        ]);

        // Revocar tokens anteriores de la app para este usuario (si los hubiera)
        $usuario->tokens()->where('name', 'app-movil')->delete();

        // Crear token sin expiración
        $token = $usuario->createToken('app-movil')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'usuario' => [
                'emp_ced' => $cedula,
                'nombre'  => $usuario->nombre,
            ],
        ]);
    }

    /**
     * GET /api/auth/me
     * Verifica que el token sigue activo. La app lo llama al arrancar.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'ok'     => true,
            'nombre' => $user->nombre,
        ]);
    }

    /**
     * POST /api/auth/logout
     * Revoca el token actual.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada.']);
    }
}
