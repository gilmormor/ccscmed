<?php

namespace App\Http\Controllers;

use App\Models\Dte;
use App\Models\Empresa;
use App\Models\Nm_MovHist;
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
        //dd($nominaPeriodos);

        return view('reportrechon.index', compact('nominaPeriodos'));
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
        $sql = "SELECT *
        FROM nm_movnomtrab 
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
}
