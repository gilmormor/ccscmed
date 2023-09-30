<?php
    use Illuminate\Http\Request;
    use App\Models\DespachoSolDet_InvBodegaProducto;
    use App\Models\DespachoOrd;
    use App\Models\InvBodegaProducto;
?>
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">
<input type="hidden" name="despachosol_id" id="despachosol_id" value="{{$data->id}}">
<input type="hidden" name="notaventa_id" id="notaventa_id" value="{{$data->notaventa_id}}">
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">
<input type="hidden" name="direccioncot" id="direccioncot" value="{{old('direccioncot', $data->direccioncot ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type="hidden" name="comuna_id" id="comuna_id" value="{{old('comuna_id', $data->comuna_id ?? '')}}">
<input type="hidden" name="formapago_id" id="formapago_id" value="{{old('formapago_id', $data->formapago_id ?? '')}}">
<input type="hidden" name="plazopago_id" id="plazopago_id" value="{{old('plazopago_id', $data->plazopago_id ?? '')}}">
<input type="hidden" name="giro_id" id="giro_id" value="{{old('giro_id', $data->giro_id ?? '')}}">
<input type="hidden" name="sucursal_id" id="sucursal_id" value="{{old('sucursal_id', $sucurArray[0] ?? '')}}">


@if($aux_sta==1)
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $vendedor_id ?? '')}}">
@else
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $data->vendedor_id ?? '')}}">
@endif
<input type="hidden" name="region_id" id="region_id" value="{{old('region_id', $data->region_id ?? '')}}">
<input type="hidden" name="provincia_id" id="provincia_id" value="{{old('provincia_id', $data->provincia_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<input type="hidden" name="neto" id="neto" value="{{old('neto', $data->neto ?? '')}}">
<input type="hidden" name="iva" id="iva" value="{{old('iva', $data->iva ?? '')}}">
<input type="hidden" name="total" id="total" value="{{old('total', $data->total ?? '')}}">
<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $data->oc_file ?? '')}}">

<?php
    $disabledReadOnly = "";
    $disabledcliente = "";
    $enableCamposCot = ""; //Este campo lo cambio a disbles si llegara a necesitar desactivar los campos marcados con esta variable
    //Si la pantalla es de aprobacion de Cotizacion desactiva todos input
    //$aux_statusPant=='0', Pantalla normal CRUD de Cotizacion
    //$aux_statusPant=='1', Aprobar o rechazar cotización. Y colocar una observacion
    if($aux_sta==3){
        $disabledReadOnly = ' disabled ';
    }
    $disabledReadOnly = " readonly";
    $aux_concot = false;
    if ($aux_sta==2 and $data->cotizacion_id and $data->id){
        $disabledcliente = ' disabled ';
        $aux_concot = true;
    }
    $disabledcliente = " disabled";

