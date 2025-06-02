<?php

namespace App\Http\Controllers;

use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Nm_MovHist;
use App\Models\nmControl;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

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

    public function reportdtefacpage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $datas = Dte::reportdtefac($request);
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
            $nm_control = $nm_control[0];
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

            if(env('APP_DEBUG')){
                //return view('reportrechon.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportrechon.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request','tasacamb'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("ReciboHonorarios.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }

    public function relHonPdf(Request $request)
    {
        //dd($request);
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $usuario = Usuario::findOrFail(auth()->id());
            $aux_cedula = $usuario->usuario;
            //$aux_cedula = "2450604";
        }
        $nm_control = nmControl::findOrFail($request->nmcontrol_id);
        $array_nronomcls = $nm_control->nmcontrolnomclss->pluck('ccl_nronomciclos')->toArray();
        $nronomcls = implode(',', $array_nronomcls);
        //dd($nronomcls);
        $empresa = Empresa::orderBy('id')->get();
        $sql = "SELECT *
            FROM nm_empleados 
            WHERE emp_ced = $aux_cedula;";
        $datas = DB::select($sql);

        $sql = "SELECT nm_honpacientedet.*,tipodocumento.desc as tipdoc_desc
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
        //dd($pacdets[0]);

        if(count($datas) > 0){
            $nm_empleado = $datas[0];
        }
        if($pacdets){

            if(env('APP_DEBUG')){
                //return view('reportrechon.relhon', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request'));
            }            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportrechon.relhon', compact('nm_control','nm_empleado','empresa','usuario','request','pacdets'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("RelacionHonorarios.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}
