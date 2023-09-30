<?php

namespace App\Http\Controllers;

use App\Models\AreaProduccion;
use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\ClienteVendedor;
use App\Models\Comuna;
use App\Models\DespachoOrd;
use App\Models\DespachoOrdRec;
use App\Models\DespachoSolAnul;
use App\Models\Giro;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use App\Models\TipoEntrega;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportSolDespController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('reporte-solicitud-de-despacho');
        $clientesArray = Cliente::clientesxUsuario();
        $clientes = $clientesArray['clientes'];
        $vendedor_id = $clientesArray['vendedor_id'];
        $sucurArray = $clientesArray['sucurArray'];

        $arrayvend = Vendedor::vendedores(); //Viene del modelo vendedores
        $clientevendedorArray = $arrayvend['clientevendedorArray'];

        $giros = Giro::orderBy('id')->get();
        $areaproduccions = AreaProduccion::orderBy('id')->get();
        $tipoentregas = TipoEntrega::orderBy('id')->get();
        $fechaServ = [
                    'fecha1erDiaMes' => date("01/m/Y"),
                    'fechaAct' => date("d/m/Y")
                    ];
        $tablashtml['comunas'] = Comuna::selectcomunas();
        $tablashtml['vendedores'] = Vendedor::selectvendedores(); 
        $user = Usuario::findOrFail(auth()->id());
        $tablashtml['sucurArray'] = $user->sucursales->pluck('id')->toArray(); //$clientesArray['sucurArray'];
        $tablashtml['sucursales'] = Sucursal::orderBy('id')->whereIn('sucursal.id', $tablashtml['sucurArray'])->get();          
        return view('reportsoldesp.index', compact('clientes','giros','areaproduccions','tipoentregas','fechaServ','tablashtml'));

    }

    public function reporte(Request $request){
        $respuesta = array();
        $respuesta['exito'] = false;
        $respuesta['mensaje'] = "Código no Existe";
        $respuesta['tabla'] = "";
    
        if($request->ajax()){
            $datas = consultasoldesp($request);
    
            $respuesta['tabla'] .= "<table id='tablacotizacion' name='tablacotizacion' class='table display AllDataTables table-hover table-condensed tablasconsOD' data-page-length='50'>
            <thead>
                <tr>
                    <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                    <th>Fecha</th>
                    <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                    <th>Razón Social</th>
                    <th class='tooltipsC' title='Orden de Compra'>OC</th>
                    <th class='tooltipsC' title='Nota de Venta'>NV</th>
                    <th>Comuna</th>
                    <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                    <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                    <th class='tooltipsC' title='Orden Despacho'>Despacho</th>
                </tr>
            </thead>
            <tbody>";
    
            $i = 0;
            foreach ($datas as $data) {
                $rut = number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 );
                if(empty($data->oc_file)){
                    $aux_enlaceoc = $data->oc_id;
                }else{
                    $aux_enlaceoc = "<a onclick='verpdf2(\"$data->oc_file\",2)'>$data->oc_id</a>";
                }
                $ruta_nuevoOrdDesp = route('crearord_despachoord', ['id' => $data->id]);
                $aprorddesp = "<i class='glyphicon glyphicon-floppy-save text-warning tooltipsC' title='Pendiente Aprobar'></i>";
                if($data->aprorddesp){
                    $fechaaprob = date('d-m-Y h:i:s A', strtotime($data->aprorddespfh));
                    $aprorddesp = "<i class='glyphicon glyphicon-floppy-save text-primary tooltipsC' title='Fecha: $fechaaprob'></i>";
                }
                $listadosoldesp = "";
                $ordesp = DespachoOrd::orderBy('id')
                        ->where('despachosol_id',$data->id);
                if($ordesp->count() > 0){
                    $aux_cont = $ordesp->count();
                    $listadosoldesp = "
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Orden Despacho: $aux_cont' onclick='genlistaOD($data->id)'>
                            <i class='glyphicon glyphicon-search'></i>
                        </a>";
                }
                //$despachoordrec = DespachoOrdRec::where('despachoord_id',$data->id)->get();
                //dd($despachoordrec);
                //dd($ordesp);

                /*$despachoord = DespachoOrd::findOrFail($data->id)
                                ->whereNull();*/
                $respuesta['tabla'] .= "
                <tr id='fila$i' name='fila$i' class='btn-accion-tabla tooltipsC'>
                    <td id='id$i' name='id$i'>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD($data->id,1)'>
                            $data->id
                        </a>
                    </td>
                    <td id='fechahora$i' name='fechahora$i' data-order='$data->fechahora'>" . date('d-m-Y', strtotime($data->fechahora)) . "</td>
                    <td id='fechaestdesp$i' name='fechaestdesp$i' data-order='$data->fechaestdesp'>" . date('d-m-Y', strtotime($data->fechaestdesp)) . "</td>
                    <td id='razonsocial$i' name='razonsocial$i'>$data->razonsocial</td>
                    <td id='oc_id$i' name='oc_id$i'>$aux_enlaceoc</td>
                    <td>
                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV($data->notaventa_id,1)'>
                            $data->notaventa_id
                        </a>
                    </td>
                    <td id='comuna$i' name='comuna$i'>$data->comunanombre</td>
                    <td style='text-align:right'>".
                        number_format($data->totalkilos, 2, ",", ".") .
                    "</td>
                    <td>
                        <i class='fa fa-fw $data->icono tooltipsC' title='$data->tipentnombre'></i>
                    </td>";
                    $despachosolanul = DespachoSolAnul::where('despachosol_id','=',$data->id)->get();
                    if (count($despachosolanul)>0){
                        $respuesta['tabla'] .= "<td><small class='label pull-left bg-red'>Anulado</small></td>";
                    }else{
                        $respuesta['tabla'] .= "
                        <td>
                            $aprorddesp | 
                            $listadosoldesp
                        </td>
                        ";
                    }
                    $respuesta['tabla'] .= "</tr>";
    
                //dd($data->contacto);
            }
    
            $respuesta['tabla'] .= "
            </tbody>
            </table>";
            return $respuesta;
        }
        
        return $respuesta;
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


