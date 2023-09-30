<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\Comuna;
use App\Models\Giro;
use App\Models\GuiaDesp;
use App\Models\Producto;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;

class ReportGuiaDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-orden-despacho');

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaAct = date("d/m/Y");
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores();

        return view('reportguiadesp.index', compact('giros','areaproduccions','tipoentregas','fechaAct','tablashtml'));

    }
/*
    public function productobuscarpage(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpage(){
        $datas = Cliente::clientesxUsuarioSQLTemp();
        return datatables($datas)->toJson();
    }

    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        $datas = Cliente::clientesxUsuarioSQLTemp();
        return datatables($datas)->toJson();
    }
*/
    public function reportguiadesppage(Request $request){
        //can('reporte-guia_despacho');
        //dd('entro');
        //$datas = GuiaDesp::reporteguiadesp($request);
        $datas = GuiaDesp::reportguiadesppage($request);
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
}
