@extends("theme.$theme.layout")
@section('titulo')
Recibo Honorarios
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
    <script src="{{autoVer("assets/pages/scripts/reportrechon/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Reporte Honorarios</h3>
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
                        <div class="col-xs-12 col-md-8 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                {{-- <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="annomes" class="col-lg-3 control-label">Mes:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="annomes" id="annomes" class="form-control date-picker" value="{{old('annomes', $aux_mesanno ?? '')}}" readonly required>
                                    </div>
                                </div> --}}
                                <div class="col-xs-12 col-sm-7">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Area de Producción">Periodo:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="mov_nummon" id="mov_nummon" class="selectpicker form-control mov_nummon">
                                            @foreach($nominaPeriodos as $nominaPeriodo)
                                                <option
                                                    value="{{$nominaPeriodo->cot_numnom}}"
                                                    id="{{$nominaPeriodo->id}}"
                                                    >{{date("d/m/Y", strtotime($nominaPeriodo->cot_fdesde))}} al {{date("d/m/Y", strtotime($nominaPeriodo->cot_fhasta))}}</option>
                                            @endforeach
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
                                            <div class="col-xs-6 col-sm-5">
                                                <label for="fecha_desde">Desde</label>
                                                <input type="date" id="fecha_desde" name="fecha_desde" class="form-control">
                                            </div>
                                            <div class="col-xs-6 col-sm-5">
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
                            <button type='button' id='btnpdf3' name='btnpdf3' class='btn btn-success tooltipsC' title="Relación Honorarios PDF">
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


@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
