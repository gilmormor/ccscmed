<?php

namespace App\Http\Controllers;

use App\Models\DespachoOrd;
use App\Models\DespachoOrdAnulGuiaFact;
use App\Models\Dte;
use App\Models\DteAnul;
use App\Models\GuiaDesp;
use App\Models\InvBodegaProducto;
use App\Models\InvMov;
use App\Models\InvMovDet;
use App\Models\InvMovDet_BodOrdDesp;
use App\Models\InvMovDet_BodSolDesp;
use App\Models\InvMovModulo;
use Illuminate\Http\Request;

class DespachoOrdAnulGuiaFactController extends Controller
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

    public function guardaranularguia(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            /*
            $guiadesp = GuiaDesp::findOrFail($request->id);
            if($request->updated_at != $guiadesp->updated_at){
                return response()->json([
                    'mensaje' => 'No se actualizaron los datos, registro fue modificado por otro usuario!',
                    'tipo_alert' => 'alert-error'
                ]);
                //POR ALGUNA RAZON NO REDIRECCIONA A 'factura_listarguiadesp'
                return redirect('factura_listarguiadesp')->with([
                    'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario!',
                    'tipo_alert' => 'alert-error'
                ]);
            }
            */
            
            if(isset($request->procesoorigen)){
                //SI SE ANULA DESDE LA GUIA DESPACHO GENERADA DTE
                if($request->procesoorigen == "AnularDTE"){
                    $dte = Dte::findOrFail($request->dte_id);
                    if($request->updated_at != $dte->updated_at){
                        return redirect('dteguiadesp')->with([
                            'mensaje'=>'No se actualizaron los datos, registro fue modificado por otro usuario! dteguiadesp',
                            'tipo_alert' => 'alert-error'
                        ]);
                    }/*
                    $data = DespachoOrd::findOrFail($request->despachoord_id);
                    if($request->despordupdated_at != $data->updated_at){
                        return response()->json([
                            'mensaje' => 'No se actualizaron los datos, registro fue modificado por otro usuario! DespachoOrd',
                            'tipo_alert' => 'error'
                        ]);
                    }
                    */

                    $request->request->add(['usuario_id' => auth()->id()]);
                    $dteanul = DteAnul::create($request->all());
                    $dte->updated_at = date("Y-m-d H:i:s");
                    if($dte->save()){
                        $despachoord = DespachoOrd::findOrFail($dte->dteguiadesp->despachoord_id);
                        $despachoord->updated_at = date("Y-m-d H:i:s");
                        if(!$despachoord->save()){
                            return response()->json([
                                'mensaje' => 'Error al guardar!',
                                'tipo_alert' => 'error'
                            ]);
                        }
                    }else{
                        return response()->json([
                            'mensaje' => 'Error al guardar!',
                            'tipo_alert' => 'error'
                        ]);
                    }

                }
                //ESTO EN CASO QUE SE ANULE DESDE ORDEN DE DESPACHO, OJO EN ESTE MODULO NO ESTOY ENVIANDO ESTE VALOR
                if($request->procesoorigen == "AnularDespOrd"){
                    $data = DespachoOrd::findOrFail($request->despachoord_id);
                    if($request->updated_at != $data->updated_at){
                        return response()->json([
                            'mensaje' => 'No se actualizaron los datos, registro fue modificado por otro usuario!',
                            'tipo_alert' => 'error'
                        ]);
                    }    
                }
            }
            if(!isset($request->despordupdated_at)){ //ESTA VARIABLE LA CREE EN INDEX DTEGUIADESP PARA TENER SOLO EL UPDATE DE DESPACHOORD, SI NO EXISTE HACE LA VALIDACION
                $despachoord = DespachoOrd::findOrFail($request->despachoord_id);
                //EN COMENTARIO PORQUE DEBO REVISAR SI REGISTRO YA FUE EDITADOR POR OTRO USUARIO
                //TENGO QUE REVISAR PORQUE POSE ESTA CONDICION if(isset($request->procesoorigen) and $request->procesoorigen == 1)
                //if(isset($request->procesoorigen) and $request->procesoorigen == 1){
                    if($request->updated_at != $despachoord->updated_at){
                        return response()->json([
                            'status' => 0,
                            'id' => 0,
                            'error' => '0',
                            'title' => '',
                            'mensaje' => 'Registro fué modificado por otro usuario.',
                            'tipo_alert' => 'error'
                        ]);
                    }    
                //}
                if(isset($request->pantalla_origen) and $request->pantalla_origen == 2){
                    if($request->updated_at != $despachoord->updated_at){
                        return response()->json([
                            'status' => 0,
                            'id' => 0,
                            'error' => '0',
                            'title' => '',
                            'mensaje' => 'Registro fué modificado por otro usuario.',
                            'tipo_alert' => 'error'
                        ]);
                    }    
                }
    
            }


            /*
            $aux_bandera = true;
            foreach ($despachoord->despachoorddets as $despachoorddet) {
                $aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$request->invbodega_id);
                if($aux_respuesta["bandera"] == false){
                    $aux_bandera = $aux_respuesta["bandera"];
                    break;
                }
            }
            */
            $annomes = date("Ym");
            if($request->pantalla_origen == 1){
                $invmodulo = InvMovModulo::where("cod","ORDDESP")->get();
                if(count($invmodulo) == 0){
                    return response()->json([
                        'status'=>'0',
                        'id' => 0,
                        'error' => '0',
                        'title' => '',    
                        'mensaje'=> "No existe modulo ORDDESP",
                        'tipo_alert' => 'error'
                    ]);
                }
                $invmoduloBod = InvMovModulo::findOrFail($invmodulo[0]->id);
                $aux_DespachoBodegaId = $invmoduloBod->invmovmodulobodents[0]->id; //Id Bodega Despacho (La bodega despacho debe ser unica)
                validarSiExisteBodega($despachoord,$invmoduloBod);

                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                        $aux_bodegadespacho_id = 0;
                        foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                            //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                            if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                $aux_bodegadespacho_id = $invmovmodulobodent->id;
                                $requestProd = new Request();
                                $requestProd["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                                $requestProd["invbodega_id"] = $aux_bodegadespacho_id;
                                $requestProd["tipo"] = 2;
                                $arrayExistencia = InvBodegaProducto::existencia($requestProd);
                                $existencia = $arrayExistencia["stock"]["cant"];
                                $existencia += $oddetbodprod->cant;
                                //$aux_respuesta = InvBodegaProducto::validarExistenciaStock($despachoorddet->despachoorddet_invbodegaproductos,$aux_bodegadespacho_id);
                                if($existencia < 0){
                                    //$aux_bandera = $aux_respuesta["bandera"];
                                    return response()->json([
                                        'status'=>'0',
                                        'id' => 0,
                                        'error' => '0',
                                        'title' => '',                    
                                        'mensaje'=> "Sin Stock Sucursal: " . $invmovmodulobodent->sucursal->nombre . ", Bodega: " . $oddetbodprod->invbodegaproducto->invbodega->nombre . ". Id: " . $requestProd["producto_id"] . ", Nombre: " . $oddetbodprod->invbodegaproducto->producto->nombre . ", Stock: " . $arrayExistencia["stock"]["cant"] . " Mov: " . $oddetbodprod->cant,
                                        'tipo_alert' => 'error'
                                    ]);
                                }    
                            }
                        }
                        if($aux_bodegadespacho_id == 0){
                            return response()->json([
                                'status'=>'0',
                                'id' => 0,
                                'error' => '0',
                                'title' => '',            
                                'mensaje'=> "No existe Bodega Despacho de Salida en modulo invmodulo: " . $invmoduloBod->nombre . ". Debe ser creada. ",
                                'tipo_alert' => 'error'
                            ]);         
                        }

                    }
                    //ESTO DEBE IR EN EL PROYECTO FINAL
                }
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = $annomes;
                $invmov_array["desc"] = "Salida por anular aprobación de OD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                $invmov_array["obs"] = "Salida por anular aprobación de OD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //Orden de Despacho
                $invmov_array["idmovmod"] = $request->id;
                $invmov_array["invmovtipo_id"] = 2;
                $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                $arrayinvmov_id = array();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        //ESTO DEBE IR EN EL PROYECTO FINAL
                        $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                        foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                            //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                            if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                $aux_bodegadespacho_id = $invmovmodulobodent->id;
                            }
                        }      
                        //ESTO DEBE IR EN EL PROYECTO FINAL
                        
                        $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                            ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho_id],
                            [
                                'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                'invbodega_id' => $aux_bodegadespacho_id
                            ]
                        );

                        $array_invmovdet = $oddetbodprod->attributesToArray();
                        $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                        $array_invmovdet["invbodega_id"] = $aux_bodegadespacho_id;
                        $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                        $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                        $array_invmovdet["invmovtipo_id"] = 2;
                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                        $array_invmovdet["cantxgrupo"] = 1;
                        $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                        $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                        $array_invmovdet["invmov_id"] = $invmov->id;
                        $invmovdet = InvMovDet::create($array_invmovdet);
                        $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                            'invmovdet_id' => $invmovdet->id,
                            'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                        ]);

                    }
                }
                $invmov_array = array();
                $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                $invmov_array["annomes"] = $annomes;
                $invmov_array["desc"] = "Entrada por anular aprobacion de OD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                $invmov_array["obs"] = "Entrada por anular aprobacion de OD / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                $invmov_array["invmovmodulo_id"] = $invmoduloBod->id; //Orden de Despacho
                $invmov_array["idmovmod"] = $request->id;
                $invmov_array["invmovtipo_id"] = 1;
                $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                $invmov_array["usuario_id"] = auth()->id();
                
                $invmov = InvMov::create($invmov_array);
                array_push($arrayinvmov_id, $invmov->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        $array_invmovdet = $oddetbodprod->attributesToArray();
                        $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                        $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                        $array_invmovdet["sucursal_id"] = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id;
                        $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                        $array_invmovdet["invmovtipo_id"] = 1;
                        $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                        $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                        $array_invmovdet["cantxgrupo"] = 1;
                        $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                        $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                        $array_invmovdet["invmov_id"] = $invmov->id;
                        $invmovdet = InvMovDet::create($array_invmovdet);
                        /*
                        $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                            'invmovdet_id' => $invmovdet->id,
                            'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                        ]);
                        */
                        if ($oddetbodprod->invbodegaproducto->invbodega->tipo == 1){ //Si = 1 Bodega de Picking
                            /***BUSCO LA BODEGA QUE TIENE PICKING */
                            /***ENTRADA A PICKING POR ANULAR GUIA DESPACHO */
                            foreach($oddetbodprod->despachoorddet->despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                                if(($despachosoldet_invbodegaproducto->cant * -1) > 0){
                                    $invmovdet_bodorddesp = InvMovDet_BodSolDesp ::create([
                                        'invmovdet_id' => $invmovdet->id,
                                        'despachosoldet_invbodegaproducto_id' => $despachosoldet_invbodegaproducto->id
                                    ]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }else{

                $invmoduloGiaD = InvMovModulo::where("cod","GUIADESP")->get();
                if(count($invmoduloGiaD) == 0){
                    return response()->json([
                        'status'=>'0',
                        'id' => 0,
                        'error' => '0',
                        'title' => '',    
                        'mensaje'=> "No existe modulo GUIADESP",
                        'tipo_alert' => 'error'
                    ]);
                }
                $invmoduloBGiaD = InvMovModulo::findOrFail($invmoduloGiaD[0]->id);
                foreach ($despachoord->despachoorddets as $despachoorddet) {
                    foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                        //ESTO DEBE IR EN EL PROYECTO FINAL
                        $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                        $aux_bodegadespacho_id = 0;
                        foreach($invmoduloBGiaD->invmovmodulobodents as $invmovmodulobodent){
                            //BUSCAR BODEGA PROD TERMINADO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                            if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                $aux_bodegadespacho_id = $invmovmodulobodent->id;
                            }
                        }
                        if($aux_bodegadespacho_id == 0){
                            return response()->json([
                                'status'=>'0',
                                'id' => 0,
                                'error' => '0',
                                'title' => '',            
                                'mensaje'=> "No existe Bodega Despacho de Entrada en modulo invmodulo: " . $invmoduloBGiaD->nombre . ". Debe ser creada. ",
                                'tipo_alert' => 'error'
                            ]);         
                        }
                    }
                }
                //dd($invmoduloBGiaD->invmovmodulobodents);
                //$invmoduloBod =   InvMovModulo::findOrFail($invmoduloGiaD[0]->id);

                if($request->statusM == '1'){
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    $invmov_array["annomes"] = $annomes;
                    $invmov_array["desc"] = "Entrada por anulacion desde asignar Fac / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                    $invmov_array["obs"] = "Entrada por anulacion desde asignar Fac / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                    $invmov_array["invmovmodulo_id"] = $invmoduloBGiaD->id; //Modulo Guia Despacho
                    $invmov_array["idmovmod"] = $request->id;
                    $invmov_array["invmovtipo_id"] = 1;
                    $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                    $invmov_array["usuario_id"] = auth()->id();
                    
                    $invmov = InvMov::create($invmov_array);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            //ESTO DEBE IR EN EL PROYECTO FINAL
                            $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
                            foreach($invmoduloBGiaD->invmovmodulobodents as $invmovmodulobodent){
                                //BUSCAR BODEGA PROD TERMINADO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                                if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                                    $aux_bodegadespacho_id = $invmovmodulobodent->id;
                                }
                            }      
                            //ESTO DEBE IR EN EL PROYECTO FINAL
                            
                            $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                ['producto_id' => $oddetbodprod->invbodegaproducto->producto_id,'invbodega_id' => $aux_bodegadespacho_id],
                                [
                                    'producto_id' => $oddetbodprod->invbodegaproducto->producto_id,
                                    'invbodega_id' => $aux_bodegadespacho_id
                                ]
                            );
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["invbodegaproducto_id"] = $invbodegaproducto->id;
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $aux_bodegadespacho_id;
                            $array_invmovdet["sucursal_id"] = $invbodegaproducto->invbodega->sucursal_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 1;
                            $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                            $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                            $array_invmovdet["cantxgrupo"] = 1;
                            $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                            $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);
                            $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                                'invmovdet_id' => $invmovdet->id,
                                'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                            ]);
                        }
                    } 
                }else{
                    $aux_codigos = " NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id; //CODIGOS TRAZABILIDAD
                    if(isset($request->descmovinv)){                        
                        $aux_desc = $request->descmovinv;
                        if(isset($request->dte_id)){
                            $dte = Dte::findOrFail($request->dte_id);
                            $aux_desc = $aux_desc . $aux_codigos . " DTE_ID:" . $dte->id;
                            if(!is_null($dte->nrodocto)){
                                $aux_desc = $aux_desc . " DTE_nrodocto:" . $dte->nrodocto;
                            }
                        }
                    }else{
                        $aux_desc = "Entrada por anulacion desde asignar Fact / NV:" . $aux_codigos;
                    }
                    $invmov_array = array();
                    $invmov_array["fechahora"] = date("Y-m-d H:i:s");
                    /* //San Bernardo
                    $invmov_array["annomes"] = $annomes;
                    $invmov_array["desc"] = "Entrada por anulacion desde asignar Fact / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                    $invmov_array["obs"] = "Entrada por anulacion desde asignar Fact / NV:" . $despachoord->notaventa_id . " SD:" . $despachoord->despachosol_id . " OD:" . $request->id;
                    */
                    $invmov_array["annomes"] =  $annomes;
                    $invmov_array["desc"] = $aux_desc;
                    $invmov_array["obs"] = $aux_desc;
                    $invmov_array["invmovmodulo_id"] = $invmoduloBGiaD->id; //Modulo Guia Despacho
                    $invmov_array["idmovmod"] = $request->id;
                    $invmov_array["invmovtipo_id"] = 1;
                    $invmov_array["sucursal_id"] = $despachoord->notaventa->sucursal_id;
                    $invmov_array["usuario_id"] = auth()->id();
                    $invmov = InvMov::create($invmov_array);
                    foreach ($despachoord->despachoorddets as $despachoorddet) {
                        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
                            $array_invmovdet = $oddetbodprod->attributesToArray();
                            $array_invmovdet["producto_id"] = $oddetbodprod->invbodegaproducto->producto_id;
                            $array_invmovdet["invbodega_id"] = $oddetbodprod->invbodegaproducto->invbodega_id;
                            $array_invmovdet["sucursal_id"] = $despachoord->notaventa->sucursal_id; //$oddetbodprod->invbodegaproducto->invbodega->sucursal_id;
                            $array_invmovdet["unidadmedida_id"] = $despachoorddet->notaventadetalle->unidadmedida_id;
                            $array_invmovdet["invmovtipo_id"] = 1;
                            $array_invmovdet["cant"] = $array_invmovdet["cant"] * -1;
                            $array_invmovdet["cantgrupo"] = $array_invmovdet["cant"];
                            $array_invmovdet["cantxgrupo"] = 1;
                            $array_invmovdet["peso"] = $despachoorddet->notaventadetalle->producto->peso;
                            $array_invmovdet["cantkg"] = ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant) * $array_invmovdet["cant"];
                            $array_invmovdet["invmov_id"] = $invmov->id;
                            $invmovdet = InvMovDet::create($array_invmovdet);
                            /*
                            $invmovdet_bodorddesp = InvMovDet_BodOrdDesp ::create([
                                'invmovdet_id' => $invmovdet->id,
                                'despachoorddet_invbodegaproducto_id' => $oddetbodprod->id
                            ]);
                            */
                            if ($oddetbodprod->invbodegaproducto->invbodega->tipo == 1){ //Si = 1 Bodega de Picking
                                /***BUSCO LA BODEGA QUE TIENE PICKING */
                                /***ENTRADA A PICKING POR ANULAR GUIA DESPACHO */
                                foreach($oddetbodprod->despachoorddet->despachosoldet->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                                    if(($despachosoldet_invbodegaproducto->cant * -1) > 0){
                                        $invmovdet_bodorddesp = InvMovDet_BodSolDesp ::create([
                                            'invmovdet_id' => $invmovdet->id,
                                            'despachosoldet_invbodegaproducto_id' => $despachosoldet_invbodegaproducto->id
                                        ]);
                                        break;
                                    }
                                }
                            }
                        }
                    } 
                }
            }
            $despachoord = DespachoOrd::findOrFail($request->despachoord_id);
            $despachoordanulguiafact = new DespachoOrdAnulGuiaFact();
            $despachoordanulguiafact->despachoord_id = $request->despachoord_id;
            $despachoordanulguiafact->guiadespacho = $despachoord->guiadespacho;
            $despachoordanulguiafact->guiadespachofec = $despachoord->guiadespachofec;
            $despachoordanulguiafact->numfactura = $despachoord->numfactura;
            $despachoordanulguiafact->fechafactura = $despachoord->fechafactura;
            $despachoordanulguiafact->numfacturafec = $despachoord->numfacturafec;
            $despachoordanulguiafact->observacion = $request->observacion;
            $despachoordanulguiafact->usuario_id = auth()->id();
            $despachoordanulguiafact->status = $request->statusM;
            $despachoordanulguiafact->save();

            $despachoord->guiadespacho = NULL;
            $despachoord->guiadespachofec = NULL;
            if($request->statusM == '2'){ //Si status es = 1 solo borra la guia de despacho si es = 2 borra guia y factura
                $despachoord->guiadespacho = NULL;
                $despachoord->guiadespachofec = NULL;
                $despachoord->numfactura = NULL;
                $despachoord->fechafactura = NULL;
                $despachoord->numfacturafec = NULL;
                $despachoord->aprguiadesp = NULL;
                $despachoord->aprguiadespfh = NULL;
            }
            if ($despachoord->save()) {
                return response()->json([
                                'status'=>'1',
                                'error' => '0',
                                'title' => '',            
                                'mensaje'=> "Registro procesado con exito",
                                'tipo_alert' => 'success',
                                'id' => $request->id,
                                'nfila' => $request->nfila,
                            ]);
            } else {
                return response()->json([
                    'status'=>'0',
                    'error' => '0',
                    'title' => '',
                    'mensaje'=> "Error al eliminar Guia de despacho de Orden de despacho",
                    'id' => $request->id,
                    'nfila' => $request->nfila,
                    'tipo_alert' => 'error'
                ]);
            }

        } else {
            abort(404);
        }
    }

}

