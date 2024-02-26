<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function empleadobuscarpage(){
        $datas = consulta("");
        return datatables($datas)->toJson();

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

    public function buscarCedula(Request $request){
        if($request->ajax()){
            $datas = consulta($request);
            return $datas;
            //dd($respuesta);
        }
    }
}

function consulta($request){
    $cond_cedula = "true";
    if(isset($request->emp_ced) and $request->emp_ced != null and $request->emp_ced != ""){
        $cond_cedula = " nm_empleados.emp_ced = $request->emp_ced";
    }
    $sql = "SELECT emp_ced,concat(TRIM(emp_nom),' ',TRIM(emp_ape)) as emp_nomape
    FROM nm_empleados INNER JOIN nm_movnomtrab
    ON nm_empleados.emp_ced = nm_movnomtrab.mov_ced
    where $cond_cedula
    GROUP BY emp_ced;";
    return DB::select($sql);


}
