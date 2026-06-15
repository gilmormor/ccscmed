<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Nm_MovHist;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class ReportEnviarEmailConErrorGenController extends Controller
{
    public function index()
    {
        can('listar-correos-no-validos');

        //$nominaPeriodos = Nm_MovHist::periodosnompersona("");
        //dd($nominaPeriodos);

        return view('reportenviaremailconerrorgen.index');
    }


    public function exportPdf(Request $request)
    {

        $datos = CedularErrorEmail(2);
        //dd($datos);
        
        if($datos){
            $empresa = Empresa::orderBy('id')->get();

            if(env('APP_DEBUG')){
                //return view('reportenviaremailconerror.listado', compact('datos','empresa'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportenviaremailconerror.listado', compact('datos','empresa'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');

            return $pdf->stream("reportenviaremailconerror.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
}
