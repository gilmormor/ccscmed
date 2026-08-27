<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Ejecutivo — Honorarios Profesionales
 *
 * FUENTES:
 *   Montos    → nm_movhist + nm_movhismonext + nm_conceptos + nm_control
 *   Pacientes → nm_control → nm_controlnomcls → nm_honpacientedet + tipodocumento
 *
 * MÉDICO: se considera médico a quien aparece en nm_honpacientedet.
 * MONEDA: mme_montomone = Bs | mme_montodl = USD | mme_tasacambiorig = tasa BCV
 *
 * Los montos se devuelven SIEMPRE en positivo (requerimiento 9): el signo se
 * deriva de mov_tipocon (A = asignación, D = deducción).
 */
class DashboardHonController extends Controller
{
    /* ==================================================================
     * FROM compartido — honorarios de médicos
     * ================================================================== */
    private function baseFrom(): string
    {
        return "
            FROM nm_movhist
            LEFT JOIN  nm_movhismonext ON nm_movhist.mov_id     = nm_movhismonext.mov_id
            INNER JOIN nm_control      ON nm_control.cot_numnom = nm_movhist.mov_nummon
            INNER JOIN nm_empleados    ON nm_empleados.emp_ced  = nm_movhist.emp_ced
            INNER JOIN nm_conceptos    ON nm_conceptos.con_cod  = nm_movhist.mov_codcon
            INNER JOIN (SELECT DISTINCT emp_ced FROM nm_honpacientedet) med
                    ON med.emp_ced = nm_movhist.emp_ced
        ";
    }

