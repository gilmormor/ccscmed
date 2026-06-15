<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

/**
 * Dashboard Laboral — Portal Mi Nómina & Honorarios
 *
 * FUENTE DE DATOS:
 *   - Recibos / historial : nm_movhist JOIN nm_control  (mismo patrón que periodosnompersona)
 *   - Sin alias en nm_movhist  → nm_movhist es una VIEW y los aliases rompen el JOIN
 *   - Filtros en WHERE     → no en la cláusula ON
 *   - Donut "Por tipo"     : nm_honpacientedet + tipodocumento
 */
class DashboardLaboralController extends Controller
{
    public function index()
    {
        can('listar-dashboard-laboral');
        $usuario     = Usuario::findOrFail(auth()->id());
        $aux_cedula  = $usuario->usuario;
        $esEmpleado  = false;
        $nm_empleado = null;
        if (is_numeric($aux_cedula)) {
            $nm_empleado = DB::table('nm_empleados')->where('emp_ced', $aux_cedula)->first();
            $esEmpleado  = $nm_empleado ? true : false;
        }
        return view('dashboardlaboral.index', compact('usuario', 'nm_empleado', 'esEmpleado'));
    }

    /* ------------------------------------------------------------------
     * KPIs principales
     * ------------------------------------------------------------------ */
    public function kpis(Request $request)
    {
        $usuario    = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;
        if (!is_numeric($aux_cedula)) {
            return response()->json(['sin_cedula' => true]);
        }

        $anio          = !empty($request->anio) ? intval($request->anio) : null;
        $whereAnioCtrl = $anio ? "AND YEAR(nm_control.cot_fdesde) = $anio" : "";

        // ── Último período (sin filtro de año — siempre el más reciente)
        $ultimoPeriodo = DB::selectOne("
            SELECT nm_control.id, nm_control.cot_numnom,
                   DATE_FORMAT(nm_control.cot_fdesde,'%d/%m/%Y') AS fdesde,
                   DATE_FORMAT(nm_control.cot_fhasta,'%d/%m/%Y') AS fhasta,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhist.mov_monto ELSE 0 END) AS asignaciones,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhist.mov_monto ELSE 0 END) AS deducciones
            FROM nm_movhist
            INNER JOIN nm_control ON nm_movhist.mov_nummon = nm_control.cot_numnom
            WHERE nm_movhist.emp_ced = $aux_cedula
            GROUP BY nm_movhist.mov_nummon, nm_control.id, nm_control.cot_numnom,
                     nm_control.cot_fdesde, nm_control.cot_fhasta
            ORDER BY nm_control.cot_fdesde DESC LIMIT 1
        ");

        $ultimoNeto = $ultimoPeriodo
            ? ($ultimoPeriodo->asignaciones - $ultimoPeriodo->deducciones)
            : 0;