function consultasoldesp($request){
    if(empty($request->vendedor_id)){
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $vendedorcond = "notaventa.vendedor_id=" . $vendedor_id ;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $sucurArray = $user->sucursales->pluck('id')->toArray();
        }else{
            $vendedorcond = " true ";
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
    }else{
        if(is_array($request->vendedor_id)){
            $aux_vendedorid = implode ( ',' , $request->vendedor_id);
        }else{
            $aux_vendedorid = $request->vendedor_id;
        }
        $vendedorcond = " notaventa.vendedor_id in ($aux_vendedorid) ";

        //$vendedorcond = "notaventa.vendedor_id='$request->vendedor_id'";
    }

    if(empty($request->fechad) or empty($request->fechah)){
        $aux_condFecha = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechad);
        $fechad = date_format($fecha, 'Y-m-d')." 00:00:00";
        $fecha = date_create_from_format('d/m/Y', $request->fechah);
        $fechah = date_format($fecha, 'Y-m-d')." 23:59:59";
        $aux_condFecha = "despachosol.fechahora>='$fechad' and despachosol.fechahora<='$fechah'";
    }
    if(empty($request->rut)){
        $aux_condrut = " true";
    }else{
        $aux_condrut = "cliente.rut='$request->rut'";
    }
    if(empty($request->oc_id)){
        $aux_condoc_id = " true";
    }else{
        $aux_condoc_id = "notaventa.oc_id='$request->oc_id'";
    }
    if(empty($request->giro_id)){
        $aux_condgiro_id = " true";
    }else{
        $aux_condgiro_id = "notaventa.giro_id='$request->giro_id'";
    }
    if(empty($request->areaproduccion_id)){
        $aux_condareaproduccion_id = " true";
    }else{
        $aux_condareaproduccion_id = "categoriaprod.areaproduccion_id='$request->areaproduccion_id'";
    }
    if(empty($request->tipoentrega_id)){
        $aux_condtipoentrega_id = " true";
    }else{
        $aux_condtipoentrega_id = "notaventa.tipoentrega_id='$request->tipoentrega_id'";
    }
    if(empty($request->notaventa_id)){
        $aux_condnotaventa_id = " true";
    }else{
        $aux_condnotaventa_id = "notaventa.id='$request->notaventa_id'";
    }
    $aux_sqlsumdespfact = 0;
    if(empty($request->status)){
        $aux_status = " true";
    }else{
        $aux_inNullSoldesp = "despachosol.id IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE isnull(despachosolanul.deleted_at))";
        $aux_notinNullSoldesp = "despachosol.id NOT IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE isnull(despachosolanul.deleted_at))";
        $aux_sqlsumdespfact = "SELECT cantdesp
                    FROM vista_sumorddespdetconfactura
                    WHERE despachosoldet_id=despachosoldet.id";
        switch ($request->status) {
            case '1':
                $aux_status = "if((if(isnull(($aux_sqlsumdespfact)),0,($aux_sqlsumdespfact))
                                ) < despachosoldet.cantsoldesp,TRUE,FALSE)
                                AND $aux_notinNullSoldesp";
                break;    
            case '2':
                $aux_status = "if((if(isnull(($aux_sqlsumdespfact)),0,($aux_sqlsumdespfact))
                                ) >= despachosoldet.cantsoldesp,TRUE,FALSE)
                                AND $aux_notinNullSoldesp
                                ";
                break;
            case '3':
                $aux_status = "despachosol.id IN (SELECT despachosolanul.despachosol_id FROM despachosolanul WHERE isnull(despachosolanul.deleted_at))";
                break;
        }
    }
