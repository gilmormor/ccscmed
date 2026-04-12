<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppMarcaje;
use App\Models\AppMarcajeExento;
use App\Models\NmEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarcajeApiController extends Controller
{
    /**
     * Distancia en metros entre dos coordenadas (fórmula Haversine).
     */
    private function distanciaMetros($lat1, $lng1, $lat2, $lng2)
    {
        $R    = 6371000; // radio Tierra en metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) * sin($dLat / 2)
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
              * sin($dLng / 2) * sin($dLng / 2);
        $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    /**
     * Verifica si el empleado está exento de validación de perímetro
     * para la empresa dada.
     */
    private function esExento($emp_ced, $emp_codh)
    {
        return AppMarcajeExento::where('emp_ced', $emp_ced)
            ->where(function ($q) use ($emp_codh) {
                $q->whereNull('emp_codh')
                  ->orWhere('emp_codh', $emp_codh);
            })
            ->exists();
    }

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve el nombre de la empresa para identificarla en la app.
     * GET /api/marcaje/empresa
     */
    public function empresa(Request $request)
    {
        $user    = $request->user();
        $emp_ced = $user->usuario;

        $empresa = DB::table('nm_empleados as e')
            ->join('nm_empresa as emp', 'emp.emp_codh', '=', 'e.emp_codh')
            ->where('e.emp_ced', $emp_ced)
            ->select('emp.emp_nombre')
            ->first();

        return response()->json([
            'nombre' => $empresa->emp_nombre ?? config('app.name'),
        ]);
    }

    /**
     * Registra un marcaje de Entrada o Salida.
     * Valida perímetro GPS si la empresa lo tiene configurado.
     * POST /api/marcaje/registrar
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'emp_ced'  => 'required|string',
            'tipo'     => 'required|in:E,S',
            'latitud'  => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $user    = $request->user();
        $emp_ced = $user->usuario;

        // Buscar emp_codh del empleado
        $empleado = DB::table('nm_empleados')
            ->where('emp_ced', $emp_ced)
            ->select('emp_codh')
            ->first();

        $emp_codh = $empleado->emp_codh ?? null;

        // ── Validación de perímetro ───────────────────────────────────────────
        if ($emp_codh) {
            $empresa = NmEmpresa::find($emp_codh);

            if ($empresa && $empresa->marc_valida_perimetro) {
                // ¿El empleado está exento?
                if (!$this->esExento($emp_ced, $emp_codh)) {
                    // Necesita GPS
                    if (is_null($request->latitud) || is_null($request->longitud)) {
                        return response()->json([
                            'ok'      => false,
                            'fuera'   => true,
                            'mensaje' => 'Esta empresa requiere GPS para marcar. Activa la ubicación e intenta de nuevo.',
                        ], 422);
                    }

                    // Solo valida si el centro está configurado
                    if ($empresa->marc_lat && $empresa->marc_lng) {
                        $distancia   = $this->distanciaMetros(
                            $empresa->marc_lat, $empresa->marc_lng,
                            $request->latitud,  $request->longitud
                        );
                        $radioMax = ($empresa->marc_radio ?? 100) + ($empresa->marc_tolerancia ?? 50);

                        if ($distancia > $radioMax) {
                            return response()->json([
                                'ok'         => false,
                                'fuera'      => true,
                                'distancia'  => (int) round($distancia),
                                'radio_max'  => $radioMax,
                                'mensaje'    => 'Estás a ' . round($distancia) . ' m del perímetro permitido (' . $radioMax . ' m). Debes estar en la empresa para marcar.',
                            ], 422);
                        }
                    }
                }
            }
        }

        // ── Registrar marcaje ─────────────────────────────────────────────────
        $hoy  = Carbon::today()->toDateString();
        $hora = Carbon::now()->format('H:i:s');

        $tokenActual    = $user->currentAccessToken();
        $dispositivo_id = $tokenActual ? $tokenActual->name : null;

        $marcaje = AppMarcaje::create([
            'emp_ced'        => $emp_ced,
            'emp_codh'       => $emp_codh,
            'tipo'           => $request->tipo,
            'fecha'          => $hoy,
            'hora'           => $hora,
            'latitud'        => $request->latitud,
            'longitud'       => $request->longitud,
            'dispositivo_id' => $dispositivo_id,
        ]);

        return response()->json([
            'ok'    => true,
            'id'    => $marcaje->id,
            'tipo'  => $marcaje->tipo,
            'fecha' => $hoy,
            'hora'  => substr($hora, 0, 5),
        ]);
    }

    /**
     * Marcajes de hoy del empleado autenticado.
     * GET /api/marcaje/hoy
     */
    public function hoy(Request $request)
    {
        $user    = $request->user();
        $emp_ced = $user->usuario;
        $hoy     = Carbon::today()->toDateString();

        $marcajes = AppMarcaje::where('emp_ced', $emp_ced)
            ->where('fecha', $hoy)
            ->orderBy('hora')
            ->get(['tipo', 'hora', 'latitud', 'longitud'])
            ->map(function ($m) {
                return [
                    'tipo'     => $m->tipo,
                    'hora'     => substr($m->hora, 0, 5),
                    'latitud'  => $m->latitud,
                    'longitud' => $m->longitud,
                ];
            });

        $ultimo     = $marcajes->last();
        $ultimoTipo = $ultimo['tipo'] ?? null;
        $ultimaHora = $ultimo['hora'] ?? null;

        return response()->json([
            'ultimo_tipo' => $ultimoTipo,
            'ultima_hora' => $ultimaHora,
            'marcajes'    => $marcajes->values(),
        ]);
    }

    /**
     * Historial de marcajes de los últimos N días.
     * GET /api/marcaje/historial?dias=30
     */
    public function historial(Request $request)
    {
        $user    = $request->user();
        $emp_ced = $user->usuario;
        $dias    = (int) $request->input('dias', 30);
        $dias    = min(max($dias, 1), 90);

        $desde = Carbon::today()->subDays($dias - 1)->toDateString();

        $marcajes = AppMarcaje::where('emp_ced', $emp_ced)
            ->where('fecha', '>=', $desde)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora')
            ->get(['fecha', 'tipo', 'hora', 'latitud', 'longitud']);

        $agrupados = $marcajes->groupBy(function ($m) {
                return $m->fecha->toDateString();
            })
            ->map(function ($items, $fecha) {
                return [
                    'fecha'    => $fecha,
                    'marcajes' => $items->map(function ($m) {
                        return [
                            'tipo'     => $m->tipo,
                            'hora'     => substr($m->hora, 0, 5),
                            'latitud'  => $m->latitud,
                            'longitud' => $m->longitud,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json(['data' => $agrupados]);
    }
}
