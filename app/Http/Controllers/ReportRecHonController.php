<?php

namespace App\Http\Controllers;

use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Nm_MovHist;
use App\Models\nmControl;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Routing\Route;

class ReportRecHonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-recibo-honorarios');
        $nominaPeriodos = Nm_MovHist::periodosnompersona("");
        $aux_mesanno = mesanno(date("Y") . date("m"));
        //dd($nominaPeriodos);

        return view('reportrechon.index', compact('nominaPeriodos','aux_mesanno'));
    }

    /**
     * Constancia de ingresos por honorarios profesionales (requerimiento 11).
     *
     * La sirven dos rutas: reportrechongen, donde el personal administrativo la
     * emite para cualquier médico, y reportrechon, donde cada médico emite la
     * suya. La diferencia está sólo en de dónde sale la cédula.
     *
     * MONTO — verificado contra el modelo aprobado por la clínica
     *   Se promedian las asignaciones (mov_tipocon A, O o F) de nm_movhist en el
     *   rango de fechas, entre la cantidad de meses calendario que abarca. Es la
     *   misma cifra que el dashboard muestra como "Honorarios Profesionales".
     *   El resultado se trunca a bolívares enteros, no se redondea: el documento
     *   aprobado dice "CON CERO CÉNTIMOS", y es el criterio que ya sigue el
     *   recibo de honorarios por venir así de Visual FoxPro.
     *
     *   Contraste con la constancia aprobada de la Dra. Albarracín (13.490.646),
     *   01/04/2026 al 30/06/2026: Bs 3.186.485,47 / 3 = 1.062.161,82 → 1.062.161.
     */
    public function constanciaHonorarios(Request $request)
    {
        ini_set('memory_limit', '256M');

        $aux_cedula = $request->filled('emp_ced')
            ? $request->emp_ced
            : Usuario::findOrFail(auth()->id())->usuario;

        // La pantalla envía mes y año ("2026-01"). Se normaliza igual en el
        // servidor por si llegara un valor con día: la constancia certifica un
        // promedio de meses completos y no debe calcularse sobre un mes partido.
        $desde = $this->primerDiaDelMes($request->fecha_desde);
        $hasta = $this->ultimoDiaDelMes($request->fecha_hasta);

        if (!$desde || !$hasta) {
            return response('Debe indicar el período de la constancia (mes y año).', 422);
        }
        if ($desde > $hasta) {
            return response('El mes Desde no puede ser posterior al mes Hasta.', 422);
        }

        $medico = DB::table('nm_empleados')
            ->select('id', 'emp_nac', 'emp_ced', 'emp_sexo', 'emp_ape', 'emp_nom', 'emp_fecing')
            ->where('emp_ced', $aux_cedula)
            ->first();

        if (!$medico) {
            return response('No se encontró el médico con cédula ' . e($aux_cedula) . '.', 404);
        }

        // Asignaciones del rango. Mismo criterio que el recibo y el dashboard.
        $total = (float) DB::selectOne("
            SELECT SUM(CASE WHEN nm_movhist.mov_tipocon IN ('A','O','F')
                            THEN nm_movhist.mov_monto ELSE 0 END) AS asig_bs
            FROM nm_movhist
            INNER JOIN nm_control ON nm_control.cot_numnom = nm_movhist.mov_nummon
            WHERE nm_movhist.emp_ced   = ?
              AND nm_control.cot_fdesde >= ?
              AND nm_control.cot_fhasta <= ?
        ", [$aux_cedula, $desde, $hasta])->asig_bs;

        if ($total <= 0) {
            return response('El médico no tiene honorarios registrados en el rango indicado.', 404);
        }

        $meses    = $this->mesesDelRango($desde, $hasta);
        $promedio = intval($total / $meses);   // truncado, ver nota de cabecera

        $especialidades = DB::table('nm_empleadoespecialidad as ee')
            ->join('nm_especialidad as e', 'e.id', '=', 'ee.esp_id')
            ->whereNull('ee.deleted_at')
            ->whereNull('e.deleted_at')
            ->where('ee.emp_id', $medico->id)
            ->orderBy('e.nombre')
            ->pluck('e.nombre')
            ->toArray();

        $empresa    = DB::table('empresa')->first();
        $nm_empresa = DB::table('nm_empresa')->where('emp_codh', $request->emp_codh ?: 1)->first()
                   ?: DB::table('nm_empresa')->first();

        $pdf = PDF::loadView('reportrechon.constanciahonorarios', [
            'medico'         => $medico,
            'especialidades' => $especialidades,
            'empresa'        => $empresa,
            'nm_empresa'     => $nm_empresa,
            'total'          => $total,
            'meses'          => $meses,
            'promedio'       => $promedio,
            'periodo_texto'  => $this->rangoEnMeses($desde, $hasta),
            'fecha_desde'    => $desde,
            'fecha_hasta'    => $hasta,
        ]);

        $apellido = ucwords(strtolower(explode(' ', strtoupper(trim($medico->emp_ape)))[0]));
        $nombre   = ucwords(strtolower(explode(' ', strtoupper(trim($medico->emp_nom)))[0]));
        $cedula   = str_pad($medico->emp_ced, 8, '0', STR_PAD_LEFT);

        return $pdf->stream("ConstanciaHon_{$cedula}_{$apellido}{$nombre}.pdf");
    }

    /**
     * Primer día del mes indicado. Acepta "2026-01" (input type=month) y
     * también "2026-01-15", que se lleva igual al día 1.
     */
    private function primerDiaDelMes(?string $valor): ?string
    {
        if (!preg_match('/^(\d{4})-(\d{2})/', trim((string) $valor), $m)) {
            return null;
        }
        if ($m[2] < '01' || $m[2] > '12') {
            return null;
        }

        return "$m[1]-$m[2]-01";
    }

    /** Último día del mes indicado: 28, 29, 30 o 31 según corresponda. */
    private function ultimoDiaDelMes(?string $valor): ?string
    {
        $primero = $this->primerDiaDelMes($valor);

        return $primero ? date('Y-m-t', strtotime($primero)) : null;
    }

    /** Meses calendario que abarca el rango, ambos extremos incluidos. */
    private function mesesDelRango(string $desde, string $hasta): int
    {
        $d = new \DateTime(date('Y-m-01', strtotime($desde)));
        $h = new \DateTime(date('Y-m-01', strtotime($hasta)));
        $dif = $d->diff($h);

        return ((int) $dif->format('%y') * 12) + (int) $dif->format('%m') + 1;
    }

    /** Texto del período tal como lo pide el modelo: "Abril a Junio". */
    private function rangoEnMeses(string $desde, string $hasta): string
    {
        $meses = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $mDesde = $meses[(int) date('n', strtotime($desde))];
        $mHasta = $meses[(int) date('n', strtotime($hasta))];
        $aDesde = date('Y', strtotime($desde));
        $aHasta = date('Y', strtotime($hasta));

        if ($mDesde === $mHasta && $aDesde === $aHasta) {
            return "$mDesde de $aDesde";
        }
        if ($aDesde === $aHasta) {
            return "$mDesde a $mHasta de $aDesde";
        }

        return "$mDesde de $aDesde a $mHasta de $aHasta";
    }

    public function reportdtefacpage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $datas = Dte::reportdtefac($request);
        return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '256M');
        //dd($request);
        $usuario = Usuario::findOrFail(auth()->id());
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $aux_cedula = $usuario->usuario;
            //$aux_cedula = "2450604";
        }

        $empresa = Empresa::orderBy('id')->get();
        $sql = "SELECT *
            FROM nm_empleados 
            WHERE emp_ced = $aux_cedula;";
        $datas = DB::select($sql);
        $sql = "SELECT nm_movnomtrab.*,nm_cargos.car_desc
            FROM nm_movnomtrab INNER JOIN nm_cargos
            ON nm_movnomtrab.mov_codcar=nm_cargos.car_cod 
            WHERE mov_ced = $aux_cedula
            AND mov_numnom = $request->mov_nummon;";
        $nm_movnomtrab = DB::select($sql);
        $sql = "SELECT *
        FROM nm_control 
        WHERE cot_numnom = $request->mov_nummon;";
        $nm_control = DB::select($sql);
        if(count($datas) > 0 and count($nm_movnomtrab) > 0){
            $nm_empleado = $datas[0];
            $nm_movnomtrab = $nm_movnomtrab[0];
            //$nm_control = $nm_control[0];
            $nm_control = nmControl::findOrFail($request->nmcontrol_id);
            $nm_movhists = Nm_MovHist::consultarecibo($request,$nm_empleado);
            $tasacamb = 0;
            foreach($nm_movhists as $nm_movhist){
                if($nm_movhist->mme_tasacambiorig > 0){
                    $tasacamb = $nm_movhist->mme_tasacambiorig;
                    break;
                }
                
            }
            //dd($nm_movhists);
        }
        if($datas){

            $sql = "SELECT nm_controlnomcls.ccl_nronomciclos
                        FROM nm_control INNER JOIN nm_controlnomcls
                        ON nm_control.id = nm_controlnomcls.nm_control_id
                        INNER JOIN nm_honpacientedet
                        ON nm_controlnomcls.id = nm_honpacientedet.nm_controlnomcls_id
                        LEFT JOIN tipodocumento
                        ON nm_honpacientedet.tipo_documento = tipodocumento.tipodoc
                        WHERE nm_control.id = $request->nmcontrol_id
                        AND nm_honpacientedet.emp_ced = $aux_cedula
                        GROUP BY nm_controlnomcls.ccl_nronomciclos;";
            $nronomciclos = DB::select($sql);
            // Convertir a array de números
            $valores = array_map(function($item) {
                return $item->ccl_nronomciclos;
            }, $nronomciclos);

            // Convertir a cadena separada por comas
            $nroNominaCiclos = implode(',', $valores);

            if(env('APP_DEBUG')){
                //return view('reportrechon.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportrechon.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request','tasacamb','nroNominaCiclos'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            // Convertimos todo a mayúsculas y eliminamos espacios extra
            $apellido = strtoupper(trim($nm_empleado->emp_ape));
            $nombre = strtoupper(trim($nm_empleado->emp_nom));

            $primer_apellido = explode(' ', $apellido)[0]; // GARCIA
            $primer_nombre = explode(' ', $nombre)[0];     // ANNA

            $primer_apellido = ucwords(strtolower($primer_apellido));
            $primer_nombre = ucwords(strtolower($primer_nombre));

            // Cedula con ceros a la izquierda (8 dígitos)
            $cedula_formateada = str_pad($nm_empleado->emp_ced, 8, '0', STR_PAD_LEFT); // 00504431

            // Concatenamos todo
            $nomach = 'RecHon_'  . $nm_movnomtrab->mov_numrec . '_' .  $cedula_formateada . '_' . $primer_apellido . $primer_nombre;
            return $pdf->stream($nomach . ".pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function relHonPdf(Request $request)
    {
        ini_set('memory_limit', '256M');
        //dd($request);
        $data = nmControl::generarPDFRelHon($request);
        if(!isset($data["pdf"])){
            return $data["mensaje"];
        }
        $pdf = $data["pdf"];
        $nomach = $data["nomach"];
        return $pdf->inline($nomach . '.pdf');
        /* dd($data);
        $data = json_decode($aux_valor, true);
        dd($data);
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $usuario = Usuario::findOrFail(auth()->id());
            $aux_cedula = $usuario->usuario;
            //$aux_cedula = "2450604";
        }
        $nm_control = nmControl::findOrFail($request->nmcontrol_id);
        $empresa = Empresa::orderBy('id')->get();
        $sql = "SELECT *
            FROM nm_empleados 
            WHERE emp_ced = $aux_cedula;";
        $datas = DB::select($sql);

        $sql = "SELECT nm_honpacientedet.*,tipodocumento.desc as tipdoc_desc,
                    nm_controlnomcls.ccl_nronomciclos
                    FROM nm_control INNER JOIN nm_controlnomcls
                    ON nm_control.id = nm_controlnomcls.nm_control_id
                    INNER JOIN nm_honpacientedet
                    ON nm_controlnomcls.id = nm_honpacientedet.nm_controlnomcls_id
                    LEFT JOIN tipodocumento
                    ON nm_honpacientedet.tipo_documento = tipodocumento.tipodoc
                    WHERE nm_control.id = $request->nmcontrol_id
                    AND nm_honpacientedet.emp_ced = $aux_cedula
                    ORDER BY tipodocumento.orden,factura;";
        $pacdets = DB::select($sql);        
        if(count($datas) > 0){
            $nm_empleado = $datas[0];
        }
        if($pacdets){
            $sql = "SELECT nm_controlnomcls.ccl_nronomciclos
                        FROM nm_control INNER JOIN nm_controlnomcls
                        ON nm_control.id = nm_controlnomcls.nm_control_id
                        INNER JOIN nm_honpacientedet
                        ON nm_controlnomcls.id = nm_honpacientedet.nm_controlnomcls_id
                        LEFT JOIN tipodocumento
                        ON nm_honpacientedet.tipo_documento = tipodocumento.tipodoc
                        WHERE nm_control.id = $request->nmcontrol_id
                        AND nm_honpacientedet.emp_ced = $aux_cedula
                        GROUP BY nm_controlnomcls.ccl_nronomciclos;";
            $nronomciclos = DB::select($sql);
            // Convertir a array de números
            $valores = array_map(function($item) {
                return $item->ccl_nronomciclos;
            }, $nronomciclos);

            // Convertir a cadena separada por comas
            $nroNominaCiclos = implode(',', $valores);


            // Convertimos todo a mayúsculas y eliminamos espacios extra
            $apellido = strtoupper(trim($nm_empleado->emp_ape));
            $nombre = strtoupper(trim($nm_empleado->emp_nom));

            $primer_apellido = explode(' ', $apellido)[0]; // GARCIA
            $primer_nombre = explode(' ', $nombre)[0];     // ANNA

            $primer_apellido = ucwords(strtolower($primer_apellido));
            $primer_nombre = ucwords(strtolower($primer_nombre));

            // Cedula con ceros a la izquierda (8 dígitos)
            $cedula_formateada = str_pad($nm_empleado->emp_ced, 8, '0', STR_PAD_LEFT); // 00504431

            // Concatenamos todo
            $nomach = 'RelPacHon_' . implode('_', $nm_control->nmcontrolnomclss->pluck('ccl_nronomciclos')->toArray()) . '_' . $cedula_formateada . '_' . $primer_apellido . $primer_nombre;

            $pdf = SnappyPdf::loadView('reportrechon.relhon', compact(
                'nm_control', 'nm_empleado', 'empresa', 'usuario', 'request', 'pacdets', 'nroNominaCiclos'
            ));
            $pdf->setPaper('a4', 'landscape');
            // Para descargar directamente
            return $pdf->inline($nomach . '.pdf');

        }else{
            dd('Ningún dato disponible en esta consulta.');
        }  */
    }
}
