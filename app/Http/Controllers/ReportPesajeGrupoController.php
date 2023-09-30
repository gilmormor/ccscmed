<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use App\Models\Pesaje;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportPesajeGrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-reporte-pesaje-grupo');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $tablashtml['fechaServ'] = [
            'fecha1erDiaMes' => date("01/m/Y"),
            'fechaAct' => date("d/m/Y"),
            ];
        //dd($tablashtml);
        $selecmultprod = 1;
        return view('reportpesajegrupo.index', compact('tablashtml','selecmultprod'));

    }
    public function reportpesajegrupopage(Request $request){
        //dd($request);
        can('listar-reporte-pesaje-grupo');
        $request->statusSumPeriodo = 1;
        $datas = Pesaje::pesajeDet($request);

        //dd($datas);
        return datatables($datas)->toJson();
/*
        return datatables()
        ->eloquent(InvMov::stock($request,"producto.id"))
        ->toJson();*/
    }

    public function exportPdf(Request $request)
    {
        can('listar-reporte-pesaje-grupo');
        $request->statusSumPeriodo = 1;
        $datas = Pesaje::pesajeDet($request);

        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $totalesPeriodo = totalizarindexpub($request);
        //dd($totalesPeriodo);


        if($datas){
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $request->request->add(['sucursal_nombre' => $sucursal->nombre]);
            if(env('APP_DEBUG')){
                return view('reportpesajegrupo.listado', compact('datas','empresa','usuario','request','sucursal','totalesPeriodo'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportpesajegrupo.listado', compact('datas','empresa','usuario','request','sucursal','totalesPeriodo'));
            //return $pdf->download('cotizacion.pdf');
            //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
            return $pdf->stream("ReporteStockBodegaPicking.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        } 
    }
    public function totalizarindex(Request $request){
        /*
        $respuesta = array();
        $datas = Pesaje::pesajeDet($request);

        $aux_totalTara = 0;
        $aux_totalPesoBal = 0;
        $aux_totalPesoTotalProdBal = 0;
        $aux_totalPesoTotalNorma = 0;
        $aux_totalDifKg = 0;
        foreach ($datas as $data) {
            //$aux_totalkg += $data->stockkg;
            $aux_totalTara += $data->tara;
            $aux_totalPesoBal += $data->pesobaltotal;
            $aux_totalPesoTotalProdBal += $data->pesototalprodbal;
            $aux_totalPesoTotalNorma += $data->pesototalnorma;
            $aux_totalDifKg += $data->difkg;
    
        }
        $respuesta['subtotalTara'] = $aux_totalTara;
        $respuesta['subtotalPesoBal'] = $aux_totalPesoBal;
        $respuesta['subtotalPesoTotalProdBal'] = $aux_totalPesoTotalProdBal;
        $respuesta['subtotalPesoTotalNorma'] = $aux_totalPesoTotalNorma;
        $respuesta['subtotalDifKg'] = $aux_totalDifKg;

        return $respuesta;
        */
        return totalizarindexpub($request);
    }

}


function totalizarindexpub($request){
    $respuesta = array();
    $datas = Pesaje::pesajeDet($request);

    $aux_totalTara = 0;
    $aux_totalPesoBal = 0;
    $aux_totalPesoTotalProdBal = 0;
    $aux_totalPesoTotalNorma = 0;
    $aux_totalDifKg = 0;
    foreach ($datas as $data) {
        //$aux_totalkg += $data->stockkg;
        $aux_totalTara += $data->sumtara;
        $aux_totalPesoBal += $data->sumpesobaltotal;
        $aux_totalPesoTotalProdBal += $data->pesototalprodbal;
        $aux_totalPesoTotalNorma += $data->pesototalnorma;
        $aux_totalDifKg += $data->difkg;
    }
    $respuesta['subtotalTara'] = $aux_totalTara;
    $respuesta['subtotalPesoBal'] = $aux_totalPesoBal;
    $respuesta['subtotalPesoTotalProdBal'] = $aux_totalPesoTotalProdBal;
    $respuesta['subtotalPesoTotalNorma'] = $aux_totalPesoTotalNorma;
    $respuesta['subtotalDifKg'] = $aux_totalDifKg;

    return $respuesta;
}
