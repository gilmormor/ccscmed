<?php

namespace App\Http\Controllers;

use App\Models\NmEmpleado;
use Illuminate\Http\Request;

class NmEmpleadoController extends Controller
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

    public function nmepleadobuscarpage(){
        //$datas = Cliente::clientesxUsuarioSQLTemp();
        $datas = NmEmpleado::consultaempleado();
        return datatables($datas)->toJson();
    }
    
}
