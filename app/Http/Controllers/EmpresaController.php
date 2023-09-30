<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarEmpresa;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-empresa');
        $datas = Empresa::orderBy('id')->get();
        return view('empresa.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-empresa');
        $sucursales = Sucursal::orderBy('id')->get();
        $aux_sta=1;
        return view('empresa.crear',compact('sucursales','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarEmpresa $request)
    {
        can('guardar-empresa');
        Empresa::create($request->all());
        return redirect('empresa')->with('mensaje','Empresa creada con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mostrar($id)
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
        can('editar-empresa');
        $data = Empresa::findOrFail($id);
        $sucursales = Sucursal::orderBy('id')->get();
        $aux_sta=2;
        return view('empresa.editar', compact('data','sucursales','aux_sta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarEmpresa $request, $id)
    {
        can('guardar-empresa');
        Empresa::findOrFail($id)->update($request->all());
        return redirect('empresa')->with('mensaje','Empresa actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
    }
}
