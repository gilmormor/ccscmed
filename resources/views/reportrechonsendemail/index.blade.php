@extends("theme.$theme.layout")
@section('titulo')
Enviar Recibo Honorarios
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportrechonsendemail/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Enviar por correo Recibo Honorarios</h3>
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
                                        <label data-toggle='tooltip' title="Area de Producción">Periodo:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="mov_nummon" id="mov_nummon" class="selectpicker form-control mov_nummon">
                                            @foreach($nominaPeriodos as $nominaPeriodo)
                                                <option
                                                    value="{{$nominaPeriodo->cot_numnom}}"
                                                    >{{date("d/m/Y", strtotime($nominaPeriodo->cot_fdesde))}} al {{date("d/m/Y", strtotime($nominaPeriodo->cot_fhasta))}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                            <button type='button' id='sendemail' name='sendemail' class='btn btn-success tooltipsC' title="Enviar email">
                                <i class='glyphicon glyphicon-envelope'></i> Enviar
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
@include('generales.buscarnmempleado')
@endsection
