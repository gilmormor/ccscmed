<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\InvMov;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class InvMovController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-movimiento-inventario');
        return view('invmov.index');
    }

    public function invmovpage(){
        $datas = consultaindex();
        return datatables($datas)->toJson();
/*
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        return datatables()
            ->eloquent(InvMov::query()
                        ->join('invmovmodulo','invmov.invmovmodulo_id','=','invmovmodulo.id')
                        ->whereIn('invmov.sucursal_id', $sucurArray)
                        ->select([
                            'invmov.id',
                            'invmov.desc',
                            'invmov.fechahora',
                            'invmovmodulo.nombre as invmovmodulo_nombre',
                            'idmovmod',
                            'invmovmodulo_id'
                        ])
            
            )
            ->toJson();
            */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportPdf(Request $request)
    {
        if(can('ver-pdf-movimiento-inventario',false)){
            $datas = InvMov::findOrFail($request->id);
            $empresa = Empresa::orderBy('id')->get();
            $usuario = Usuario::findOrFail(auth()->id());
            if($datas){
                if(env('APP_DEBUG')){
                    return view('invmov.listado', compact('datas','empresa','usuario','request'));
                }
                //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
                //$pdf = PDF::loadView('reportinvstock.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                $pdf = PDF::loadView('invmov.listado', compact('datas','empresa','usuario','request'));
                //return $pdf->download('cotizacion.pdf');
                //return $pdf->stream(str_pad($notaventa->id, 5, "0", STR_PAD_LEFT) .' - '. $notaventa->cliente->razonsocial . '.pdf');
                return $pdf->stream("ReporteInvMov.pdf");
            }else{
                dd('Ningún dato disponible en esta consulta.');
            }     
        }else{
            $pdf = PDF::loadView('generales.pdfmensajesinacceso');
            return $pdf->stream("mensajesinacceso.pdf");
        }
    }
}

function consultaindex(){
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    $sucurcadena = implode(",", $sucurArray);

    $sql = "SELECT invmov.id,invmov.desc,invmov.fechahora,invmovmodulo.nombre as invmovmodulo_nombre,
    idmovmod,invmovmodulo_id,invmov.created_at,invmov.updated_at
    FROM invmov INNER JOIN invmovmodulo
    ON invmov.invmovmodulo_id = invmovmodulo.id AND ISNULL(invmov.deleted_at) and isnull(invmovmodulo.deleted_at)
    WHERE invmov.sucursal_id in ($sucurcadena)
    AND isnull(invmov.staanul)
    ORDER BY invmov.ID DESC;";

    /*
    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = $user->sucursales->pluck('id')->toArray();
    return datatables()
        ->eloquent(InvMov::query()
                    ->join('invmovmodulo','invmov.invmovmodulo_id','=','invmovmodulo.id')
                    ->whereIn('invmov.sucursal_id', $sucurArray)
                    ->select([
                        'invmov.id',
                        'invmov.desc',
                        'invmov.fechahora',
                        'invmovmodulo.nombre as invmovmodulo_nombre',
                        'idmovmod',
                        'invmovmodulo_id'
                    ])
        
        )
        ->toJson();
    */

    return DB::select($sql);

}