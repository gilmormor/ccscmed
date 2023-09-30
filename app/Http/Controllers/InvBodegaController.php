<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarInvBodega;
use App\Models\CategoriaProd;
use App\Models\CategoriaProdSuc;
use App\Models\DespachoOrd;
use App\Models\InvBodega;
use App\Models\InvBodegaProducto;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvBodegaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-invbodega');
        return view('invbodega.index');
    }

    public function invbodegapage(){
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        //Filtrando las categorias por sucursal, dependiendo de las sucursales asignadas al usuario logueado
        //******************* */
        $sucurcadena = implode(",", $sucurArray);

        $sql= "SELECT invbodega.id,invbodega.nombre,invbodega.desc,invbodega.sucursal_id,sucursal.nombre as nombre_suc
        FROM invbodega INNER JOIN sucursal
        ON invbodega.sucursal_id = sucursal.id and isnull(invbodega.deleted_at) and isnull(sucursal.deleted_at)
        WHERE invbodega.sucursal_id in ($sucurcadena);";

        $datas = DB::select($sql);  
        //dd($datas);       
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-invbodega');
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $categoriaprodsucs = CategoriaProdSuc::where('sucursal_id','=','1')
                            ->get();
        $categoriaprodsucs = CategoriaProd::categoriasxUsuario("-1");
        return view('invbodega.crear',compact('sucursales','categoriaprodsucs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarInvBodega $request)
    {
        can('guardar-invbodega');
        $request->request->add(['usuario_id' => auth()->id()]);
        $invbodega = InvBodega::create($request->all());
        $invbodega->categoriaprods()->sync($request->categoriaprod_id);
        InvBodegaProducto::crearBodegasPorCategoria($invbodega);
        return redirect('invbodega')->with('mensaje','Bodega creado con exito.');
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
        can('editar-invbodega');
        $data = InvBodega::findOrFail($id);
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $sucursales = Sucursal::orderBy('id')
                        ->whereIn('sucursal.id', $sucurArray)
                        ->get();
        $categoriaprodsucs = CategoriaProdSuc::where('sucursal_id','=',$data->sucursal_id)
                        ->get();
        $categoriaprodsucs = CategoriaProd::categoriasxUsuario($data->sucursal_id);
        //dd($categoriaprods[0]->categoriaprod->nombre);
        //dd($data->categoriaprods);
        //dd($data->categoriaprods->firstWhere('id', $categoriaprodsucs[0]->categoriaprod->id));
        return view('invbodega.editar', compact('data','sucursales','categoriaprodsucs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarInvBodega $request, $id)
    {
        $invbodega = InvBodega::findOrFail($id);
        $invbodega->update($request->all());
        $invbodega->categoriaprods()->sync($request->categoriaprod_id);
        InvBodegaProducto::crearBodegasPorCategoria($invbodega);
        return redirect('invbodega')->with('mensaje','Bodega actualizada con exito');

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

    public function obtbodegasxsucursal(Request $request){
        $categoriaprod = CategoriaProd::findOrFail($request->categoriaprod_id);
        $categoriaprodsucurArray=$categoriaprod->sucursales->pluck('id')->toArray();

        $array_excluirid = json_decode($request->array_excluirid);
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $datas = InvBodega::join('sucursal', 'invbodega.sucursal_id', '=', 'sucursal.id')
                            ->whereIn('invbodega.sucursal_id', $sucurArray)
                            ->whereIn('invbodega.sucursal_id', $categoriaprodsucurArray)
                            ->whereNotIn('invbodega.id', $array_excluirid)
                            ->select([
                                'invbodega.id',
                                'invbodega.nombre',
                                'invbodega.desc',
                                'invbodega.sucursal_id',
                                'sucursal.nombre'
                            ])
                            ->get();
        //dd($datas);
        //$datas = CategoriaProd::catxUsuCostoAnnoMes($request);
        return $datas; //response()->json($data)
    }

    public function buscarTipoBodegaOrdDesp(Request $request){
        $despachoord = DespachoOrd::findOrFail($request->id);
        $respuesta = array();
        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $respuesta["datas"] = InvBodega::join('sucursal', 'invbodega.sucursal_id', '=', 'sucursal.id')
                            ->where('invbodega.tipo','=',$request->tipobodega)
                            ->where('invbodega.activo','=',1)
                            ->whereIn('invbodega.sucursal_id', $sucurArray)
                            ->where('invbodega.sucursal_id',$despachoord->notaventa->sucursal_id)
                            ->select([
                                'invbodega.id',
                                'invbodega.nombre',
                                'invbodega.desc',
                                'invbodega.sucursal_id',
                                'sucursal.nombre as sucursal_nombre'
                            ])
                            ->get();
        $respuesta["id"] = $request->id;
        $respuesta["nfila"] = $request->nfila;
        $respuesta["tipobodega"] = $request->tipobodega;
        //dd($respuesta["datas"]);
        //dd($datas);
        //$datas = CategoriaProd::catxUsuCostoAnnoMes($request);
        return $respuesta; //response()->json($data)

    }
    
}
