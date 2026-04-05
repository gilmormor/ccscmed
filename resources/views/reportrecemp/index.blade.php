@extends("theme.$theme.layout")
@section('titulo')
Recibo Empleados
@endsection

<?php
    $selecmultprod = true;
?>

@section("styles")
<style>
@media (max-width: 767px) {
    .bootstrap-select .dropdown-menu {
        width: 100% !important;
        max-width: 100vw !important;
        min-width: 0 !important;
        overflow-x: hidden;
    }
    .bootstrap-select .dropdown-menu li a {
        white-space: normal;
        word-break: break-word;
    }
    .bootstrap-select.open {
        position: static !important;
    }
}
</style>
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportrecemp/index.js")}}" type="text/javascript"></script>
    @if($esAdmin)
    <script src="{{autoVer("assets/pages/scripts/empleado/buscar.js")}}" type="text/javascript"></script>
    @endif
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recibo Empleados</h3>
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
                        <div class="col-xs-12 col-md-8 col-sm-9">
                            @if($esAdmin)
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-9">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="emp_ced" data-toggle='tooltip' title="Cédula del Empleado">Cédula:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="emp_ced" id="emp_ced" class="form-control" placeholder="Nro. Cédula" maxlength="12" data-toggle='tooltip'/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarempleado" name="btnbuscarempleado" data-toggle='tooltip' title="Buscar Empleado">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-9">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="cedula" data-toggle='tooltip' title="Cono Monetario">Cono Monetario:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select id="cono_monetario" name="cono_monetario" class="selectpicker form-control tipoentrega_id">>
                                            <option value="2" selected>BsD</option>
                                            <option value="0">BsS</option>
                                            <option value="1">BsF</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-9">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Empresa">Empresa:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="emp_codh" id="emp_codh" class="selectpicker form-control emp_codh">
                                            <option
                                                value=""
                                                >Seleccione...</option>
                                            @foreach($empresas as $empresa)
                                                <option
                                                    value="{{$empresa->emp_codh}}"
                                                    >{{$empresa->emp_nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-9">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Periodo">Periodo:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="mov_nummon" id="mov_nummon" class="selectpicker form-control mov_nummon" data-live-search='true' data-size="10" data-width="100%" style="max-width:100%;">
                                            <option value="" 
                                                mov_codcar=""
                                                cot_tipo=""
                                                mov_codubica=""
                                                >
                                                    Seleccione...
                                                </option>
                                            {{-- @foreach($nominaPeriodos as $nominaPeriodo)
                                                <option
                                                    value="{{$nominaPeriodo->cot_numnom}}"
                                                    >{{date("d/m/Y", strtotime($nominaPeriodo->cot_fdesde))}} al {{date("d/m/Y", strtotime($nominaPeriodo->cot_fhasta))}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-3">
                            <div class="col-xs-6 col-md-6 col-sm-6 text-center">
                                <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success tooltipsC' title="PDF Recibo">
                                    <i class='glyphicon glyphicon-print'></i> Recibo
                                </button>
                            </div>
                            <div class="col-xs-6 col-md-6 col-sm-6 text-center">
                                <button type='button' id='constancia' name='constancia' class='btn btn-success tooltipsC' title="PDF Constancia de Trabajo">
                                    <i class='glyphicon glyphicon-print'></i> Constancia
                                </button>
                            </div>

                        </div>

                        {{-- <div class="col-xs-12 col-md-1 col-sm-12 text-center">
                            <button type='button' id='btnpdf3' name='btnpdf3' class='btn btn-success tooltipsC' title="Relación Honorarios PDF">
                                <i class='glyphicon glyphicon-print'></i> Rel Hon
                            </button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@if($esAdmin)
@include('generales.buscarempleado')
@endif
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