function validarSiExisteBodega($despachoord,$invmoduloBod){
    //ANTES DE PROCESAR LA ORDEN VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAN BODEGA DE DESPACHO CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
    //ESTO DEBE IR EN EL PROYECTO FINAL
    foreach ($despachoord->despachoorddets as $despachoorddet) {
        foreach ($despachoorddet->despachoorddet_invbodegaproductos as $oddetbodprod) {
            $aux_sucursal_id_producto = $oddetbodprod->invbodegaproducto->invbodega->sucursal_id; 
            $aux_bodegadespacho_id = 0;
            foreach($invmoduloBod->invmovmodulobodents as $invmovmodulobodent){
                //BUSCAR BODEGA DESPACHO CORRESPONDIENTE AL PRODUCTO QUE SE ESTA PROCESANDO DEPENDIENDO DE LA SUCURSAL QUE CORRESPONDE EL PRODUCTO
                if($invmovmodulobodent->sucursal_id == $aux_sucursal_id_producto){
                    $aux_bodegadespacho_id = $invmovmodulobodent->id;
                }
            }
            if($aux_bodegadespacho_id == 0){
                return response()->json([
                    'status'=>'0',
                    'id' => 0,
                    'error' => '0',
                    'title' => '',
                    'mensaje'=> 'No existe Bodega Despacho en Sucursal: ' . $despachoord->notaventa->sucursal->nombre,
                    'tipo_alert' => 'error'
                ]);
            }
        }
    }
    //ANTES DE PROCESAR LA ORDEN VALIDO QUE LOS PRODUCTOS INVOLUCRADOS TENGAN BODEGA DE DESPACHO CORRESPONDIENTE A LA SUCURSAL DE CADA PRODUCTO
    //ESTO DEBE IR EN EL PROYECTO FINAL
}