?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    @include('despachoord.datosform')
                </div>
            </div>
        </div>
    </div>    
    <div style="display:none;" class="col-xs-12 col-sm-3">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_id" class="control-label">Nro OrdenCompra</label>
                            <div class="input-group">
                                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" {{$enableCamposCot}}/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="image/*"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th style="display:none;">cotizacion_ID</th>
                            <th class="tooltipsC" title="Código Producto">CodProd</th>
                            <th style="display:none;">CódInterno</th>
                            <th style="display:none;">Cant</th>
                            <th>Cant</th>
                            <!--<th>Desp</th>-->
                            <th>OrdDesp</th>
                            <th>Saldo</th>
                            <th class='tooltipsC' title='Marcar todo' style="text-align:center;display:none;">
                                <div class='checkbox'>
                                    <label style='font-size: 1.2em'>
                                        <input type='checkbox' id='marcarTodo' name='marcarTodo'>
                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                    </label>
                                </div>
                            </th>
                            <th class="width90">OrdDesp</th>
                            <th>Bodegas</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Nombre</th>
                            <th>Diam</th>
                            <th>Clase</th>
                            <th style="display:none;">Diametro</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th>TU</th>
                            <th style="display:none;">TUnion</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th>PUnit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th>Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php $aux_nfila = 0; $i = 0;
                            ?>
                            @foreach($detalles as $detalle)
                                <?php 
                                    $sql = "SELECT cantdesp
                                            FROM vista_sumorddespdet
                                            WHERE despachosoldet_id=$detalle->id";
                                    $datasuma = DB::select($sql);
                                    $peso = $detalle->notaventadetalle->totalkilos/$detalle->notaventadetalle->cant;
                                    if(empty($datasuma)){
                                        $sumacantorddesp= 0;
                                    }else{
                                        $sumacantorddesp= $datasuma[0]->cantdesp;
                                    }
                                    if($detalle->cantsoldesp > $sumacantorddesp){
                                        //if($detalle->id == 8846){
                                        $aux_totalrecABodSolDesp = 0;
                                        $aux_nfila++;
                                        $aux_saldo = $detalle->cantsoldesp - $sumacantorddesp;
                                        $invbodegaproductos = $detalle->notaventadetalle->producto->invbodegaproductos;
                                        $categoriaprod = $detalle->notaventadetalle->producto->categoriaprod;
                                        //CREAR REGISTRO EN TABLA invbodegaproducto (SOLO SI NO EXISTE)
                                        foreach ($categoriaprod->invbodegas as $invbodega){
                                            if($invbodega->tipo == 2){
                                                $invbodegaproducto = InvBodegaProducto::updateOrCreate(
                                                    ['producto_id' => $detalle->notaventadetalle->producto_id,'invbodega_id' => $invbodega->id],
                                                    [
                                                        'producto_id' => $detalle->notaventadetalle->producto_id,
                                                        'invbodega_id' => $invbodega->id
                                                    ]
                                                );
                                            }
                                        }
                                        $aux_cantBodSD = 0;
                                        foreach ($detalle->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto) {
                                            if(($despachosoldet_invbodegaproducto->cant * -1) > 0){
                                                foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
                                                    $aux_cantBodSD += $invmovdet_bodsoldesp->invmovdet->cant;
                                                }
                                            }
                                        }
                                        foreach ($detalle->despachoorddets as $despachoorddet){
                                            $DespachoOrd = DespachoOrd::findOrFail($despachoorddet->despachoord_id);
                                            if(!$DespachoOrd->despachoordanul ){
                                                foreach ($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
                                                    foreach ($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
                                                        //SUMO SOLO EL MOVIMIENTO DE LA BODEGA DE SOL DESPACHO
                                                        if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->nomabre == "SolDe"){
                                                            $aux_cantBodSD += $invmovdet_bodorddesp->invmovdet->cant;
                                                        }
                                                    }
                                                    //SI AUN NO HAY MOVIMIENTO DE INVENTARIO RESTA LOS QUE ESTA EN despachoorddet_invbodegaproducto 
                                                    //ESTO ES POR SI ACASO HAY UNA ORDEN DE DESPACHO SIN GUARDAR EN LA PANTALLA INDEX DE ORDEN DE DESPACHO
                                                    if (sizeof($despachoorddet_invbodegaproducto->invmovdet_bodorddesps) == 0){
                                                        $aux_cantBodSD += $despachoorddet_invbodegaproducto->cant;
                                                    }
                                                }
                                            }
                                        }
                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}" class="proditems">
                                    <td style="display:none;" name="despachosoldet_id{{$aux_nfila}}" id="despachosoldet_id{{$aux_nfila}}">
                                        {{$detalle->id}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="NVdet_id[]" id="NVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                        <input type="text" name="despachosoldet_id[]" id="despachosoldet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                        <input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->cotizaciondetalle_id}}" style="display:none;"/>
                                    </td>
                                    <td style='text-align:center' name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                        {{$detalle->notaventadetalle->producto_id}}
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->codintprod}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cantsoldesp}}" style="display:none;"/>
                                    </td>
                                    <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->cantsoldesp}}
                                    </td>
                                    <!--
                                    <td name="cantdespF{{$aux_nfila}}" id="cantdespF{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->cantdesp}}
                                    </td>
                                    -->
                                    <td name="cantorddespF{{$aux_nfila}}" id="cantorddespF{{$aux_nfila}}" style="text-align:right">
                                        {{$sumacantorddesp}}
                                    </td>
                                    <td name="saldocantOrigF{{$aux_nfila}}" id="saldocantOrigF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_saldo}}
                                    </td>
                                    <td name="saldocantF{{$aux_nfila}}" id="saldocantF{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_saldo}}
                                    </td>
                                    <td class='tooltipsC' style='text-align:center;display:none;' class='tooltipsC' title='Marcar'>
                                        <div class='checkbox'>
                                            <label style='font-size: 1.2em'>
                                                <input type="checkbox" class="checkllenarCantOrd" id="llenarCantOrd{{$aux_nfila}}" name="llenarCantOrd{{$aux_nfila}}" onclick="llenarCantOrd({{$aux_nfila}})">
                                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td name="cantordF{{$aux_nfila}}" id="cantordF{{$aux_nfila}}" style="text-align:right">
                                        <input type="text" name="cantord[]" id="cantord{{$aux_nfila}}" class="form-control numerico cantordsum" onkeyup="actSaldo({{$aux_nfila}})" fila="{{$aux_nfila}}" style="text-align:right;" readonly/>
                                    </td>
                                    <td name="bodegasTB{{$aux_nfila}}" id="bodegasTB{{$aux_nfila}}" style="text-align:center;">
                                        <table class="table" id="tabla-bod" style="font-size:14px;table-layout: fixed;width: 200px;">
                                            <tbody>
                                                <?php $i=0; //dd($invbodegaproductos) ?>
                                                @foreach($invbodegaproductos as $invbodegaproducto)
                                                    @if ($invbodegaproducto->invbodega->sucursal_id == $data->sucursal_id)
                                                        <?php
                                                            //dd($invbodegaproducto);
                                                            $i++;
                                                            $request = new Request();
                                                            $request["producto_id"] = $invbodegaproducto->producto_id;
                                                            $request["invbodega_id"] = $invbodegaproducto->invbodega_id;
                                                            $request["tipo"] = 2;
                                                            $existencia = $invbodegaproducto::existencia($request);
                                                            //$existencia = $invbodegaproductoobj->consexistencia($request);
                                                            //$aux_stock = $invbodegaproducto->invbodega->nomabre == "SolDe" ? $aux_cantBodSD  : $existencia["stock"]["cant"];
                                                            $aux_valueStock = ""; 
                                                            $despachosoldet_invbodegaproducto_id = $detalle->despachosoldet_invbodegaproducto->id;
                                                            if(array_key_exists($invbodegaproducto->id, $arrayBodegasPicking)){
                                                                $aux_stock = $arrayBodegasPicking[$invbodegaproducto->id]["stock"];
                                                                $aux_valueStock = $aux_stock == 0 ? "" : $aux_stock;
                                                            }else{
                                                                //SI NO ESTA EN EL ARRAY DE $arrayBodegasPicking NO TIENE PICKING, ENTONCES LE ASIGNO 0
                                                                if($invbodegaproducto->invbodega->nomabre == "SolDe"){
                                                                    $aux_stock = 0;
                                                                }else{
                                                                    $aux_stock = $existencia["stock"]["cant"];
                                                                }
                                                            }
                                                            if ($invbodegaproducto->invbodega->sucursal_id == 1) {
                                                                $colorSuc = "#26ff00";
                                                            }
                                                            if ($invbodegaproducto->invbodega->sucursal_id == 2) {
                                                                $colorSuc = "#1500ff";
                                                            }
                                                            if ($invbodegaproducto->invbodega->sucursal_id == 3) {
                                                                $colorSuc = "#00c3ff";
                                                            }
                                                        ?>
                                                        @if ($aux_stock > 0 and in_array($invbodegaproducto->invbodega_id,$array_bodegasmodulo) AND ($invbodegaproducto->invbodega->activo == 1)) <!--SOLO MUESTRA LAS BODEGAS TIPO 1, LAS TIPO 2 NO LAS MUESTRA YA QUE ES BODEGA DE DESPACHO -->
                                                            <tr name="fbod{{$invbodegaproducto->id}}" id="fbod{{$invbodegaproducto->id}}">
                                                                <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                                    <input type="text" name="pickingPrevio[]" id="pickingPrevio{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control tooltipsC numerico pickingpreviobod{{$aux_nfila}} cantord{{$aux_nfila}} {{$invbodegaproducto->invbodega->nomabre}} dismpadding" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','OD')" style="text-align:right;" value="{{$aux_valueStock}}" title='Picking previo' nomabrbod="{{$invbodegaproducto->invbodega->nomabre}}" filabod="{{$invbodegaproducto->id}}" stockvalororig="{{$aux_stock}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproducto_id[]" id="invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$invbodegaproducto->id}}" style="display:none;"/>
                                                                    <input type="text" name="despachosoldet_invbodegaproducto_id[]" id="despachosoldet_invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$despachosoldet_invbodegaproducto_id}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproductoNVdet_id[]" id="invbodegaproductoNVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                                    {{$invbodegaproducto->id}}
                                                                </td>
                                                                <td style="text-align:left;padding-right: 0px;padding-left: 2px;padding-top: 4px;padding-bottom: 4px;" class='tooltipsC' title='Bodega: {{$invbodegaproducto->invbodega->nombre}} / {{$invbodegaproducto->invbodega->sucursal->nombre}}'>
                                                                    <div class="centrarhorizontal">
                                                                        <p name="nomabreTD{{$invbodegaproducto->id}}" id="nomabreTD{{$invbodegaproducto->id}}" style="color:{{$colorSuc}};font-size: 11px;margin-bottom: 0px">{{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->abrev}}</p>
                                                                    </div>
                                                                </td>
                                                                <td style="text-align:right;padding-left: 0px;padding-right: 0px;padding-top: 4px;padding-bottom: 4px;"  class='tooltipsC' title='Stock disponible'>
                                                                    <div name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="centrarhorizontal">
                                                                        {{$aux_stock}}
                                                                    </div>
                                                                </td>
                                                                <td class="width90" name="cantorddespF{{$invbodegaproducto->id}}" id="cantorddespF{{$invbodegaproducto->id}}" style="text-align:right;padding-top: 4px;padding-bottom: 4px;">
                                                                    <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control tooltipsC numerico bod{{$aux_nfila}} cantord{{$aux_nfila}} {{$invbodegaproducto->invbodega->nomabre}} dismpadding" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','OD')" style="text-align:right;" value="{{$aux_valueStock}}" title='Cant a despachar' nomabrbod="{{$invbodegaproducto->invbodega->nomabre}}" filabod="{{$invbodegaproducto->id}}" stockvalororig="{{$aux_stock}}"/>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                @if ($i == 0)
                                                    <a style="text-align:center" class='btn-sm tooltipsC' title='Producto sin Bodega Asignada y sin Stock'>
                                                        <i class='fa fa-fw fa-question-circle text-aqua'></i>
                                                    </a>
                                                @endif
                                            </tbody>
                                        </table>
                                    </td>
                                    <td name="cantorddespinputF{{$aux_nfila}}" id="cantorddespinputF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        <input type="text" name="cantorddesp[]" id="cantorddesp{{$aux_nfila}}" class="form-control" style="text-align:right;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$detalle->notaventadetalle->producto->nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->notaventadetalle->producto->diametro}}
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                        {{$detalle->notaventadetalle->producto->claseprod->cla_nombre}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->diametro}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->notaventadetalle->producto->espesor}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->espesor}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->notaventadetalle->producto->long}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->long}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{$peso}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$peso}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right" class="subtotalkg" valor="0.00">
                                        0,00 <!--{{number_format($detalle->notaventadetalle->totalkilos, 2, '.', ',')}}-->
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                        {{$detalle->notaventadetalle->producto->tipounion}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                        <?php $aux_descPorc = $detalle->notaventadetalle->descuento * 100; ?>
                                        {{$aux_descPorc}}%
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->descuento}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <?php $aux_descVal = 1 - $detalle->notaventadetalle->descuento; ?>
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right;"> 
                                        {{number_format($detalle->notaventadetalle->preciounit, 0, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->preciounit}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->notaventadetalle->precioxkilo, 0, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCFTD" style="text-align:right"> 
                                        0,00
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;"> 
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->subtotal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        0,00
                                    </td>
                                </tr>
                                <?php $i++;
                                    }
                                ?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="4" style="text-align:right">
                                    <b>Total Unidades:</b>
                                </td>
                                <td style="text-align:right;padding-bottom: 0px;padding-top: 0px;padding-left: 2px;padding-right: 2px;">
                                    <div class="form-group col-xs-12 col-sm-12" style="margin-bottom: 0px;width: 100px !important">
                                        <input type="text" name="cantordTotal" id="cantordTotal" class="form-control" style="text-align:right;" readonly/>
                                    </div>
                                </td>
                                <td colspan="7" style="text-align:right"><b>Total Kg</b></td>
                                <td id="totalkg" name="totalkg" style="text-align:right">0,00</td>
                                <td colspan="2" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="15" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="15" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotal" name="tdtotal" style="text-align:right">0,00</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')