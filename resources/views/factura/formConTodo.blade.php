<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <div class="input-group">
                                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" onkeyup="llevarMayus(this);" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" required {{$disabledReadOnly}} {{$disabledcliente}}/>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar" {{$disabledcliente}}>Buscar</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->guiadesp ? $data->cliente->razonsocial : $data->notaventa->cliente->razonsocial ?? '')}}" readonly/>
                            <input type="text" name="rznsocrecep" id="rznsocrecep" class="form-control" value="{{old('rznsocrecep', $data->guiadesp ? $data->cliente->razonsocial : $data->notaventa->cliente->razonsocial ?? '')}}" style="display:none;" readonly/>
                            <input type="text" name="girorecep" id="girorecep" class="form-control" value="{{old('girorecep', $data->guiadesp ? $data->guiadesp->cliente->giro : $data->notaventa->cliente->giro ?? '')}}" style="display:none;" readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fechahora" class="control-label">Fecha</label>
                            <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', date("d/m/Y", strtotime($data->fechahora)) ?? '')}}" style="padding-left: 0px;padding-right: 0px;" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="direccion" class="control-label">Dirección Princ</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->notaventa->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="telefono" class="control-label requerido">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->notaventa->telefono ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="email" class="control-label requerido">Email</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->notaventa->email ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                            <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $data->notaventa->comuna->nombre ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                            <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', $data->notaventa->comuna->provincia->nombre ?? '')}}" required readonly/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_nombre" class="control-label requerido">Vendedor</label>
                            <input type="text" name="vendedor_nombre" id="vendedor_nombre" class="form-control" value="{{old('vendedor_nombre', $data->notaventa->vendedor->persona->nombre . " " . $data->notaventa->vendedor->persona->apellido ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazopago_desc" class="control-label requerido">Plazo</label>
                            <input type="text" name="plazopago_desc" id="plazopago_desc" class="form-control" value="{{old('plazopago_desc', $data->notaventa->plazopago->descripcion ?? '')}}" required readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="formapago_desc" class="control-label requerido">Forma de Pago</label>
                            <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="{{old('formapago_desc', $data->notaventa->formapago->descripcion ?? '')}}" required readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="giro_nombre" class="control-label requerido">Giro</label>
                            <input type="text" name="giro_nombre" id="giro_nombre" class="form-control" value="{{old('giro_nombre', $data->notaventa->giro->nombre ?? '')}}" required readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
                            <input type="text" name="tipoentrega_nombre" id="tipoentrega_nombre" class="form-control" value="{{old('tipoentrega_nombre', $data->guiadesp ? $data->guiadesp->tipoentrega->nombre : $data->tipoentrega->nombre ?? '')}}" required readonly/>
                            <input type="text" name="tipoentrega_id" id="tipoentrega_id" class="form-control" value="{{old('tipoentrega_id', $data->guiadesp ? $data->guiadesp->tipoentrega_id : ($data->tipoentrega_id ?? ''))}}" required style="display:none;" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
                            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="contacto" class="control-label requerido">Contacto</label>
                            <input type="text" name="contacto" id="contacto" class="form-control" value="{{old('contacto', $data->contacto ?? '')}}" required placeholder="Contacto Entrega" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="contactotelf" class="control-label requerido">Teléfono</label>
                            <input type="text" name="contactotelf" id="contactotelf" class="form-control" value="{{old('contactotelf', $data->contactotelf ?? '')}}" required placeholder="Teléfono Contacto Entrega" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="contactoemail" class="control-label requerido">Email</label>
                            <input type="email" name="contactoemail" id="contactoemail" class="form-control" value="{{old('contactoemail', $data->contactoemail ?? '')}}" required placeholder="Email Contacto Entrega" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fechaestdesp" class="control-label requerido" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
                            <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right" value="{{old('fechaestdesp', $data->fechaestdesp ?? '')}}" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="ot" class="control-label" data-toggle='tooltip' title="Orden de trabajo">OT</label>
                            <input type="text" name="ot" id="ot" class="form-control pull-right numerico" value="{{old('ot', $data->guiadesp ? $data->guiadesp->ot :  ($data->ot ?? ''))}}" maxlength="5"/>
                        </div>

                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="centroeconomico_id" class="control-label requerido">Centro Economico</label>
                            <select name="centroeconomico_id" id="centroeconomico_id" class="form-control select2  centroeconomico_id" data-live-search='true' value="{{old('centroeconomico_id', $data->centroeconomico_id ?? '')}}" required>
                                @foreach($centroeconomicos as $centroeconomico)
                                    <option
                                        value="{{$centroeconomico->id}}"
                                        @if (($data->notaventa->sucursal_id==$centroeconomico->sucursal_id))
                                            {{'selected'}}
                                        @endif
                                        >{{$centroeconomico->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="tipodespacho" class="control-label requerido" data-toggle='tooltip' title="Tipo Despacho">Tipo Desp</label>
                            <select name="tipodespacho" id="tipodespacho" class="form-control select2  tipodespacho" data-live-search='true' value="{{old('tipodespacho', $data->guiadesp ? $data->guiadesp->tipodespacho : ($data->tipodespacho ?? ''))}}" required>
                                <option 
                                    value="1"
                                    @if (($data->guiadesp) and ($data->guiadesp->tipodespacho=="1"))
                                        {{'selected'}}
                                    @endif
                                    >Despacho por cuenta del receptor del documento (cliente o vendedor en caso de Facturas de compra.)</option>
                                <option 
                                    value="2"
                                    @if (($data->guiadesp))
                                        @if (($data->guiadesp->tipodespacho=="2"))
                                            {{'selected'}}
                                        @endif
                                    @else 
                                        {{'selected'}}
                                    @endif
                                    >Despacho por cuenta del emisor a instalaciones del cliente</option>
                                <option 
                                    value="3"
                                    @if (($data->guiadesp) and ($data->guiadesp->tipodespacho=="3"))
                                        {{'selected'}}
                                    @endif
                                    >Despacho por cuenta del emisor a otras instalaciones (Ejemplo: entrega en Obra)</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="indtraslado" class="control-label requerido">Tipo Traslado</label>
                            <select name="indtraslado" id="indtraslado" class="form-control select2  indtraslado" data-live-search='true' value="{{old('indtraslado', $data->guiadesp ? $data->guiadesp->indtraslado : ($data->indtraslado ?? ''))}}" required>
                                <option 
                                    value="1" 
                                    @if($data->guiadesp) 
                                        @if($data->guiadesp->indtraslado =="1")
                                            {{'selected'}}
                                        @endif
                                    @else
                                        {{'selected'}}
                                    @endif
                                    >Operación constituye venta</option>
                                <option 
                                    value="6"
                                    @if(($data->guiadesp) and $data->guiadesp->indtraslado =="6")
                                        {{'selected'}}
                                    @endif
                                    >Otros traslados no venta</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
                            <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $data->guiadesp ? $data->guiadesp->lugarentrega : ($data->lugarentrega ?? ''))}}" required placeholder="Lugar de Entrega"/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="comunaentrega_id" class="control-label requerido">Comuna Entrega</label>
                            <select name="comunaentrega_id" id="comunaentrega_id" class="form-control select2  comunaentrega_id" data-live-search='true' value="{{old('comunaentrega_id', $data->comunaentrega_id ?? '')}}" required>
                                <option value="">Seleccione...</option>
                                @foreach($comunas as $comuna)
                                    <option
                                        value="{{$comuna->id}}"
                                        @if (!isset($data->guiadesp) and $comuna->id==$data->comunaentrega_id)
                                            {{'selected'}}
                                        @else 
                                            @if(isset($data->guiadesp) and $comuna->id==$data->guiadesp->comunaentrega_id)
                                                {{'selected'}}
                                            @endif
                                        @endif
                                        >{{$comuna->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="obs" class="control-label">Observaciones</label>
                            <input type='text' name="obs" id="obs" class="form-control" value="{{old('obs', $data->guiadesp ? $data->guiadesp->obs : ($data->observacion ?? ''))}}" placeholder="Observaciones" maxlength="90"/>
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
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
                            <th class="width30" style="text-align:right;">Cant</th>
                            <th class="tooltipsC" title="Unidad de Medida">UniMed</th>
                            <th>Nombre</th>
                            <th style="text-align:right;">Kilos</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th style="text-align:right;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th style="text-align:right;">Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; $i = 0;
                            $aux_Tsubtotal = 0;
                            $aux_Tcantdesp = 0;
                            $aux_Tkilos = 0;
                        ?>
                        @foreach($data->despachoorddets as $despachoorddet)
                            <?php 
                                $aux_nfila++;
                                $NVDet = $despachoorddet->notaventadetalle;
                                $aux_subtotal = ($NVDet->subtotal/$NVDet->cant) * $despachoorddet->cantdesp;
                                $aux_Tsubtotal += $aux_subtotal;
                                $aux_Tcantdesp += $despachoorddet->cantdesp;
                                $aux_kilos = ($NVDet->totalkilos/$NVDet->cant) * $despachoorddet->cantdesp;
                                $aux_Tkilos += $aux_kilos;
                                //dd($despachoorddet->guiadespdet);
                            ?>
                            <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}" class="proditems">
                                <td style='text-align:center'>
                                    {{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->nrolindet : ($i + 1)}}
                                    <input type="text" name="nrolindet[]" id="nrolindet{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->nrolindet : ($i + 1)}}" style="display:none;"/>
                                    <input type="text" name="despachoorddet_id[]" id="despachoorddet_id{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->id}}" style="display:none;"/>
                                    <input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id{{$aux_nfila}}" class="form-control" value="{{$NVDet->id}}" style="display:none;"/>
                                    <input type="text" name="iddet[]" id="iddet{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->id : ''}}" style="display:none;"/>
                                    <input type="text" name="obsdet[]" id="obsdet{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->obs : ''}}" style="display:none;"/>
                                    
                                </td>
                                <td style='text-align:center' name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                    {{$NVDet->producto_id}}
                                    <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$NVDet->producto_id}}" style="display:none;"/>
                                </td>
                                <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                    {{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->qtyitem : $despachoorddet->cantdesp}}
                                    <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->qtyitem : $despachoorddet->cantdesp}}" style="display:none;"/>
                                    <input type="text" name="qtyitem[]" id="qtyitem{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->qtyitem : $despachoorddet->cantdesp}}" style="display:none;"/>
                                </td>
                                <td name="unidadmedida_nombre{{$aux_nfila}}" id="unidadmedida_nombre{{$aux_nfila}}">
                                    {{$NVDet->unidadmedida->nombre}}
                                    <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->unidadmedida_id : $NVDet->unidadmedida_id}}" style="display:none;"/>
                                    <input type="text" name="unmditem[]" id="unmditem{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->unmditem : $NVDet->unidadmedida->nombre}}" style="display:none;"/>
                                </td>
                                <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                    {{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->nmbitem : $NVDet->producto->nombre}}
                                    <input type="text" name="nmbitem[]" id="nmbitem{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->nmbitem : $NVDet->producto->nombre}}" style="display:none;"/>
                                    <input type="text" name="dscitem[]" id="dscitem{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->dscitem : ''}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;"> 
                                    <a id="aux_kilos{{$aux_nfila}}" name="aux_kilos{{$aux_nfila}}" class="btn-accion-tabla btn-sm>"  onclick="editKilos({{$aux_nfila}})" title="Editar Kilos" data-toggle="tooltip"> 
                                        {{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->itemkg : $aux_kilos}}
                                    </a>
                                    <a class="btn-accion-tabla btn-sm>"  onclick="editKilos({{$aux_nfila}})" title="Editar Kilos" data-toggle="tooltip"> 
                                        <i class="fa fa-fw fa-pencil"></i>
                                    </a>
                                    <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->itemkg : $aux_kilos}}" style="display:none;"/>
                                    <input type="text" name="itemkg[]" id="itemkg{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->itemkg : $aux_kilos}}" style="display:none;"/>
                                </td>
                                <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                    <?php $aux_descPorc = $NVDet->descuento * 100; ?>
                                    {{$aux_descPorc}}%
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$NVDet->descuento}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;display:none;">
                                    <?php $aux_descVal = 1 - $NVDet->descuento; ?>
                                    <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                </td>
                                <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right;"> 
                                    {{number_format($NVDet->preciounit, 0, ',', '.')}}
                                    <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->prcitem : $NVDet->preciounit}}" style="display:none;"/>
                                    <input type="text" name="prcitem[]" id="prcitem{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->prcitem : $NVDet->preciounit}}" style="display:none;"/>
                                </td>
                                <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                    {{number_format($NVDet->precioxkilo, 0, ',', '.')}}                                    
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$NVDet->precioxkilo}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$NVDet->precioxkiloreal}}" style="display:none;"/>
                                </td>
                                <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCFTD" style="text-align:right"> 
                                    {{number_format($despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->montoitem : $aux_subtotal, 0, ',', '.')}}
                                    <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->montoitem : $aux_subtotal}}" style="display:none;"/>
                                    <input type="text" name="montoitem[]" id="montoitem{{$aux_nfila}}" class="form-control" value="{{$despachoorddet->guiadespdet ? $despachoorddet->guiadespdet->montoitem : $aux_subtotal}}" style="display:none;"/>
                                </td>
                            </tr>
                            <?php $i++;
                            ?>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr id="trneto" name="trneto">
                            <th colspan="2" style="text-align:right">
                                <b>Totales:</b>
                            </th>
                            <th style="text-align:right">
                                {{$aux_Tcantdesp}}
                            </th>
                            <th colspan="2" style="text-align:right"><b>Total Kg</b></th>
                            <th id="totalkg" name="totalkg" style="text-align:right">{{$aux_Tkilos}}</th>
                            <th style="text-align:right"><b>Neto</b></th>
                            <th id="tdneto" name="tdneto" style="text-align:right">{{number_format($aux_Tsubtotal, 0, ',', '.')}}</th>
                        </tr>
                        <?php 
                            $aux_Tiva = round(($empresa->iva * $aux_Tsubtotal/100));
                            $aux_total = round($aux_Tsubtotal + $aux_Tiva);
                        ?>
                        <tr id="triva" name="triva">
                            <th colspan="7" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></th>
                            <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($aux_Tiva, 0, ',', '.')}}</th>
                        </tr>
                        <tr id="trtotal" name="trtotal">
                            <th colspan="7" style="text-align:right"><b>Total</b></th>
                            <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                        </tr>

                        
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.buscarclientebd')
