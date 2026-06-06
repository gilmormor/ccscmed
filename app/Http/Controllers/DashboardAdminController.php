<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Administrativo — BI Nómina
 *
 * FUENTE: nm_movhist (VIEW) JOIN nm_movhismonext, nm_control, nm_empleados, nm_conceptos
 * REGLA:  nm_movhist es VIEW → sin alias en JOINs, filtros en WHERE
 * MONEDA: mme_montomone = Bs  |  mme_montodl = Bs / tasa  = USD
 */
class DashboardAdminController extends Controller
{
    /* ------------------------------------------------------------------
     * Fragmento FROM compartido por todos los métodos
     * ------------------------------------------------------------------ */
    private function baseFrom(): string
    {
        return "
            FROM nm_movhist
            INNER JOIN nm_movhismonext ON nm_movhist.mov_id        = nm_movhismonext.mov_id
            INNER JOIN nm_control      ON nm_control.cot_numnom    = nm_movhist.mov_nummon
            INNER JOIN nm_empleados    ON nm_empleados.emp_ced     = nm_movhist.emp_ced
            INNER JOIN nm_conceptos    ON nm_conceptos.con_cod     = nm_movhist.mov_codcon
        ";
    }

    /* ------------------------------------------------------------------
     * Construir WHERE dinámico con bindings
     * ------------------------------------------------------------------ */
    private function buildWhere(Request $request, array &$b): string
    {
        $w = " WHERE 1=1 ";

        // ── Período
        $periodo = $request->get('periodo', '12m');
        if ($periodo === 'custom') {
            if ($request->filled('fecha_desde')) {
                $w .= " AND nm_control.cot_fdesde >= ? "; $b[] = $request->fecha_desde;
            }
            if ($request->filled('fecha_hasta')) {
                $w .= " AND nm_control.cot_fhasta <= ? "; $b[] = $request->fecha_hasta;
            }
        } elseif ($periodo === 'mes') {
            $w .= " AND YEAR(nm_control.cot_fdesde)  = YEAR(NOW())
                   AND MONTH(nm_control.cot_fdesde) = MONTH(NOW()) ";
        } elseif ($periodo === 'anio') {
            $w .= " AND YEAR(nm_control.cot_fdesde) = YEAR(NOW()) ";
        } elseif (in_array($periodo, ['3m','6m','12m'])) {
            $meses = (int) str_replace('m', '', $periodo);
            $w .= " AND nm_control.cot_fdesde >= DATE_SUB(NOW(), INTERVAL ? MONTH) "; $b[] = $meses;
        }

        // ── Trabajador individual
        if ($request->filled('emp_ced') && is_numeric($request->emp_ced)) {
            $w .= " AND nm_movhist.emp_ced = ? "; $b[] = intval($request->emp_ced);
        }

        // ── Conceptos múltiples (CSV numérico)
        if ($request->filled('conceptos')) {
            $conceptos = array_values(array_filter(
                array_map('intval', explode(',', $request->conceptos)),
                fn($v) => $v > 0
            ));
            if (!empty($conceptos)) {
                $ph = implode(',', array_fill(0, count($conceptos), '?'));
                $w .= " AND nm_movhist.mov_codcon IN ($ph) ";
                foreach ($conceptos as $c) $b[] = $c;
            }
        }

        return $w;
    }

    /* ------------------------------------------------------------------
     * INDEX
     * ------------------------------------------------------------------ */
    public function index()
    {
        can('listar-dashboard-admin');
        return view('dashboardadmin.index');
    }

