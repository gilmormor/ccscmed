<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarFormaPago;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class FormaPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-forma-de-pago');
        //$datas = FormaPago::orderBy('id')->get();
        return view('formapago.index');
    }

    public function formapagopage(){
        return datatables()
            ->eloquent(FormaPago::query())
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-forma-de-pago');
        return view('formapago.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarFormaPago $request)
    {
        can('guardar-forma-de-pago');
        FormaPago::create($request->all());
        return redirect('formapago')->with('mensaje','Forma de Pago creado con exito');
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
    public function editar($id)
    {
        can('editar-forma-de-pago');
        $data = FormaPago::findOrFail($id);
        return view('formapago.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarFormaPago $request, $id)
    {
        FormaPago::findOrFail($id)->update($request->all());
        return redirect('formapago')->with('mensaje','Forma Pago actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        /*
        if ($request->ajax()) {
            if (FormaPago::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        */

        if(can('eliminar-forma-de-pago',false)){
            if ($request->ajax()) {
                $data = FormaPago::findOrFail($request->id);
                $aux_contRegistos = $data->clientedirecs->count() + $data->cotizacions->count() + $data->notaventas->count() + $data->clientes->count() + $data->clientetemps->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (FormaPago::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $FormaPago = FormaPago::withTrashed()->findOrFail($request->id);
                        $FormaPago->usuariodel_id = auth()->id();
                        $FormaPago->save();
                        return response()->json(['mensaje' => 'ok']);
                    } else {
                        return response()->json(['mensaje' => 'ng']);
                    }    
                }
            } else {
                abort(404);
            }
    
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
