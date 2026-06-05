<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Nm_MovHist;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class DashboardLaboralController extends Controller
{
    public function index()
    {
        can('listar-dashboard-laboral');

        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = $usuario->usuario;
        $esEmpleado = false;
        $nm_empleado = null;

        if (is_numeric($aux_cedula)) {
            $nm_empleado = DB::table('nm_empleados')
                ->where('emp_ced', $aux_cedula)
                ->first();
            $esEmpleado = $nm_empleado ? true : false;
        }

        return view('dashboardlaboral.index', compact('usuario', 'nm_empleado', 'esEmpleado'));
    }

    /**
     * KPIs principales del empleado (AJAX)
     */
    public function kpis(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;

        if (!is_numeric($aux_cedula)) {
            return response()->json(['error' => 'Usuario no es empleado'], 403);
        }

        $anio = $request->anio ?? date('Y');

        // Sueldo último período
        $ultimoPeriodo = DB::selectOne("
            SELECT nc.cot_fdesde, nc.cot_fhasta, nc.cot_numnom, nc.id,
                   mn.mov_sueldo
            FROM nm_movnomtrab mn
            INNER JOIN nm_control nc ON mn.mov_numnom = nc.cot_numnom AND mn.emp_codh = nc.emp_codh
            WHERE mn.mov_ced = ?
            ORDER BY nc.cot_fdesde DESC
            LIMIT 1
        ", [$aux_cedula]);

        // Total honorarios cobrados en el año
        $totalHonorarios = DB::selectOne("
            SELECT COALESCE(SUM(hd.honorario), 0) AS total
            FROM nm_honpacientedet hd
            INNER JOIN nm_controlnomcls cls ON hd.nm_controlnomcls_id = cls.id
            INNER JOIN nm_control nc ON cls.nm_control_id = nc.id
            WHERE hd.emp_ced = ?
              AND hd.pagado = 1
              AND YEAR(nc.cot_fdesde) = ?
        ", [$aux_cedula, $anio]);

        // Total recibos de nómina disponibles
        $totalRecibos = DB::selectOne("
            SELECT COUNT(DISTINCT mov_nummon) AS total
            FROM nm_movhist
            WHERE emp_ced = ?
        ", [$aux_cedula]);

        // Crecimiento salarial: sueldo promedio año actual vs año anterior
        $sueldoAnioActual = DB::selectOne("
            SELECT COALESCE(AVG(mn.mov_sueldo), 0) AS promedio
            FROM nm_movnomtrab mn
            INNER JOIN nm_control nc ON mn.mov_numnom = nc.cot_numnom AND mn.emp_codh = nc.emp_codh
            WHERE mn.mov_ced = ? AND YEAR(nc.cot_fdesde) = ?
        ", [$aux_cedula, $anio]);

        $sueldoAnioAnterior = DB::selectOne("
            SELECT COALESCE(AVG(mn.mov_sueldo), 0) AS promedio
            FROM nm_movnomtrab mn
            INNER JOIN nm_control nc ON mn.mov_numnom = nc.cot_numnom AND mn.emp_codh = nc.emp_codh
            WHERE mn.mov_ced = ? AND YEAR(nc.cot_fdesde) = ?
        ", [$aux_cedula, $anio - 1]);

        $crecimiento = 0;
        if ($sueldoAnioAnterior->promedio > 0) {
            $crecimiento = round((($sueldoAnioActual->promedio - $sueldoAnioAnterior->promedio) / $sueldoAnioAnterior->promedio) * 100, 1);
        }

        return response()->json([
            'sueldo_actual'      => $ultimoPeriodo ? $ultimoPeriodo->mov_sueldo : 0,
            'periodo_desde'      => $ultimoPeriodo ? $ultimoPeriodo->cot_fdesde : null,
            'periodo_hasta'      => $ultimoPeriodo ? $ultimoPeriodo->cot_fhasta : null,
            'total_honorarios'   => $totalHonorarios->total ?? 0,
            'total_recibos'      => $totalRecibos->total ?? 0,
            'crecimiento'        => $crecimiento,
            'anio'               => $anio,
        ]);
    }

    /**
     * Evolución de ingresos últimos 12 meses (AJAX — para Chart.js)
     */
    public function evolucionIngresos(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;

        if (!is_numeric($aux_cedula)) {
            return response()->json(['error' => 'No aplica'], 403);
        }

        // Sueldo por período (últimos 12)
        $sueldos = DB::select("
            SELECT DATE_FORMAT(nc.cot_fdesde, '%b %Y') AS periodo,
                   nc.cot_fdesde AS fecha_orden,
                   mn.mov_sueldo AS monto
            FROM nm_movnomtrab mn
            INNER JOIN nm_control nc ON mn.mov_numnom = nc.cot_numnom AND mn.emp_codh = nc.emp_codh
            WHERE mn.mov_ced = ?
            ORDER BY nc.cot_fdesde DESC
            LIMIT 12
        ", [$aux_cedula]);

        // Honorarios por período (últimos 12 controles con honorarios)
        $honorarios = DB::select("
            SELECT DATE_FORMAT(nc.cot_fdesde, '%b %Y') AS periodo,
                   nc.cot_fdesde AS fecha_orden,
                   COALESCE(SUM(hd.honorario), 0) AS monto
            FROM nm_honpacientedet hd
            INNER JOIN nm_controlnomcls cls ON hd.nm_controlnomcls_id = cls.id
            INNER JOIN nm_control nc ON cls.nm_control_id = nc.id
            WHERE hd.emp_ced = ? AND hd.pagado = 1
            GROUP BY nc.id, nc.cot_fdesde
            ORDER BY nc.cot_fdesde DESC
            LIMIT 12
        ", [$aux_cedula]);

        // Invertir para orden cronológico ascendente
        $sueldos    = array_reverse($sueldos);
        $honorarios = array_reverse($honorarios);

        return response()->json([
            'sueldos'    => $sueldos,
            'honorarios' => $honorarios,
        ]);
    }

    /**
     * Composición del recibo: asignaciones vs deducciones del último período (AJAX)
     */
    public function composicionRecibo(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;

        if (!is_numeric($aux_cedula)) {
            return response()->json(['error' => 'No aplica'], 403);
        }

        // Último número de nómina del empleado
        $ultimoNom = DB::selectOne("
            SELECT mn.mov_nummon
            FROM nm_movnomtrab mn
            INNER JOIN nm_control nc ON mn.mov_numnom = nc.cot_numnom AND mn.emp_codh = nc.emp_codh
            WHERE mn.mov_ced = ?
            ORDER BY nc.cot_fdesde DESC
            LIMIT 1
        ", [$aux_cedula]);

        if (!$ultimoNom) {
            return response()->json(['asignaciones' => 0, 'deducciones' => 0, 'conceptos' => []]);
        }

        $mov_numnom = $ultimoNom->mov_numnom;

        $totales = DB::select("
            SELECT nc.con_asided, SUM(mh.mov_monto) AS total
            FROM nm_movhist mh
            INNER JOIN nm_conceptos nc ON nc.con_cod = mh.mov_codcon AND nc.gru_cod = mh.gru_cod
            WHERE mh.emp_ced = ? AND mh.mov_nummon = ?
            GROUP BY nc.con_asided
        ", [$aux_cedula, $mov_numnom]);

        $asignaciones = 0;
        $deducciones  = 0;
        foreach ($totales as $t) {
            if ($t->con_asided === 'A') $asignaciones = $t->total;
            if ($t->con_asided === 'D') $deducciones  = $t->total;
        }

        // Top conceptos para desglose
        $conceptos = DB::select("
            SELECT nc.con_desc, nc.con_asided, mh.mov_monto
            FROM nm_movhist mh
            INNER JOIN nm_conceptos nc ON nc.con_cod = mh.mov_codcon AND nc.gru_cod = mh.gru_cod
            WHERE mh.emp_ced = ? AND mh.mov_nummon = ?
            ORDER BY nc.con_asided, mh.mov_monto DESC
            LIMIT 20
        ", [$aux_cedula, $mov_numnom]);

        return response()->json([
            'asignaciones' => $asignaciones,
            'deducciones'  => $deducciones,
            'conceptos'    => $conceptos,
        ]);
    }

    /**
     * Historial de recibos de nómina del empleado (AJAX — DataTables)
     */
    public function historialNomina(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;

        if (!is_numeric($aux_cedula)) {
            return response()->json(['data' => []]);
        }

        $periodos = DB::select("
            SELECT nc.id, nc.cot_numnom, nc.cot_fdesde, nc.cot_fhasta,
                   DATE_FORMAT(nc.cot_fdesde, '%d/%m/%Y') AS fdesde,
                   DATE_FORMAT(nc.cot_fhasta, '%d/%m/%Y') AS fhasta,
                   mn.mov_sueldo
            FROM nm_movnomtrab mn
            INNER JOIN nm_control nc ON mn.mov_numnom = nc.cot_numnom AND mn.emp_codh = nc.emp_codh
            WHERE mn.mov_ced = ?
            ORDER BY nc.cot_fdesde DESC
        ", [$aux_cedula]);

        return response()->json(['data' => $periodos]);
    }

    /**
     * Historial de honorarios del empleado (AJAX — DataTables)
     */
    public function historialHonorarios(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;

        if (!is_numeric($aux_cedula)) {
            return response()->json(['data' => []]);
        }

        $honorarios = DB::select("
            SELECT nc.id AS nmcontrol_id,
                   nc.cot_numnom,
                   DATE_FORMAT(nc.cot_fdesde, '%d/%m/%Y') AS fdesde,
                   DATE_FORMAT(nc.cot_fhasta, '%d/%m/%Y') AS fhasta,
                   SUM(hd.honorario) AS total_honorarios,
                   MAX(hd.pagado) AS pagado
            FROM nm_honpacientedet hd
            INNER JOIN nm_controlnomcls cls ON hd.nm_controlnomcls_id = cls.id
            INNER JOIN nm_control nc ON cls.nm_control_id = nc.id
            WHERE hd.emp_ced = ?
            GROUP BY nc.id, nc.cot_numnom, nc.cot_fdesde, nc.cot_fhasta
            ORDER BY nc.cot_fdesde DESC
        ", [$aux_cedula]);

        return response()->json(['data' => $honorarios]);
    }

    /**
     * PDF Constancia de Trabajo
     */
    public function constanciaPdf(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());
        $aux_cedula = isset($request->emp_ced) ? $request->emp_ced : $usuario->usuario;

        if (!is_numeric($aux_cedula)) {
            abort(403, 'No autorizado');
        }

        $nm_empleado = DB::selectOne("
            SELECT e.*, c.car_desc, u.ubi_desc, tp.tmo_desc
            FROM nm_empleados e
            LEFT JOIN nm_movnomtrab mn ON mn.mov_ced = e.emp_ced AND mn.emp_codh = e.emp_codh
            LEFT JOIN nm_cargos c ON c.car_cod = mn.mov_codcar AND c.emp_codh = e.emp_codh
            LEFT JOIN nm_ubicacion u ON u.ubi_cod = mn.mov_codubica AND u.emp_codh = e.emp_codh
            LEFT JOIN nm_control nc ON nc.cot_numnom = mn.mov_numnom AND nc.emp_codh = e.emp_codh
            LEFT JOIN nm_tiponomina tp ON tp.tmo_cod = nc.cot_tipo AND tp.emp_codh = e.emp_codh
            WHERE e.emp_ced = ?
            ORDER BY nc.cot_fdesde DESC
            LIMIT 1
        ", [$aux_cedula]);

        if (!$nm_empleado) {
            abort(404, 'Empleado no encontrado');
        }

        $empresa = Empresa::orderBy('id')->first();
        $fecha_emision = now()->format('d/m/Y');

        $pdf = PDF::loadView('dashboardlaboral.constancia_pdf', compact(
            'nm_empleado', 'empresa', 'usuario', 'fecha_emision'
        ));

        $apellido = ucwords(strtolower(explode(' ', trim($nm_empleado->emp_ape))[0]));
        $nombre   = ucwords(strtolower(explode(' ', trim($nm_empleado->emp_nom))[0]));
        $cedula   = str_pad($nm_empleado->emp_ced, 8, '0', STR_PAD_LEFT);

        return $pdf->stream("Constancia_{$cedula}_{$apellido}{$nombre}.pdf");
    }
}