    /* ------------------------------------------------------------------
     * KPIs principales
     * ------------------------------------------------------------------ */
    public function kpis(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $row = DB::selectOne("
            SELECT
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS ded_usd,
                COUNT(DISTINCT nm_movhist.emp_ced)    AS total_trab,
                COUNT(*)                              AS total_mov,
                COUNT(DISTINCT nm_movhist.mov_codcon) AS total_conceptos,
                COUNT(DISTINCT nm_movhist.mov_nummon) AS total_periodos,
                AVG(NULLIF(nm_control.cot_valordolar, 0)) AS tasa_avg
            " . $this->baseFrom() . $w
        , $b);

        $asig_bs = round($row->asig_bs  ?? 0, 2);
        $ded_bs  = round($row->ded_bs   ?? 0, 2);
        $neto_bs = round($asig_bs - $ded_bs, 2);
        $trab    = $row->total_trab ?? 0;

        return response()->json([
            'asig_bs'          => $asig_bs,
            'ded_bs'           => $ded_bs,
            'asig_usd'         => round($row->asig_usd ?? 0, 2),
            'ded_usd'          => round($row->ded_usd  ?? 0, 2),
            'neto_bs'          => $neto_bs,
            'neto_usd'         => round(($row->asig_usd ?? 0) - ($row->ded_usd ?? 0), 2),
            'total_trab'       => $trab,
            'total_mov'        => $row->total_mov       ?? 0,
            'total_conceptos'  => $row->total_conceptos ?? 0,
            'total_periodos'   => $row->total_periodos  ?? 0,
            'tasa_avg'         => round($row->tasa_avg  ?? 0, 2),
            'prom_por_trab'    => $trab > 0 ? round($neto_bs / $trab, 2) : 0,
        ]);
    }

