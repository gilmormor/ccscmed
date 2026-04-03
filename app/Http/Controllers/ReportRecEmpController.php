<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Nm_MovHist;
use App\Models\nmControl;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class ReportRecEmpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-recibo-empleados');
        $empresas = Nm_MovHist::empresas(null);
        //dd("prueba");
        //$nominaPeriodos = Nm_MovHist::periodosnompersona("");
        //$aux_mesanno = mesanno(date("Y") . date("m"));
        //dd($nominaPeriodos);

        return view('reportrecemp.index', compact('empresas'));
    }

    public function periodos(Request $request){
        $datas = Nm_MovHist::periodosnompersona($request);
        return datatables($datas)->toJson();
    }

    public function exportPdf(Request $request)
    {
        //dd($request);
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $usuario = Usuario::findOrFail(auth()->id());
            $aux_cedula = $usuario->usuario;
            //$aux_cedula = "2450604";
        }
        $nm_empresa = DB::table('nm_empresa')
            ->select('emp_codh', 'emp_nombre', 'emp_rif','logo')
            ->where('emp_codh', $request->emp_codh)
            ->first();

        $nm_movnomtrab = DB::table('nm_movnomtrab')
            ->select('mov_numnom', 'mov_numrec')
            ->where('emp_codh', $request->emp_codh)
            ->where('mov_ced', $aux_cedula)
            ->where('mov_numnom', $request->mov_nummon)
            ->first();

        $nm_control = DB::table('nm_control')
            ->select('cot_tipo','cot_fdesde', 'cot_fhasta')
            ->where('emp_codh', $request->emp_codh)
            ->where('cot_tipo', $request->cot_tipo)
            ->where('cot_numnom', $request->mov_nummon)
            ->first();


        $nm_movhists = DB::table('nm_movhist')
            ->join('nm_conceptos', 'nm_movhist.mov_codcon', '=', 'nm_conceptos.con_cod')
            ->select(
                'nm_movhist.*',
                DB::raw('TRIM(nm_conceptos.con_desc) as con_desc')
            )
            ->where('nm_movhist.emp_ced', $aux_cedula)
            ->where('nm_movhist.mov_nummon', $request->mov_nummon)
            ->where('nm_movhist.emp_codh', $request->emp_codh)
            ->get();

        $nm_tiponomina = DB::table('nm_tiponomina')
            ->select('tmo_cod', 'tmo_desc')
            ->where('emp_codh', $request->emp_codh)
            ->where('tmo_cod', $request->cot_tipo)
            ->first();
 
        $nm_cargos = DB::table('nm_cargos')
            ->select('car_desc', 'emp_codh')
            ->where('emp_codh', $request->emp_codh)
            ->where('car_cod', $request->mov_codcar)
            ->first();

        $nm_ubicacion = DB::table('nm_ubicacion')
            ->select('ubi_cod', 'ubi_desc')
            ->where('emp_codh', $request->emp_codh)
            ->where('ubi_cod', $request->mov_codubica)
            ->first();

        $nm_empleado = DB::table('nm_empleados')
            ->where('emp_ced', $aux_cedula)
            ->first();
        $nm_vacproc = null;
        if($request->cot_tipo == "V"){
            $nm_vacproc = DB::table('nm_vacproc')
                ->where('emp_codh', $request->emp_codh)
                ->where('vac_ced', $aux_cedula)
                ->where('vac_numrec', $nm_movnomtrab->mov_numrec)
                ->first();
        }
        
        if($nm_movhists){


            if(env('APP_DEBUG')){
                return view('reportrecemp.listado', compact('nm_control','nm_empleado','nm_empresa','nm_movhists','nm_movnomtrab','usuario','nm_cargos','nm_ubicacion','nm_tiponomina','request'));
            }
            
            //return view('reportrecemp.listado', compact('nm_control','nm_empleado','nm_empresa','nm_movhists','nm_movnomtrab','usuario','nm_cargos','nm_ubicacion','nm_tiponomina','request'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportrecemp.listado', compact('nm_control','nm_empleado','nm_empresa','nm_movhists','nm_movnomtrab','usuario','nm_cargos','nm_ubicacion','nm_tiponomina','request','nm_vacproc'));
            //$pdf = PDF::loadView('reportrecemp.listado', compact('nm_control','nm_empleado','nm_empresa','nm_movhists','nm_movnomtrab','usuario','nm_cargos','nm_ubicacion','nm_tiponomina','request'))->setPaper('a4', 'landscape');
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
            $nomach = 'RecEmp_'  . $nm_movnomtrab->mov_numrec . '_' .  $cedula_formateada . '_' . $primer_apellido . $primer_nombre;
            return $pdf->stream($nomach . ".pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function constanciaTrabajo(Request $request){
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $usuario = Usuario::findOrFail(auth()->id());
            $aux_cedula = $usuario->usuario;
            //$aux_cedula = "2450604";
        }
        $nm_empresa = DB::table('nm_empresa')
            ->select('emp_codh', 'emp_nombre', 'emp_rif','emp_nombrefirma','ciudad', 'emp_direc', 'emp_telf', 'logo', 'region', 'pais')
            ->where('emp_codh', $request->emp_codh)
            ->first();
        //dd($nm_empresa);
        $nm_movnomtrab = DB::table('nm_movnomtrab as m')
            ->join('nm_empleados as e', function ($join) {
                $join->on('m.mov_ced', '=', 'e.emp_ced')
                    ->on('m.emp_codh', '=', 'e.emp_codh')
                    ->where(function ($query) {
                        $query->whereNull('e.emp_fecegre')
                            ->orWhere('e.emp_fecegre', '');
                    });
            })
            ->join('nm_cargos as c', function ($join) {
                $join->on('m.mov_codcar', '=', 'c.car_cod')
                    ->on('m.emp_codh', '=', 'c.emp_codh'); // recomendado si es multiempresa
            })
            ->select(
                'm.mov_numnom',
                'm.mov_numrec',
                'm.mov_fecing',
                'm.mov_sueldo',
                'e.emp_ape',
                'e.emp_nom',
                'e.emp_rif',
                'c.car_desc',
                'e.emp_ced'
            )
            ->where('m.emp_codh', $request->emp_codh)
            ->where('m.mov_ced', $aux_cedula)
            ->where('m.mov_numnom', $request->mov_nummon)
            ->first();
        //dd($nm_movnomtrab);
        if(!$nm_movnomtrab){
            dd('Ningún dato disponible en esta consulta.');
        }else{
            $pdf = PDF::loadView('reportrecemp.constanciatrabajo', compact('nm_movnomtrab','nm_empresa','request'));
            //$pdf = PDF::loadView('reportrecemp.listado', compact('nm_control','nm_empleado','nm_empresa','nm_movhists','nm_movnomtrab','usuario','nm_cargos','nm_ubicacion','nm_tiponomina','request'))->setPaper('a4', 'landscape');
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            // Convertimos todo a mayúsculas y eliminamos espacios extra
            $apellido = strtoupper(trim($nm_movnomtrab->emp_ape));
            $nombre = strtoupper(trim($nm_movnomtrab->emp_nom));

            $primer_apellido = explode(' ', $apellido)[0]; // GARCIA
            $primer_nombre = explode(' ', $nombre)[0];     // ANNA

            $primer_apellido = ucwords(strtolower($primer_apellido));
            $primer_nombre = ucwords(strtolower($primer_nombre));

            // Cedula con ceros a la izquierda (8 dígitos)
            $cedula_formateada = str_pad($nm_movnomtrab->emp_rif, 8, '0', STR_PAD_LEFT); // 00504431

            // Concatenamos todo
            $nomach = 'ConstanciaTrab_'  .  $cedula_formateada . '_' . $primer_apellido . $primer_nombre;
            return $pdf->stream($nomach . ".pdf");
        }
    }
    public function relHonPdf(Request $request)
    {
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

            $pdf = SnappyPdf::loadView('reportrecemp.relhon', compact(
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