        // ── Totales (con filtro de año si aplica)
        $totales = DB::selectOne("
            SELECT
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhist.mov_monto ELSE 0 END) AS total_asig,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhist.mov_monto ELSE 0 END) AS total_ded,
                COUNT(DISTINCT nm_movhist.mov_nummon)                                          AS total_periodos
            FROM nm_movhist
            INNER JOIN nm_control ON nm_movhist.mov_nummon = nm_control.cot_numnom
            WHERE nm_movhist.emp_ced = $aux_cedula $whereAnioCtrl
        ");

        $totalNeto   = ($totales->total_asig ?? 0) - ($totales->total_ded ?? 0);
        $totalPeriodos = $totales->total_periodos ?? 0;

        // ── Promedio mensual (meses con al menos un movimiento)
        $mesesRes = DB::selectOne("
            SELECT COUNT(DISTINCT DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m')) AS meses
            FROM nm_movhist
            INNER JOIN nm_control ON nm_movhist.mov_nummon = nm_control.cot_numnom
            WHERE nm_movhist.emp_ced = $aux_cedula $whereAnioCtrl
        ");
        $meses    = max(1, $mesesRes->meses ?? 1);
        $promedio = round($totalNeto / $meses, 2);

        return response()->json([
            'ultimo_neto'    => $ultimoNeto,
            'periodo_fdesde' => $ultimoPeriodo ? $ultimoPeriodo->fdesde : null,
            'periodo_fhasta' => $ultimoPeriodo ? $ultimoPeriodo->fhasta : null,
            'total_neto'     => $totalNeto,
            'total_asig'     => $totales->total_asig ?? 0,
            'total_ded'      => $totales->total_ded  ?? 0,
            'total_periodos' => $totalPeriodos,
            'promedio'       => $promedio,
            'anio'           => $anio,
        ]);
    }

    /* ------------------------------------------------------------------
     * Evolución mensual de neto (asig - ded) agrupada por mes
     * SIN alias en nm_movhist — mismo patrón que periodosnompersona()
     * ------------------------------------------------------------------ */
    public function evolucionIngresos(Request $request)
    {
        $usuario    = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;
        if (!is_numeric($aux_cedula)) {
            return response()->json(['sin_cedula' => true, 'meses' => [], 'nomina' => []]);
        }

        $anio          = !empty($request->anio) ? intval($request->anio) : null;
        $whereAnioCtrl = $anio ? "AND YEAR(nm_control.cot_fdesde) = $anio" : "";

        $meses = DB::select("
            SELECT DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m')  AS mes_key,
                   DATE_FORMAT(nm_control.cot_fdesde,'%b %Y')  AS mes_label,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhist.mov_monto ELSE 0 END) AS asignaciones,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhist.mov_monto ELSE 0 END) AS deducciones
            FROM nm_movhist
            INNER JOIN nm_control ON nm_movhist.mov_nummon = nm_control.cot_numnom
            WHERE nm_movhist.emp_ced = $aux_cedula $whereAnioCtrl
            GROUP BY mes_key, mes_label
            ORDER BY mes_key ASC
        ");

        return response()->json(['meses' => $meses, 'anio' => $anio]);
    }

    /* ------------------------------------------------------------------
     * Honorarios por tipo de documento (donut) — nm_honpacientedet
     * Esta tabla sí es una tabla real con alias; se mantiene sin cambios
     * ------------------------------------------------------------------ */
    public function composicionRecibo(Request $request)
    {
        $usuario    = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;
        if (!is_numeric($aux_cedula)) {
            return response()->json(['tipos' => [], 'total' => 0]);
        }

        $tipos = DB::select("
            SELECT COALESCE(td.desc,'Sin clasificar') AS tipo,
                   COALESCE(SUM(hd.honorario),0)      AS total,
                   COUNT(*)                           AS cantidad
            FROM nm_honpacientedet hd
            INNER JOIN nm_controlnomcls cls ON hd.nm_controlnomcls_id = cls.id
            INNER JOIN nm_control nc         ON cls.nm_control_id = nc.id
            LEFT JOIN tipodocumento td        ON td.tipodoc = hd.tipo_documento
            WHERE hd.emp_ced = ?
            GROUP BY hd.tipo_documento, td.desc
            ORDER BY total DESC
        ", [$aux_cedula]);

        $total = array_sum(array_map(function($t) { return $t->total; }, $tipos));
        return response()->json(['tipos' => $tipos, 'total' => $total]);
    }

    /* ------------------------------------------------------------------
     * Historial de recibos — nm_movhist + nm_control
     * = todos los períodos con recibo (mismo origen que periodosnompersona)
     * ------------------------------------------------------------------ */
    public function historialHonorarios(Request $request)
    {
        $usuario    = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;
        if (!is_numeric($aux_cedula)) {
            return response()->json(['data' => []]);
        }

        $anio          = !empty($request->anio) ? intval($request->anio) : null;
        $whereAnioCtrl = $anio ? "AND YEAR(nm_control.cot_fdesde) = $anio" : "";

        $periodos = DB::select("
            SELECT nm_control.id                                    AS nmcontrol_id,
                   nm_control.cot_numnom,
                   DATE_FORMAT(nm_control.cot_fdesde,'%d/%m/%Y')   AS fdesde,
                   DATE_FORMAT(nm_control.cot_fhasta,'%d/%m/%Y')   AS fhasta,
                   nm_control.cot_fdesde                           AS fecha_orden,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhist.mov_monto ELSE 0 END) AS asignaciones,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhist.mov_monto ELSE 0 END) AS deducciones,
                   SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhist.mov_monto ELSE 0 END) -
                   SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhist.mov_monto ELSE 0 END) AS neto
            FROM nm_movhist
            INNER JOIN nm_control ON nm_movhist.mov_nummon = nm_control.cot_numnom
            WHERE nm_movhist.emp_ced = $aux_cedula $whereAnioCtrl
            GROUP BY nm_movhist.mov_nummon, nm_control.id, nm_control.cot_numnom,
                     nm_control.cot_fdesde, nm_control.cot_fhasta
            ORDER BY nm_control.cot_fdesde DESC
        ");

        return response()->json(['data' => $periodos]);
    }

    /** Compatibilidad — no se usa en el dashboard actual */
    public function historialNomina(Request $request)
    {
        return $this->historialHonorarios($request);
    }

    /* ------------------------------------------------------------------
     * PDF Constancia de Trabajo
     * ------------------------------------------------------------------ */
    public function constanciaPdf(Request $request)
    {
        $usuario    = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;
        if (!is_numeric($aux_cedula)) { abort(403, 'No autorizado'); }

        $nm_empleado = DB::selectOne("
            SELECT e.*, c.car_desc, u.ubi_desc, tp.tmo_desc
            FROM nm_empleados e
            LEFT JOIN nm_movnomtrab mn ON mn.mov_ced = e.emp_ced AND mn.emp_codh = e.emp_codh
            LEFT JOIN nm_cargos c      ON c.car_cod  = mn.mov_codcar   AND c.emp_codh = e.emp_codh
            LEFT JOIN nm_ubicacion u   ON u.ubi_cod  = mn.mov_codubica AND u.emp_codh = e.emp_codh
            LEFT JOIN nm_control nc    ON nc.cot_numnom = mn.mov_numnom AND nc.emp_codh = e.emp_codh
            LEFT JOIN nm_tiponomina tp ON tp.tmo_cod = nc.cot_tipo     AND tp.emp_codh = e.emp_codh
            WHERE e.emp_ced = ?
            ORDER BY nc.cot_fdesde DESC LIMIT 1
        ", [$aux_cedula]);

        if (!$nm_empleado) { abort(404, 'Empleado no encontrado'); }

        $empresa       = Empresa::orderBy('id')->first();
        $fecha_emision = now()->format('d/m/Y');
        $pdf = PDF::loadView('dashboardlaboral.constancia_pdf',
            compact('nm_empleado', 'empresa', 'usuario', 'fecha_emision'));

        $apellido = ucwords(strtolower(explode(' ', trim($nm_empleado->emp_ape))[0]));
        $nombre   = ucwords(strtolower(explode(' ', trim($nm_empleado->emp_nom))[0]));
        $cedula   = str_pad($nm_empleado->emp_ced, 8, '0', STR_PAD_LEFT);
        return $pdf->stream("Constancia_{$cedula}_{$apellido}{$nombre}.pdf");
    }
}
