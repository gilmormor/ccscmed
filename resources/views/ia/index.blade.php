@extends("theme.$theme.layout")
@section('titulo')
Asistente IA
@endsection

@section("styles")
<style>
    #chat-container {
        height: 500px;
        overflow-y: auto;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .burbuja {
        max-width: 85%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13.5px;
        line-height: 1.5;
        word-break: break-word;
    }
    .burbuja-usuario {
        align-self: flex-end;
        background: #337ab7;
        color: #fff;
        border-bottom-right-radius: 2px;
    }
    .burbuja-ia {
        align-self: flex-start;
        background: #fff;
        border: 1px solid #ddd;
        color: #333;
        border-bottom-left-radius: 2px;
    }
    .burbuja-error {
        align-self: flex-start;
        background: #f2dede;
        border: 1px solid #ebccd1;
        color: #a94442;
        border-bottom-left-radius: 2px;
    }
    .burbuja-label {
        font-size: 11px;
        font-weight: bold;
        margin-bottom: 4px;
        opacity: 0.7;
    }
    .ia-tabla {
        width: 100%;
        font-size: 12px;
        margin-top: 8px;
    }
    .ia-tabla th {
        background: #337ab7;
        color: #fff;
        padding: 5px 8px;
    }
    .ia-tabla td {
        padding: 4px 8px;
        border-bottom: 1px solid #eee;
    }
    .ia-tabla tr:hover td {
        background: #f5f5f5;
    }
    .ia-grafico-wrap {
        margin-top: 10px;
        max-width: 500px;
    }
    .typing-indicator span {
        display: inline-block;
        width: 8px; height: 8px;
        background: #aaa;
        border-radius: 50%;
        margin: 0 2px;
        animation: bounce 1.2s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0.8); }
        40%            { transform: scale(1.3); }
    }
</style>
@endsection

@section("scripts")
<script src="{{asset('assets/lte/bower_components/chart/Chart.bundle.min.js')}}"></script>
<script src="{{autoVer('assets/pages/scripts/ia/index.js')}}"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-comments-o"></i> Asistente IA de Nómina</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                {{-- Área de mensajes --}}
                <div id="chat-container"></div>

                {{-- Área de input --}}
                <div class="row" style="margin-top:12px;">
                    <div class="col-xs-12 col-md-10">
                        <textarea id="pregunta" class="form-control" rows="2"
                            placeholder="Ej: Hazme un gráfico de barras con el total de sueldos por ubicación administrativa..."
                            style="resize:none;"></textarea>
                    </div>
                    <div class="col-xs-12 col-md-2" style="padding-top:2px;">
                        <button id="btnEnviar" class="btn btn-primary btn-block">
                            <i class="fa fa-paper-plane"></i> Enviar
                        </button>
                        <button id="btnLimpiar" class="btn btn-default btn-block" style="margin-top:4px;">
                            <i class="fa fa-trash"></i> Limpiar
                        </button>
                    </div>
                </div>

                {{-- Ejemplos de preguntas --}}
                <div class="row" style="margin-top:14px;">
                    <div class="col-xs-12">
                        <small class="text-muted"><strong>Ejemplos:</strong></small><br>
                        <a href="#" class="ejemplo-pregunta label label-default" style="margin:3px 3px 0 0; display:inline-block; cursor:pointer; font-size:12px;">
                            Gráfico de barras con total de sueldos por ubicación administrativa
                        </a>
                        <a href="#" class="ejemplo-pregunta label label-default" style="margin:3px 3px 0 0; display:inline-block; cursor:pointer; font-size:12px;">
                            Tabla con todos los empleados y su cargo
                        </a>
                        <a href="#" class="ejemplo-pregunta label label-default" style="margin:3px 3px 0 0; display:inline-block; cursor:pointer; font-size:12px;">
                            Excel con los empleados y su sueldo por empresa
                        </a>
                        <a href="#" class="ejemplo-pregunta label label-default" style="margin:3px 3px 0 0; display:inline-block; cursor:pointer; font-size:12px;">
                            ¿Cuántos empleados hay en total?
                        </a>
                        <a href="#" class="ejemplo-pregunta label label-default" style="margin:3px 3px 0 0; display:inline-block; cursor:pointer; font-size:12px;">
                            Gráfico de torta con cantidad de empleados por tipo de nómina
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
