@extends("theme.$theme.layout")
@section('titulo')
SPG Key
@endsection

@section("styles")
<style>
.spgkey-box {
    max-width: 640px;
    margin: 0 auto;
}
.spgkey-clave input {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    letter-spacing: .05em;
}
</style>
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/spgkey/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">SPG Key</h3>
            </div>
            <div class="box-body">
                <div class="spgkey-box">

                    {{-- Fecha del servidor, no del navegador: día y mes no deben
                         poder alterarse adelantando el reloj del equipo. --}}
                    <input type="hidden" id="spg_dia" value="{{ $dia }}">
                    <input type="hidden" id="spg_mes" value="{{ $mes }}">

                    <div class="row">
                        <div class="col-xs-6 col-sm-3">
                            <label for="spg_fecha">Fecha de Instalación</label>
                            <input type="text" id="spg_fecha" class="form-control" value="{{ $fechaTexto }}" readonly>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <label for="spg_serial">Serial</label>
                            <input type="text" id="spg_serial" class="form-control" maxlength="30" autocomplete="off">
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <label for="spg_codigo1">Código1</label>
                            <input type="text" id="spg_codigo1" class="form-control spgkey-solo-numeros"
                                   inputmode="numeric" maxlength="10" autocomplete="off">
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <label for="spg_codigo2">Código2</label>
                            <input type="text" id="spg_codigo2" class="form-control spgkey-solo-numeros"
                                   inputmode="numeric" maxlength="10" autocomplete="off">
                        </div>
                    </div>

                    <div class="row" style="margin-top:20px;">
                        <div class="col-xs-12 col-sm-8 col-sm-offset-2 spgkey-clave">
                            <label for="spg_clave">Clave</label>
                            <input type="text" id="spg_clave" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row" style="margin-top:20px;">
                        <div class="col-xs-12 text-center">
                            <button type="button" id="spg_generar" class="btn btn-success">
                                <i class="fa fa-key"></i> Generar
                            </button>
                            <button type="button" id="spg_salir" class="btn btn-default">
                                Salir
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