    /* ==================================================================
     * WHERE dinámico
     * ================================================================== */
    private function buildWhere(Request $request, array &$b): string
    {
        $w = " WHERE 1=1 ";

        $periodo = $request->get('periodo', 'mes');

        if ($periodo === 'custom') {
            if ($request->filled('fecha_desde')) {
                $w .= " AND nm_control.cot_fdesde >= ? "; $b[] = $request->fecha_desde;
            }
            if ($request->filled('fecha_hasta')) {
                $w .= " AND nm_control.cot_fhasta <= ? "; $b[] = $request->fecha_hasta;
            }
        } elseif ($periodo === 'hoy') {
            $w .= " AND DATE(nm_control.cot_fdesde) = CURDATE() ";
        } elseif ($periodo === 'semana') {
            $w .= " AND YEARWEEK(nm_control.cot_fdesde, 1) = YEARWEEK(NOW(), 1) ";
        } elseif ($periodo === 'mes') {
            $w .= " AND YEAR(nm_control.cot_fdesde)  = YEAR(NOW())
                    AND MONTH(nm_control.cot_fdesde) = MONTH(NOW()) ";
        } elseif ($periodo === 'mesant') {
            $w .= " AND nm_control.cot_fdesde >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
                    AND nm_control.cot_fdesde <  DATE_FORMAT(NOW(), '%Y-%m-01') ";
        } elseif ($periodo === 'anio') {
            $w .= " AND YEAR(nm_control.cot_fdesde) = YEAR(NOW()) ";
        } elseif (in_array($periodo, ['3m', '6m', '12m', '24m'])) {
            $meses = (int) str_replace('m', '', $periodo);
            $w .= " AND nm_control.cot_fdesde >= DATE_SUB(NOW(), INTERVAL ? MONTH) "; $b[] = $meses;
        }

        if ($request->filled('emp_ced') && is_numeric($request->emp_ced)) {
            $w .= " AND nm_movhist.emp_ced = ? "; $b[] = intval($request->emp_ced);
        }

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

    /**
     * Mismo WHERE pero desplazado al período inmediatamente anterior,
     * para calcular la variación porcentual de los KPIs.
     */
    private function buildWhereAnterior(Request $request, array &$b): string
    {
        $periodo = $request->get('periodo', 'mes');
        $w = " WHERE 1=1 ";

        if ($periodo === 'hoy') {
            $w .= " AND DATE(nm_control.cot_fdesde) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) ";
        } elseif ($periodo === 'semana') {
            $w .= " AND YEARWEEK(nm_control.cot_fdesde, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL 1 WEEK), 1) ";
        } elseif ($periodo === 'mes' || $periodo === 'mesant') {
            $resta = $periodo === 'mes' ? 1 : 2;
            $w .= " AND nm_control.cot_fdesde >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL $resta MONTH), '%Y-%m-01')
                    AND nm_control.cot_fdesde <  DATE_FORMAT(DATE_SUB(NOW(), INTERVAL " . ($resta - 1) . " MONTH), '%Y-%m-01') ";
        } elseif ($periodo === 'anio') {
            $w .= " AND YEAR(nm_control.cot_fdesde) = YEAR(NOW()) - 1 ";
        } elseif (in_array($periodo, ['3m', '6m', '12m', '24m'])) {
            $meses = (int) str_replace('m', '', $periodo);
            $w .= " AND nm_control.cot_fdesde >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                    AND nm_control.cot_fdesde <  DATE_SUB(NOW(), INTERVAL ? MONTH) ";
            $b[] = $meses * 2; $b[] = $meses;
        } else {
            // 'custom' u otro: sin comparativo
            return " WHERE 1=0 ";
        }

        if ($request->filled('emp_ced') && is_numeric($request->emp_ced)) {
            $w .= " AND nm_movhist.emp_ced = ? "; $b[] = intval($request->emp_ced);
        }

        return $w;
    }

    private function variacion(float $actual, float $anterior): ?float
    {
        if ($anterior == 0.0) return null;
        return round((($actual - $anterior) / abs($anterior)) * 100, 1);
    }

    /* ==================================================================
     * INDEX
     * ================================================================== */
    public function index()
    {
        can('listar-dashboard-honorarios');
        return view('dashboardhon.index');
    }

    /* ==================================================================
     * KPIs — con comparativo contra período anterior (req. 1, 3, 4)
     * ================================================================== */
    public function kpis(Request $request)
    {
        $select = "
            SELECT
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS ded_usd,
                COUNT(DISTINCT nm_movhist.emp_ced)    AS total_med,
                COUNT(*)                              AS total_mov,
                COUNT(DISTINCT nm_movhist.mov_codcon) AS total_conceptos,
                COUNT(DISTINCT nm_movhist.mov_nummon) AS total_nominas,
                AVG(NULLIF(nm_movhismonext.mme_tasacambiorig, 0)) AS tasa_avg
        ";

        $b = []; $w = $this->buildWhere($request, $b);
        $act = DB::selectOne($select . $this->baseFrom() . $w, $b);

        $b2 = []; $w2 = $this->buildWhereAnterior($request, $b2);
        $ant = DB::selectOne($select . $this->baseFrom() . $w2, $b2);

        $asigBs = round($act->asig_bs ?? 0, 2);
        $dedBs  = round($act->ded_bs  ?? 0, 2);
        $netoBs = round($asigBs - $dedBs, 2);
        $med    = (int) ($act->total_med ?? 0);

        $asigBsAnt = round($ant->asig_bs ?? 0, 2);
        $netoBsAnt = round($asigBsAnt - ($ant->ded_bs ?? 0), 2);

        return response()->json([
            'asig_bs'         => $asigBs,
            'asig_usd'        => round($act->asig_usd ?? 0, 2),
            'ded_bs'          => $dedBs,
            'ded_usd'         => round($act->ded_usd ?? 0, 2),
            'neto_bs'         => $netoBs,
            'neto_usd'        => round(($act->asig_usd ?? 0) - ($act->ded_usd ?? 0), 2),
            'total_med'       => $med,
            'total_mov'       => (int) ($act->total_mov ?? 0),
            'total_conceptos' => (int) ($act->total_conceptos ?? 0),
            'total_nominas'   => (int) ($act->total_nominas ?? 0),
            'tasa_avg'        => round($act->tasa_avg ?? 0, 2),
            'prom_por_med'    => $med > 0 ? round($netoBs / $med, 2) : 0,
            'var_asig'        => $this->variacion($asigBs, $asigBsAnt),
            'var_neto'        => $this->variacion($netoBs, $netoBsAnt),
            'var_med'         => $this->variacion($med, (float) ($ant->total_med ?? 0)),
            'var_tasa'        => $this->variacion(
                                    round($act->tasa_avg ?? 0, 2),
                                    round($ant->tasa_avg ?? 0, 2)
                                 ),
        ]);
    }

    /* ==================================================================
     * Sparklines — serie mensual compacta para las tarjetas KPI
     * ================================================================== */
    public function sparklines(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m') AS k,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                COUNT(DISTINCT nm_movhist.emp_ced) AS med,
                AVG(NULLIF(nm_movhismonext.mme_tasacambiorig,0)) AS tasa
            " . $this->baseFrom() . $w . "
            GROUP BY k ORDER BY k ASC
        ", $b);

        return response()->json([
            'asig' => array_map(fn($r) => round($r->asig_bs, 2), $rows),
            'neto' => array_map(fn($r) => round($r->asig_bs - $r->ded_bs, 2), $rows),
            'med'  => array_map(fn($r) => (int) $r->med, $rows),
            'tasa' => array_map(fn($r) => round($r->tasa ?? 0, 2), $rows),
        ]);
    }

    /* ==================================================================
     * Evolución mensual — métrica conmutable (req. 6: ventana 24 meses)
     * ================================================================== */
    public function evolucion(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_control.cot_fdesde,'%Y-%m')  AS mes_key,
                DATE_FORMAT(nm_control.cot_fdesde,'%b %Y')  AS mes_label,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS ded_usd,
                COUNT(DISTINCT nm_movhist.emp_ced)          AS med,
                COUNT(*)                                    AS mov,
                AVG(NULLIF(nm_movhismonext.mme_tasacambiorig,0)) AS tasa_avg
            " . $this->baseFrom() . $w . "
            GROUP BY mes_key, mes_label
            ORDER BY mes_key ASC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ==================================================================
     * Totales por nómina (req. 1)
     * ================================================================== */
    public function totalesNomina(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_movhist.mov_nummon                                  AS nomina,
                DATE_FORMAT(nm_control.cot_fdesde,'%d/%m/%Y')          AS fdesde,
                DATE_FORMAT(nm_control.cot_fhasta,'%d/%m/%Y')          AS fhasta,
                DATE_FORMAT(nm_control.cot_fdesde,'%Y%m%d')            AS fecha_ord,
                COUNT(DISTINCT nm_movhist.emp_ced)                     AS medicos,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS ded_usd,
                AVG(NULLIF(nm_movhismonext.mme_tasacambiorig,0))       AS tasa_avg
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.mov_nummon, fdesde, fhasta, fecha_ord
            ORDER BY fecha_ord DESC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ==================================================================
     * Distribución por tipo de atención (HOSP / CEXT / AMBU / EMER)
     * ================================================================== */
    public function distribucionTipo(Request $request)
    {
        $b = []; $w = $this->buildWhereHon($request, $b);

        // Se agrupa por la columna cruda (aprovecha índice) y la descripción se
        // resuelve después sobre las 4 filas agregadas, no sobre las 327k.
        $rows = DB::select("
            SELECT
                COALESCE(tipodocumento.`desc`, agg.tipo_documento) AS tipo,
                agg.cantidad,
                agg.total_bs,
                agg.pagado_bs
            FROM (
                SELECT
                    nm_honpacientedet.tipo_documento,
                    COUNT(*)                         AS cantidad,
                    SUM(nm_honpacientedet.honorario) AS total_bs,
                    SUM(nm_honpacientedet.pagado)    AS pagado_bs
                FROM nm_honpacientedet
                $w
                GROUP BY nm_honpacientedet.tipo_documento
            ) agg
            LEFT JOIN tipodocumento ON tipodocumento.tipodoc = agg.tipo_documento
            ORDER BY tipodocumento.orden ASC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /**
     * WHERE para consultas que parten de nm_honpacientedet.
     *
     * El filtro de fecha se resuelve en una subconsulta sobre nm_controlnomcls
     * (160 filas) en lugar de unir nm_control directamente: así MySQL puede usar
     * el índice de la FK en lugar de escanear las 327k filas de la tabla de
     * pacientes. Sin esto la consulta de 24 meses tardaba ~3 s.
     */
    private function buildWhereHon(Request $request, array &$b): string
    {
        $w = " WHERE nm_honpacientedet.deleted_at IS NULL
               AND nm_honpacientedet.nm_controlnomcls_id IN (
                   SELECT ncl.id
                   FROM nm_controlnomcls ncl
                   INNER JOIN nm_control ctl ON ctl.id = ncl.nm_control_id
                   WHERE 1=1 ";

        $periodo = $request->get('periodo', 'mes');

        if ($periodo === 'custom') {
            if ($request->filled('fecha_desde')) {
                $w .= " AND ctl.cot_fdesde >= ? "; $b[] = $request->fecha_desde;
            }
            if ($request->filled('fecha_hasta')) {
                $w .= " AND ctl.cot_fhasta <= ? "; $b[] = $request->fecha_hasta;
            }
        } elseif ($periodo === 'hoy') {
            $w .= " AND DATE(ctl.cot_fdesde) = CURDATE() ";
        } elseif ($periodo === 'semana') {
            $w .= " AND YEARWEEK(ctl.cot_fdesde, 1) = YEARWEEK(NOW(), 1) ";
        } elseif ($periodo === 'mes') {
            $w .= " AND YEAR(ctl.cot_fdesde)=YEAR(NOW()) AND MONTH(ctl.cot_fdesde)=MONTH(NOW()) ";
        } elseif ($periodo === 'mesant') {
            $w .= " AND ctl.cot_fdesde >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
                    AND ctl.cot_fdesde <  DATE_FORMAT(NOW(), '%Y-%m-01') ";
        } elseif ($periodo === 'anio') {
            $w .= " AND YEAR(ctl.cot_fdesde) = YEAR(NOW()) ";
        } elseif (in_array($periodo, ['3m','6m','12m','24m'])) {
            $meses = (int) str_replace('m', '', $periodo);
            $w .= " AND ctl.cot_fdesde >= DATE_SUB(NOW(), INTERVAL ? MONTH) "; $b[] = $meses;
        }

        $w .= " ) ";

        if ($request->filled('emp_ced') && is_numeric($request->emp_ced)) {
            $w .= " AND nm_honpacientedet.emp_ced = ? "; $b[] = intval($request->emp_ced);
        }

        return $w;
    }

    /* ==================================================================
     * Top médicos (req. 10) — orden alfabético disponible vía DataTables
     * ================================================================== */
    public function topMedicos(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_movhist.emp_ced,
                TRIM(nm_empleados.emp_nom)  AS emp_nom,
                TRIM(nm_empleados.emp_ape)  AS emp_ape,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS asig_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='D' THEN nm_movhismonext.mme_montomone ELSE 0 END) AS ded_bs,
                SUM(CASE WHEN nm_movhist.mov_tipocon='A' THEN nm_movhismonext.mme_montodl   ELSE 0 END) AS asig_usd
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.emp_ced, emp_nom, emp_ape
            ORDER BY asig_bs DESC
            LIMIT 10
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ==================================================================
     * Ranking de conceptos — con ficha de datos generales (req. 8)
     * ================================================================== */
    public function rankingConceptos(Request $request)
    {
        $b = []; $w = $this->buildWhere($request, $b);

        $rows = DB::select("
            SELECT
                nm_movhist.mov_codcon                      AS cod,
                TRIM(nm_conceptos.con_desc)                AS concepto,
                nm_movhist.mov_tipocon                     AS tipo,
                COUNT(*)                                   AS frecuencia,
                COUNT(DISTINCT nm_movhist.emp_ced)         AS medicos,
                COUNT(DISTINCT nm_movhist.mov_nummon)      AS nominas,
                SUM(nm_movhismonext.mme_montomone)         AS total_bs,
                SUM(nm_movhismonext.mme_montodl)           AS total_usd,
                AVG(nm_movhismonext.mme_montomone)         AS prom_bs,
                MIN(nm_movhismonext.mme_montomone)         AS min_bs,
                MAX(nm_movhismonext.mme_montomone)         AS max_bs
            " . $this->baseFrom() . $w . "
            GROUP BY nm_movhist.mov_codcon, concepto, nm_movhist.mov_tipocon
            ORDER BY total_bs DESC
        ", $b);

        return response()->json(['data' => $rows]);
    }

    /* ==================================================================
     * Relación de pacientes (req. 5)
     *
     * Se devuelven las últimas 500 atenciones ordenadas por fecha (columna
     * indexada). Ordenar por el nombre concatenado del médico obligaba a
     * MySQL a hacer filesort sobre las 327k filas; el orden alfabético se
     * aplica en el cliente sobre el conjunto ya acotado.
     * ================================================================== */
    public function pacientes(Request $request)
    {
        $b = []; $w = $this->buildWhereHon($request, $b);

        // tipodocumento NO se une aquí: MySQL resuelve ese LEFT JOIN sobre las
        // 327k filas antes de aplicar el LIMIT, lo que costaba ~1,5 s. Como son
        // solo 4 tipos, la descripción se mapea en PHP.
        $rows = DB::select("
            SELECT
                DATE_FORMAT(nm_honpacientedet.fecha_fact,'%d/%m/%Y')  AS fecha,
                DATE_FORMAT(nm_honpacientedet.fecha_fact,'%Y%m%d')    AS fecha_ord,
                nm_honpacientedet.factura,
                nm_honpacientedet.tipo_documento                      AS tipo_doc,
                nm_honpacientedet.emp_ced,
                TRIM(CONCAT(nm_empleados.emp_nom,' ',nm_empleados.emp_ape)) AS medico,
                TRIM(nm_honpacientedet.nom_paciente)                  AS paciente,
                TRIM(nm_honpacientedet.concepto)                      AS concepto,
                nm_honpacientedet.honorario,
                nm_honpacientedet.pagado,
                nm_honpacientedet.dscto,
                nm_honpacientedet.tasa_cambio,
                nm_honpacientedet.monto_otra_moneda                   AS monto_usd
            FROM nm_honpacientedet
            LEFT JOIN nm_empleados ON nm_empleados.emp_ced = nm_honpacientedet.emp_ced
            $w
            ORDER BY nm_honpacientedet.fecha_fact DESC
            LIMIT 500
        ", $b);

        $tipos = DB::table('tipodocumento')->pluck('desc', 'tipodoc');
        foreach ($rows as $r) {
            $r->tipo = $tipos[$r->tipo_doc] ?? $r->tipo_doc;
            unset($r->tipo_doc);
        }

        return response()->json(['data' => $rows]);
    }

    /* ==================================================================
     * Filtros
     * ================================================================== */
    public function filtroMedicos()
    {
        $rows = DB::select("
            SELECT DISTINCT
                nm_honpacientedet.emp_ced,
                TRIM(CONCAT(nm_empleados.emp_ape,' ',nm_empleados.emp_nom)) AS nombre
            FROM nm_honpacientedet
            INNER JOIN nm_empleados ON nm_empleados.emp_ced = nm_honpacientedet.emp_ced
            ORDER BY nombre ASC
        ");
        return response()->json($rows);
    }

    public function filtroConceptos()
    {
        $rows = DB::select("
            SELECT DISTINCT
                nm_movhist.mov_codcon        AS cod,
                TRIM(nm_conceptos.con_desc)  AS descripcion,
                nm_movhist.mov_tipocon       AS tipo
            FROM nm_movhist
            INNER JOIN nm_conceptos ON nm_conceptos.con_cod = nm_movhist.mov_codcon
            INNER JOIN (SELECT DISTINCT emp_ced FROM nm_honpacientedet) med
                    ON med.emp_ced = nm_movhist.emp_ced
            ORDER BY nm_movhist.mov_tipocon, descripcion ASC
        ");
        return response()->json($rows);
    }
}