/*
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true";
    }else{
        $aux_condcomuna_id = "notaventa.comunaentrega_id='$request->comuna_id'";
    }
*/
    if(empty($request->comuna_id)){
        $aux_condcomuna_id = " true ";
    }else{
        if(is_array($request->comuna_id)){
            $aux_comuna = implode ( ',' , $request->comuna_id);
        }else{
            $aux_comuna = $request->comuna_id;
        }
        $aux_condcomuna_id = " notaventa.comunaentrega_id in ($aux_comuna) ";
    }


    $aux_condaprobord = "true";
    
    if(empty($request->id)){
        $aux_condid = " true";
    }else{
        $aux_condid = "despachosol.id='$request->id'";
    }
    if(empty($request->fechaestdesp)){
        $aux_condfechaestdesp = " true";
    }else{
        $fecha = date_create_from_format('d/m/Y', $request->fechaestdesp);
        $fechad = date_format($fecha, 'Y-m-d');
        $aux_condfechaestdesp = "despachosol.fechaestdesp='$fechad'";
    }

    $user = Usuario::findOrFail(auth()->id());
    $sucurArray = implode ( ',' , $user->sucursales->pluck('id')->toArray());
    if(!isset($request->sucursal_id) or empty($request->sucursal_id)){
        //$aux_condsucursal_id = " true ";
        $aux_condsucursal_id = " despachosol.sucursal_id in ($sucurArray)";
    }else{
        if(is_array($request->sucursal_id)){
            $aux_sucursal = implode ( ',' , $request->sucursal_id);
        }else{
            $aux_sucursal = $request->sucursal_id;
        }
        $aux_condsucursal_id = " (despachosol.sucursal_id in ($aux_sucursal) and despachosol.sucursal_id in ($sucurArray))";
    }
 
    //$suma = DespachoSol::findOrFail(2)->despachosoldets->where('notaventadetalle_id',1);

    $sql = "SELECT despachosol.id,despachosol.fechahora,cliente.rut,cliente.razonsocial,notaventa.oc_id,notaventa.oc_file,
            comuna.nombre as comunanombre,
            despachosol.notaventa_id,despachosol.fechaestdesp,
            sum(despachosoldet.cantsoldesp * (notaventadetalle.totalkilos / notaventadetalle.cant)) AS totalkilos,
            sum(despachosoldet.cantsoldesp) AS totalcantsol,
            sum(($aux_sqlsumdespfact)) as totalcantdesp,
            despachosol.aprorddesp,despachosol.aprorddespfh,
            tipoentrega.nombre as tipentnombre,tipoentrega.icono
            FROM despachosol INNER JOIN despachosoldet
            ON despachosol.id=despachosoldet.despachosol_id
            AND $aux_status
            INNER JOIN notaventa
            ON notaventa.id=despachosol.notaventa_id
            INNER JOIN notaventadetalle
            ON despachosoldet.notaventadetalle_id=notaventadetalle.id
            INNER JOIN producto
            ON notaventadetalle.producto_id=producto.id
            INNER JOIN categoriaprod
            ON categoriaprod.id=producto.categoriaprod_id
            INNER JOIN areaproduccion
            ON areaproduccion.id=categoriaprod.areaproduccion_id
            INNER JOIN cliente
            ON cliente.id=notaventa.cliente_id
            INNER JOIN comuna
            ON comuna.id=despachosol.comunaentrega_id
            INNER JOIN tipoentrega
            ON tipoentrega.id=despachosol.tipoentrega_id
            WHERE $vendedorcond
            and $aux_condFecha
            and $aux_condrut
            and $aux_condoc_id
            and $aux_condgiro_id
            and $aux_condareaproduccion_id
            and $aux_condtipoentrega_id
            and $aux_condnotaventa_id
            and $aux_condcomuna_id
            and $aux_condaprobord
            and $aux_condid
            and $aux_condfechaestdesp
            and $aux_condsucursal_id
            and despachosol.deleted_at is null AND notaventa.deleted_at is null AND notaventadetalle.deleted_at is null
            GROUP BY despachosol.id;";
    //dd($sql);
    $datas = DB::select($sql);

    return $datas;
}