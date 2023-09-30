<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCliente;
use App\Models\Cliente;
use App\Models\ClienteDirec;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\Cotizacion;
use App\Models\FormaPago;
use App\Models\Giro;
use App\Models\PlazoPago;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    public function index()
    {
        can('listar-cliente');
        return view('cliente.index');
    }

    public function clientepage(){
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();

        return datatables()
            ->eloquent(Cliente::query()
            ->select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
            ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
            ->pluck('cliente_sucursal.cliente_id')->toArray())
            ->whereIn('cliente.id',$clientevendedorArray)
            )
            ->toJson();
    }

    public function clientebuscarpage(){
        //$datas = Cliente::clientesxUsuarioSQLTemp();
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        /*
        $datas = Cliente::clientesxUsuarioSQLTemp();
        return datatables($datas)->toJson();
        */
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cliente');
        $regiones = Region::orderBy('id')->get();
        $formapagos = FormaPago::orderBy('id')->get();
        $plazopagos = PlazoPago::orderBy('dias')->get();
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        //$vendedores = Vendedor::orderBy('id')->get();
        $sql = 'SELECT vendedor.id,vendedor.persona_id,concat(nombre, " " ,apellido) AS nombre
                FROM vendedor INNER JOIN persona
                ON vendedor.persona_id=persona.id';

        $vendedores = DB::select($sql);
        $giros = Giro::orderBy('id')->get();
        $provincias = Provincia::orderBy('id')->get();
        $comunas = Comuna::orderBy('id')->get();
        //dd($comunas);
        $aux_sta=1;
        return view('cliente.crear',compact('regiones','sucursales','formapagos','plazopagos','vendedores','giros','provincias','comunas','aux_sta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCliente $request)
    {
        //dd($request);
        can('guardar-cliente');
        //DD($request);
        $cliente = Cliente::create($request->all());
        $cliente->vendedores()->sync($request->vendedor_id);
        $cliente->sucursales()->sync($request->sucursalp_id);
        $clienteid = $cliente->id;
        if(isset($request->direcciondetalle)){
            $cont_direc = count($request->direcciondetalle);
            if($cont_direc>0){
                for ($i=0; $i < $cont_direc ; $i++){
                    if(is_null($request->direcciondetalle[$i])==false && is_null($request->region_id[$i])==false && is_null($request->provincia_id[$i])==false && is_null($request->comuna_id[$i])==false){
                        $clienteDireccion = new ClienteDirec();
                        $clienteDireccion->cliente_id = $clienteid;
                        $clienteDireccion->direccion = $request->direcciondetalle[$i];
                        $clienteDireccion->direcciondetalle = $request->direcciondetalle[$i];
                        $clienteDireccion->region_id = $request->region_id[$i];
                        $clienteDireccion->provincia_id = $request->provincia_id[$i];
                        $clienteDireccion->comuna_id = $request->comuna_id[$i];
                        $clienteDireccion->save();
    
                        /* ESTO ES PARA INSERTAR O ELIMINAR SUCURSALES EN DIRECCIONES
                        $idDireccion = $clienteDireccion->id;
                        if($request->sucursal_id[$i] == NULL){
                            $aux_arraySuc = [];
                        }else{
                            $aux_arraySuc = explode(",", $request->sucursal_id[$i]);
                        }
                        
                        $clientedirec = ClienteDirec::findOrFail($idDireccion);
                        $clientedirec->sucursals()->sync($aux_arraySuc);
                        */
                    }
                }
            }                
        }

        
        /*
        if(isset($array_clientedirec)){
            $cliente->clientedirecs()->createMany($array_clientedirec);
        }
        */
        return redirect('cliente')->with('mensaje','Cliente creado con exito');
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
        can('editar-cliente');
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clienteArray = Cliente::whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
                    ->pluck('cliente_sucursal.cliente_id')->toArray())
                    ->whereIn('cliente.id',$clientevendedorArray)
                    ->where('cliente.id','=',$id)
                    ->pluck('cliente.id')->toArray();
        //dd($clienteArray);
        if($clienteArray){
            $data = Cliente::findOrFail($id);
            //dd($data);
            
            $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
            $regiones = Region::orderBy('id')->get();
            //$provincias = Provincia::where('region_id',$data->region_id)->orderBy('id')->get();
            $provincias = Provincia::orderBy('id')->get();
            //$comunas = Comuna::where('provincia_id',$data->provincia_id)->orderBy('id')->get();
            $comunas = Comuna::orderBy('id')->get();
            //dd($regiones);
            $formapagos = FormaPago::orderBy('id')->get();
            $plazopagos = PlazoPago::orderBy('dias')->get();
            //$vendedores = Vendedor::orderBy('id')->get();
            $sql = 'SELECT vendedor.id,vendedor.persona_id,concat(nombre, " " ,apellido) AS nombre
                    FROM vendedor INNER JOIN persona
                    ON vendedor.persona_id=persona.id';

            $vendedores = DB::select($sql);
            $aux_codsuc = "           ";
            $clientedirecs = $data->clientedirecs()->select(['id as dir_id','direcciondetalle','region_id','provincia_id','comuna_id','formapago_id','plazopago_id','contactonombre','contactoemail','contactotelef','nombrefantasia','mostrarguiasfacturas','finanzascontacto','finanzanemail','finanzastelefono','observaciones'])->get();
            $sucursalclientedirecs = $data->clientedirecs()->join('sucursalclientedirec', 'clientedirec.id', '=', 'sucursalclientedirec.clientedirec_id')->select(['sucursalclientedirec.clientedirec_id as clientedirec_id','sucursalclientedirec.sucursal_id as sucursal_id'])->get();
            $aux_vecsuc = array();
            $aux_vecsuc1 = array();
            $i = 0;
            $j = 0;
            //dd($clientedirecs);
            //dd($sucursalclientedirecs);
            foreach ($clientedirecs as $clientedirec) {
                $j = 0;
                $aux_vecsuc1[$i][$j] = '';
                foreach ($sucursalclientedirecs as $sucursalclientedirec) {
                    if($clientedirec->dir_id == $sucursalclientedirec->clientedirec_id){
                        $aux_vecsuc1[$i][$j] = $sucursalclientedirec->sucursal_id;
                        $j++;
                    }
                    
                }
                
                if(count($aux_vecsuc1[$i]) == 0){
                    $aux_vecsuc[$i] = '';
                }else{
                    if($i==2){
                        //dd($aux_vecsuc1);
                    }
                    $aux_vecsuc[$i] = implode(',',$aux_vecsuc1[$i]);
                }
                $i++;
            }
            $giros = Giro::orderBy('id')->get();
            $aux_cont=(count($clientedirecs));
            $aux_sta=2;
            //dd($clientedirec);
            return view('cliente.editar', compact('data','sucursales','regiones','provincias','comunas','formapagos','plazopagos','clientedirecs','sucursalclientedirec','vendedores','aux_vecsuc','giros','aux_sta','aux_cont'));
        }else{
            return redirect('cliente')->with('mensaje','No tiene permiso para ver este cliente.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //ValidarCliente
    public function actualizar(ValidarCliente $request, $id)
    {
        //dd($request->mostrarguiasfacturas[$i]);
        /*
        $permisos = Permiso::whereHas('roles', function ($query) {
            $query->where('rol_id', session()->get('rol_id'));
            })->get()->pluck('slug')->toArray();
        $permiso = 'guardar-cliente';
        dd(!in_array($permiso, $permisos));
        dd($permisos);*/
        
        if(can('guardar-cliente',false) == true){
            $cliente = Cliente::findOrFail($id);
        
            $cliente->update($request->all());
            //dd($request);
            //dd($request);
            
            $cliente->vendedores()->sync($request->vendedor_id);
            $cliente->sucursales()->sync($request->sucursalp_id);
            if(isset($request->direccion_id)){
                $auxDirCli=ClienteDirec::where('cliente_id',$id)->whereNotIn('id', $request->direccion_id)->pluck('id')->toArray(); //->destroy();
                for ($i=0; $i < count($auxDirCli) ; $i++){
                    ClienteDirec::destroy($auxDirCli[$i]);
                }
                $cont_direc = count($request->direccion_id);
                if($cont_direc>0){
                    for ($i=0; $i < count($request->direccion_id) ; $i++){
                        if( $request->direccion_id[$i] == '0' ){
                            $clienteDireccion = new ClienteDirec;
                            $clienteDireccion->cliente_id = $id;
                            $clienteDireccion->direccion = $request->direcciondetalle[$i];
                            $clienteDireccion->direcciondetalle = $request->direcciondetalle[$i];
                            $clienteDireccion->region_id = $request->region_id[$i];
                            $clienteDireccion->provincia_id = $request->provincia_id[$i];
                            $clienteDireccion->comuna_id = $request->comuna_id[$i];
                            $clienteDireccion->save();
                        }else{
                            DB::table('clientedirec')->updateOrInsert(
                                ['id' => $request->direccion_id[$i], 'cliente_id' => $id],
                                [
                                    'direccion' => $request->direcciondetalle[$i],
                                    'direcciondetalle' => $request->direcciondetalle[$i],
                                    'region_id' => $request->region_id[$i],
                                    'provincia_id' => $request->provincia_id[$i],
                                    'comuna_id' => $request->comuna_id[$i],
                                ]
                            );
                        }
                        /* ESTO ES PARA INSERTAR O ELIMINAR SUCURSALES EN DIRECCIONES
                        $idDireccion = $request->direccion_id[$i]; 
                        if($request->sucursal_id[$i] == NULL){
                            $aux_arraySuc = [];
                        }else{
                            $aux_arraySuc = explode(",", $request->sucursal_id[$i]);
                        }
                        $clientedirec = ClienteDirec::findOrFail($idDireccion);
                        $clientedirec->sucursals()->sync($aux_arraySuc);
                        */
                    }
                }
        
            }

            return redirect('cliente')->with('mensaje','Cliente actualizado con exito!');
        }else{
            return redirect('cliente')->with('mensaje','No tiene permiso para editar!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request,$id)
    {
        if(can('eliminar-cliente',false)){
            if ($request->ajax()) {
                $data = Cliente::findOrFail($request->id);
                $aux_contRegistos = $data->cotizacion->count() + $data->notaventa->count(); // + $data->vendedores->count() + $data->sucursales->count();
                //dd($aux_contRegistos);
                if($aux_contRegistos > 0){
                    return response()->json(['mensaje' => 'cr']);
                }else{
                    if (Cliente::destroy($request->id)) {
                        //dd('entro');
                        //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                        $cliente = Cliente::withTrashed()->findOrFail($request->id);
                        $cliente->usuariodel_id = auth()->id();
                        $cliente->save();
                        //Eliminar Direcciones
                        $clientedirec = ClienteDirec::where('cliente_id', '=', $request->id);
                        ClienteDirec::where('cliente_id', $request->id)->update(['usuariodel_id' => auth()->id()]);
                        $clientedirecs = $clientedirec->get();
                        //Eliminar Sucursales de cada direccion
                        foreach ($clientedirecs as $clientedire) {
                            $clientedirec1 = ClienteDirec::findOrFail($clientedire->id);
                            $clientedirec1->sucursals()->sync([]);
                        }
                        $clientedirec->delete();
                        ClienteSucursal::where('cliente_id', $request->id)->update(['usuariodel_id' => auth()->id()]);
                        $clientesucursal = ClienteSucursal::where('cliente_id', '=', $request->id);
                        $clientesucursal->delete();
                        ClienteVendedor::where('cliente_id', $request->id)->update(['usuariodel_id' => auth()->id()]);
                        $clientevendedor = ClienteVendedor::where('cliente_id', '=', $request->id);
                        $clientevendedor->delete();
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

    public function eliminarClienteDirec(Request $request)
    {
        can('eliminar-cliente');
        if ($request->ajax()) {
            if (ClienteDirec::destroy($request->direccion_id)) {
                //Despues de eliminar Direccion, elimino las sucursales por direccion
                $clientedirec = ClienteDirec::withTrashed()->findOrFail($request->direccion_id);
                $clientedirec->sucursals()->sync([]);
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function buscarCli(Request $request){
        if($request->ajax()){
            //dd($request);
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            //dd($sucurArray);
            $clientedirecs = Cliente::where('cliente.rut', $request->rut)
                    ->leftjoin('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
                    ->leftjoin('clientebloqueado', function ($join) {
                        $join->on('cliente.id', '=', 'clientebloqueado.cliente_id')
                        ->whereNull('clientebloqueado.deleted_at');
                    })
                    ->leftjoin("cliente_vendedor","cliente.id","cliente_vendedor.cliente_id")
                    ->leftjoin("vendedor","cliente_vendedor.vendedor_id","vendedor.id")
                    ->leftjoin("persona","vendedor.persona_id","persona.id")
                    ->whereNull('clientebloqueado.deleted_at')
                    ->select([
                                'cliente.id',
                                'cliente.rut',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'cliente.contactonombre',
                                'cliente.formapago_id',
                                'cliente.plazopago_id',
                                'cliente.giro_id',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'clientedirec.id as direc_id',
                                'clientedirec.direcciondetalle',
                                'clientebloqueado.descripcion',
                                DB::raw('GROUP_CONCAT(DISTINCT concat(persona.nombre, " " ,persona.apellido)) AS vendedor_nombre')
                            ])
                    ->groupBy('cliente.id');
            //dd($clientedirecs->clientebloqueado->cliente_id);
            return response()->json($clientedirecs->get());
        }
    }
    //Buscar Cliente sin la sucursal
    public function buscarClisinsuc(Request $request){
        if($request->ajax()){
            //dd($request);
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $clientedirecs = Cliente::where('rut', $request->rut)
                    ->select([
                                'cliente.id',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'cliente.contactonombre',
                                'cliente.formapago_id',
                                'cliente.plazopago_id',
                                'cliente.giro_id',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                            ]);
            //dd($clientedirecs->get());
            return response()->json($clientedirecs->get());
        }
    }


    public function guardarclientetemp(Request $request){
        if($request->ajax()){
            //dd($request);
            $cliente = Cliente::create($request->all());
            $clienteid = $cliente->id; //Tomo el id de cliente
            $clienteDireccion = new ClienteDirec; //Crear el registro en direccion
            $clienteDireccion->cliente_id = $clienteid;
            $clienteDireccion->direcciondetalle = $request->direccion;
            $clienteDireccion->region_id = $request->regionp_id;
            $clienteDireccion->provincia_id = $request->provinciap_id;
            $clienteDireccion->comuna_id = $request->comunap_id;
            $clienteDireccion->save();
    
            $cliente->vendedores()->sync([
                0 => $request->vendedor_id
            ]);
            $cliente->sucursales()->sync([
                0 => $request->sucursal_id
            ]);
            //Actualizo tadoas las cotizaciones que tenga el cliente temporal asignado
            //campo cliente_id por el cliente recien creado
            /*
            $cotizacion = Cotizacion::findOrFail($request->cotizacion_id);
            $cotizacion->cliente_id = $cliente->id;
            $cotizacion->save();
            */
            $cotizacion = Cotizacion::findOrFail($request->cotizacion_id);
            Cotizacion::where('clientetemp_id','=',$cotizacion->clientetemp_id)
                            ->update(['cliente_id' => $cliente->id]);
            //********* */
            $data = Cliente::findOrFail($cliente->id);
            return response()->json($data->where('id',$cliente->id)->get());
            //return response()->json($clientedirecs->get());
        }
    }

    public function clientegiro(){
        $sql = 'SELECT *
            FROM clientegiro;';
        //where usuario_id='.auth()->id();
        //dd($sql);
        $clientegiros = DB::select($sql);
        $longitud = count($clientegiros);
        /*
        for ($i=0;$i<=$longitud;$i++){
            dd($clientegiros[$i]['cliente_id']);
        }*/
        //dd($clientegiros);
        foreach ($clientegiros as $clientegiro) {
            $cliente = Cliente::findOrFail($clientegiro->cliente_id);
            $cliente->giro_id=$clientegiro->giro_id;
            $cliente->save();
        }
    }

    public function buscarCliId(Request $request){
        if($request->ajax()){
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $clientedirecs = Cliente::where('cliente.rut', $request->rut)
                    ->leftjoin('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
                    ->leftjoin('clientebloqueado', function ($join) {
                        $join->on('cliente.id', '=', 'clientebloqueado.cliente_id')
                        ->whereNull('clientebloqueado.deleted_at');
                    })
                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
                    ->select([
                                'cliente.id',
                                'cliente.rut',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'cliente.contactonombre',
                                'cliente.formapago_id',
                                'cliente.plazopago_id',
                                'cliente.giro_id',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'clientedirec.id as direc_id',
                                'clientedirec.direcciondetalle',
                                'clientebloqueado.descripcion'
                            ]);
                
            return response()->json($clientedirecs->get());
        }
    }

    public function buscarClixId(Request $request){
        if($request->ajax()){
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $clientedirecs = Cliente::where('cliente.id', $request->id)
                    ->leftjoin('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
                    ->leftjoin('clientebloqueado', function ($join) {
                        $join->on('cliente.id', '=', 'clientebloqueado.cliente_id')
                        ->whereNull('clientebloqueado.deleted_at');
                    })
                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
                    ->select([
                                'cliente.id',
                                'cliente.rut',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'cliente.contactonombre',
                                'cliente.formapago_id',
                                'cliente.plazopago_id',
                                'cliente.giro_id',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'clientedirec.id as direc_id',
                                'clientedirec.direcciondetalle',
                                'clientebloqueado.descripcion'
                            ]);
            //dd($clientedirecs->get());
            return response()->json($clientedirecs->get());
        }
    }

    public function buscarmyCli(Request $request){
        if($request->ajax()){
            //dd($request);
            $user = Usuario::findOrFail(auth()->id());
            $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
            $counts = DB::select($sql);
            $vendedor_id = '0';
            if($counts[0]->contador>0){
                $vendedor_id=$user->persona->vendedor->id;
                $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            }else{
                $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
            }
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono'])
                ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                        ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
                ->pluck('cliente_sucursal.cliente_id')->toArray())
                ->whereIn('cliente.id',$clientevendedorArray)
                ->get();
            //dd($clientes);
            $clientedirecs = Cliente::where('cliente.id', $request->id)
                    ->leftjoin('clientedirec', 'cliente.id', '=', 'clientedirec.cliente_id')
                    ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
                    ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
                    ->select([
                                'cliente.id',
                                'cliente.rut',
                                'cliente.razonsocial',
                                'cliente.telefono',
                                'cliente.email',
                                'cliente.direccion',
                                'cliente.vendedor_id',
                                'cliente.contactonombre',
                                'cliente.formapago_id',
                                'cliente.plazopago_id',
                                'cliente.giro_id',
                                'cliente.regionp_id',
                                'cliente.provinciap_id',
                                'cliente.comunap_id',
                                'clientedirec.id as direc_id',
                                'clientedirec.direcciondetalle'
                            ]);
            //dd($clientedirecs->get());
            return response()->json($clientedirecs->get());
        }
    }

    public function sucursalesXcliente(Request $request){
        if($request->ajax()){
            $sucursales = Cliente::where('cliente.rut', $request->rut)
                    ->join('cliente_sucursal', 'cliente.id', '=', 'cliente_sucursal.cliente_id')
                    ->join('sucursal', 'cliente_sucursal.sucursal_id', '=', 'sucursal.id')
                    ->select([
                            'cliente.id',
                            'cliente.rut',
                            'sucursal.id as sucursar_id',
                            'sucursal.nombre'
                            ]);
            //dd($sucursales->get());
            return response()->json($sucursales->get());
        }
    }

    public function buscarCliRut(Request $request){
        if($request->ajax()){
            $respuesta = array();
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            //dd($sucurArray);
            $sucurcadena = implode(",", $sucurArray);

            $sql= "SELECT cliente.id,cliente.rut,cliente.razonsocial,cliente.telefono,cliente.email,
            cliente.direccion,cliente.vendedor_id,cliente.contactonombre,cliente.formapago_id,
            cliente.plazopago_id,cliente.giro_id,cliente.giro,cliente.regionp_id,cliente.provinciap_id,cliente.comunap_id,
            clientebloqueado.descripcion,comuna.nombre as comuna_nombre,provincia.nombre as provincia_nombre,
            formapago.descripcion as formapago_desc,plazopago.dias as plazopago_dias
            FROM cliente left JOIN clientebloqueado
            ON cliente.id=clientebloqueado.cliente_id and isnull(clientebloqueado.deleted_at)
            left join comuna
            ON cliente.comunap_id=comuna.id and isnull(comuna.deleted_at)
            left join provincia
            ON cliente.provinciap_id=provincia.id and isnull(provincia.deleted_at)
            INNER JOIN formapago
            ON  cliente.formapago_id = formapago.id and isnull(formapago.deleted_at)
            INNER JOIN plazopago
            ON  cliente.plazopago_id = plazopago.id and isnull(plazopago.deleted_at)
            WHERE cliente.rut='$request->rut'
            and isnull(cliente.deleted_at)
            and cliente.id in (select cliente_id from cliente_sucursal where sucursal_id in ($sucurcadena))";
            $cliente = DB::select($sql);
            //dd($cliente);
            $respuesta['cliente'] = $cliente;

            $sql= "SELECT sucursal.id,sucursal.nombre
            FROM cliente left JOIN cliente_sucursal
            ON cliente.id=cliente_sucursal.cliente_id and cliente_sucursal.sucursal_id in ($sucurcadena) and isnull(cliente_sucursal.deleted_at)
            INNER JOIN sucursal
            ON cliente_sucursal.sucursal_id=sucursal.id and isnull(sucursal.deleted_at)
            WHERE cliente.rut='$request->rut'
            and isnull(cliente.deleted_at)
            order by cliente_sucursal.sucursal_id";
            //dd($sql);

            $sucursales = DB::select($sql);
            $respuesta['sucursales'] = $sucursales;
            return $respuesta;
            //dd($respuesta);
        }
    }

    public function buscarClixVenRut(Request $request){
        if($request->ajax()){
            $respuesta = array();
            $user = Usuario::findOrFail(auth()->id());
            $sucurArray = $user->sucursales->pluck('id')->toArray();
            //dd($sucurArray);
            $sucurcadena = implode(",", $sucurArray);

            $aux_clientesvendedorCond = " true ";
            if(isset($user->persona->vendedor)){
                $vendedor_id=$user->persona->vendedor->id;
                $aux_clientesvendedorCond = "cliente.id in (SELECT cliente_id from cliente_vendedor where cliente_vendedor.vendedor_id = $vendedor_id)";
            }
            $sql= "SELECT cliente.id,cliente.rut,cliente.razonsocial,cliente.telefono,cliente.email,
            cliente.direccion,cliente.vendedor_id,cliente.contactonombre,cliente.formapago_id,
            cliente.plazopago_id,cliente.giro_id,cliente.giro,cliente.regionp_id,cliente.provinciap_id,cliente.comunap_id,
            clientebloqueado.descripcion
            FROM cliente left JOIN clientebloqueado
            ON cliente.id=clientebloqueado.cliente_id and isnull(cliente.deleted_at) and isnull(clientebloqueado.deleted_at)
            WHERE cliente.rut='$request->rut'
            and cliente.id in (select cliente_id from cliente_sucursal where sucursal_id in ($sucurcadena))
            and $aux_clientesvendedorCond;";
            $cliente = DB::select($sql);
            //dd($cliente);
            $respuesta['cliente'] = $cliente;

            $sql= "SELECT sucursal.id,sucursal.nombre
            FROM cliente left JOIN cliente_sucursal
            ON cliente.id=cliente_sucursal.cliente_id and cliente_sucursal.sucursal_id in ($sucurcadena) and isnull(cliente_sucursal.deleted_at)
            INNER JOIN sucursal
            ON cliente_sucursal.sucursal_id=sucursal.id and isnull(sucursal.deleted_at)
            WHERE cliente.rut='$request->rut'
            and isnull(cliente.deleted_at)
            order by cliente_sucursal.sucursal_id";

            $sucursales = DB::select($sql);
            $respuesta['sucursales'] = $sucursales;
            return $respuesta;
        }
    }

}
