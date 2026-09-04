@extends("theme.$theme.layout")
@section('titulo')
Recibos
@endsection

<?php
    $selecmultprod = true;
?>

@section("styles")
    @include('generales.estiloconstancia')
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportrechongen/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/empleado/buscar.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recibos</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        @csrf @method("put")
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="cedula" data-toggle='tooltip' title="Cedula">Cedula:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="cedula" id="cedula" class="form-control" value="{{old('cedula')}}" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12" data-toggle='tooltip'/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarempleado" name="btnbuscarempleado" data-toggle='tooltip' title="Buscar">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Periodo">Periodo:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="mov_nummon" id="mov_nummon" class="selectpicker form-control periodo">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Rango para la constancia de honorarios. Va en su propio
                                 recuadro porque no filtra el recibo ni la relación: es
                                 el período sobre el que se promedia lo facturado --}}
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-12">
                                    <fieldset class="caja-constancia">
                                        <legend class="caja-constancia-titulo">
                                            <i class="fa fa-calendar"></i> Periodo para promedio de Constancia
                                        </legend>
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-4 col-md-3">
                                                <label for="fecha_desde">Desde</label>
                                                <input type="date" id="fecha_desde" name="fecha_desde" class="form-control">
                                            </div>
                                            <div class="col-xs-6 col-sm-4 col-md-3">
                                                <label for="fecha_hasta">Hasta</label>
                                                <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 col-sm-12 text-center">
                            <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success tooltipsC' title="PDF Recibo Honorarios">
                                <i class='glyphicon glyphicon-print'></i> Recibo
                            </button>
                        </div>
                        <div class="col-xs-12 col-md-1 col-sm-12 text-center">
                            <button type='button' id='btnpdf3' name='btnpdf3' class='btn btn-success tooltipsC' title="PDF Relación Honorarios">
                                <i class='glyphicon glyphicon-print'></i> Rel Hon
                            </button>
                        </div>
                        <div class="col-xs-12 col-md-1 col-sm-12 text-center">
                            <button type='button' id='constancia' name='constancia' class='btn btn-success tooltipsC' title="PDF Constancia de Honorarios">
                                <i class='glyphicon glyphicon-print'></i> Constancia
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.buscarempleado')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
