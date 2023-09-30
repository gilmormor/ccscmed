<div class="col-xs-12 col-md-9 col-sm-12" style="padding-left: 0px;padding-right: 0px;">
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label for="fecha">Fecha Ini:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label for="dep_fecha">Fecha Fin:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="RUT">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label for="rut">RUT:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <div class="input-group">
                    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut')}}" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12" data-toggle='tooltip'/>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Vendedor">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label>Vendedor:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <?php
                    echo $tablashtml['vendedores'];
                ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Número Nota de Venta">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label for="notaventa_id">NotaVenta:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{old('notaventa_id')}}" maxlength="12"/>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Orden de Compra">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label for="oc_id">OC:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id')}}" maxlength="12"/>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Area de Producción">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label >Area Prod:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id">
                    <option value="">Todos</option>
                    @foreach($areaproduccions as $areaproduccion)
                        <option
                            value="{{$areaproduccion->id}}"
                            >
                            {{$areaproduccion->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Tipo de Entrega">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label >T Entrega:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <select name="tipoentrega_id" id="tipoentrega_id" class="selectpicker form-control tipoentrega_id">
                    <option value="">Todos</option>
                    @foreach($tipoentregas as $tipoentrega)
                        <option
                            value="{{$tipoentrega->id}}"
                            >
                            {{$tipoentrega->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Giro">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label>Giro:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <select name="giro_id" id="giro_id" class="selectpicker form-control giro_id">
                    <option value="">Todos</option>
                    @foreach($giros as $giro)
                        <option
                            value="{{$giro->id}}"
                            >
                            {{$giro->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Estatus Nota de Venta">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label>Estatus:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <select name="aprobstatus" id="aprobstatus" class="selectpicker form-control aprobstatus" multiple>
                    <optgroup label="Nota Venta" data-max-options="0">
                        <option value="0">Todos</option>
                        <option value="1">Emitidas sin aprobar</option>
                        <option value="2">Por debajo precio en tabla</option>
                        <option value="3" selected>Aprobadas</option>
                        <option value="4">Rechazadas</option>
                    </optgroup>
                    <optgroup label="Despacho" data-max-options="0">
                        <option value="5">Activas</option>
                        <option value="6">Cerradas Despachadas</option>
                        <option value="7">Cerradas Despacho incompleto</option>
                        <option value="8">Anulada</option>
                    </optgroup>
                </select>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Producto">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label for="producto_idM" class="control-label" title="F2 Buscar">Producto</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <div class="input-group">
                    <input type="text" name="producto_idM" id="producto_idM" class="form-control" tipoval="numericootro" value="{{old('producto_idM', $clienteselec[0]->producto_idM ?? '')}}" placeholder="F2 Buscar"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto" data-toggle='tooltip' title="Buscar">Buscar</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Comuna">
            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                <label>Comuna:</label>
            </div>
            <div class="col-xs-12 col-md-8 col-sm-8">
                <?php
                    echo $tablashtml['comunas'];
                ?>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-12 col-md-3 col-sm-12">
    <div class="col-xs-12 col-md-12 col-sm-12">
        <div class="col-xs-12 col-sm-12">
            <div class="col-xs-12 col-md-12 col-sm-12 text-center">
                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consulta</button>
                <button type="submit" class="btn btn-success tooltipsC" title="Reporte PDF"><i class='glyphicon glyphicon-print'></i> Reporte</button>
                <!--<button type="submit" class="btn-accion-tabla tooltipsC" title="PDF">
                    <i class="fa fa-fw fa-file-pdf-o"></i>
                </button>-->
                <!-- intento de boton modal para el reporte PDF
                <a class='btn-accion-tabla btn-sm' onclick='genreportepdf()' title='Reporte Nota Venta' data-toggle='tooltip'>
                    <i class="fa fa-fw fa-file-pdf-o"></i>
                </a>
                -->                                  
            </div>
        </div>
    </div>
</div>