    /* ------------------------------------------------------------------
     * Evolución mensual — asig, ded y neto agrupados por mes
     * ------------------------------------------------------------------ */
    public function evolucion(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m')  AS mes_key,
                DATE_FORMAT(nm_control.cot_fdesde,'%b %Y')  AS mes_label,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd
            " . $this->baseFrom() . $w . "
            GROUP BY mes_key, mes_label
            ORDER BY mes_key ASC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Distribución A vs D (donut)
     * ------------------------------------------------------------------ */
    public function distribucion(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_movhist.mov_tipocon                     AS tipo,
                SUM(nm_movhismonext.mme_montomone)         AS total_bs,
                SUM(nm_movhismonext.mme_montodl)           AS total_usd,
                COUNT(*)                                   AS cantidad
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.mov_tipocon
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Top 10 conceptos por monto acumulado
     * ------------------------------------------------------------------ */
    public function topConceptos(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_conceptos.con_desc                      AS concepto,
                nm_movhist.mov_tipocon                     AS tipo,
                COUNT(*)                                   AS frecuencia,
                SUM(nm_movhismonext.mme_montomone)         AS total_bs,
                SUM(nm_movhismonext.mme_montodl)           AS total_usd
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.mov_codcon, nm_conceptos.con_desc, nm_movhist.mov_tipocon
            ORDER BY total_bs DESC
            LIMIT 10
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Top 10 trabajadores por monto neto
     * ------------------------------------------------------------------ */
    public function topTrabajadores(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_movhist.emp_ced,
                nm_empleados.emp_nom,
                nm_empleados.emp_ape,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.emp_ced, nm_empleados.emp_nom, nm_empleados.emp_ape
            ORDER BY asig_bs DESC
            LIMIT 10
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Comparativo Bs vs USD por mes (solo asignaciones para comparación limpia)
     * ------------------------------------------------------------------ */
    public function comparativoBsUsd(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m')  AS mes_key,
                DATE_FORMAT(nm_control.cot_fdesde,'%b %Y')  AS mes_label,
                SUM(nm_movhismonext.mme_montomone)           AS total_bs,
                SUM(nm_movhismonext.mme_montodl)             AS total_usd,
                AVG(NULLIF(nm_movhismonext.mme_tasacambi,0)) AS tasa_avg
            " . $this->baseFrom() . $w . "
            AND nm_movhist.mov_tipocon = 'A'
            GROUP BY mes_key, mes_label
            ORDER BY mes_key ASC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Evolución de la tasa de cambio (solo nm_control, consulta liviana)
     * ------------------------------------------------------------------ */
    public function evolucionDolar(Request $request)
    {
        $b = [];
        $w = " WHERE nm_control.cot_valordolar > 0 ";

        $periodo = $request->get('periodo', '12m');
        if ($periodo === 'custom' && $request->filled('fecha_desde')) {
            $w .= " AND nm_control.cot_fdesde >= ? "; $b[] = $request->fecha_desde;
        } elseif ($periodo === 'mes') {
            $w .= " AND YEAR(nm_control.cot_fdesde)=YEAR(NOW()) AND MONTH(nm_control.cot_fdesde)=MONTH(NOW()) ";
        } elseif ($periodo === 'anio') {
            $w .= " AND YEAR(nm_control.cot_fdesde)=YEAR(NOW()) ";
        } elseif (in_array($periodo, ['3m','6m','12m'])) {
            $meses = (int) str_replace('m', '', $periodo);
            $w .= " AND nm_control.cot_fdesde >= DATE_SUB(NOW(), INTERVAL ? MONTH) "; $b[] = $meses;
        }

        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m')  AS mes_key,
                DATE_FORMAT(nm_control.cot_fdesde,'%b %Y')  AS mes_label,
                AVG(nm_control.cot_valordolar)               AS tasa_avg,
                MAX(nm_control.cot_valordolar)               AS tasa_max,
                MIN(nm_control.cot_valordolar)               AS tasa_min
            FROM nm_control $w
            GROUP BY mes_key, mes_label
            ORDER BY mes_key ASC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Últimos 200 movimientos (tabla detalle)
     * ------------------------------------------------------------------ */
    public function movimientos(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_control.cot_fdesde,'%d/%m/%Y')              AS fecha,
                nm_movhist.emp_ced,
                CONCAT(nm_empleados.emp_nom,' ',nm_empleados.emp_ape)       AS trabajador,
                nm_conceptos.con_desc                                        AS concepto,
                nm_movhist.mov_tipocon                                       AS tipo,
                nm_movhismonext.mme_montomone                                AS monto_bs,
                nm_movhismonext.mme_montodl                                  AS monto_usd,
                nm_movhismonext.mme_tasacambi                                AS tasa,
                nm_control.cot_fdesde                                        AS fecha_ord
            " . $this->baseFrom() . $w . "
            ORDER BY nm_control.cot_fdesde DESC, nm_movhist.emp_ced ASC
            LIMIT 200
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Ranking completo de conceptos (tabla)
     * ------------------------------------------------------------------ */
    public function rankingConceptos(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_conceptos.con_desc                      AS concepto,
                nm_movhist.mov_tipocon                     AS tipo,
                COUNT(*)                                   AS frecuencia,
                SUM(nm_movhismonext.mme_montomone)         AS total_bs,
                SUM(nm_movhismonext.mme_montodl)           AS total_usd
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.mov_codcon, nm_conceptos.con_desc, nm_movhist.mov_tipocon
            ORDER BY total_bs DESC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ------------------------------------------------------------------
     * Lista de trabajadores para el filtro select
     * ------------------------------------------------------------------ */
    public function filtroTrabajadores()
    {
        $rows = DB::select("
            SELECT DISTINCT
                nm_movhist.emp_ced,
                CONCAT(nm_empleados.emp_nom,' ',nm_empleados.emp_ape) AS nombre
            FROM nm_movhist
            INNER JOIN nm_empleados ON nm_empleados.emp_ced = nm_movhist.emp_ced
            ORDER BY nm_empleados.emp_ape, nm_empleados.emp_nom
        ");
        return response()->json($rows);
    }

    /* ------------------------------------------------------------------
     * Lista de conceptos para el filtro select
     * ------------------------------------------------------------------ */
    public function filtroConceptos()
    {
        $rows = DB::select("
            SELECT DISTINCT
                nm_movhist.mov_codcon                AS cod,
                nm_conceptos.con_desc                AS descripcion,
                nm_movhist.mov_tipocon               AS tipo
            FROM nm_movhist
            INNER JOIN nm_conceptos ON nm_conceptos.con_cod = nm_movhist.mov_codcon
            ORDER BY nm_movhist.mov_tipocon, nm_conceptos.con_desc
        ");
        return response()->json($rows);
    }
}
