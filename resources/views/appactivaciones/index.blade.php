@extends("theme.$theme.layout")
@section('titulo')
App Móvil — Códigos de Activación
@endsection

@section("styles")
<style>
    .badge-activo    { background-color: #00a65a; }
    .badge-pendiente { background-color: #f39c12; }
    .badge-sincodigo { background-color: #dd4b39; }
    .codigo-text {
        font-family: monospace;
        font-size: 1.3em;
        letter-spacing: 3px;
        font-weight: bold;
    }
    #tabla-empleados td { vertical-align: middle; }

    #print-codigo {
        visibility: hidden;
        position: absolute;
        top: -9999px;
    }

    @media print {
        body * { visibility: hidden; }
        #print-codigo {
            visibility: visible;
            position: fixed;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }
        #print-codigo * { visibility: visible; }
    }
</style>
@endsection

@section("scripts")
    <script src="{{autoVer('assets/pages/scripts/general.js')}}" type="text/javascript"></script>
    <script src="{{autoVer('assets/pages/scripts/appactivaciones/index.js')}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-mobile"></i> App Móvil — Códigos de Activación</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>

            <div class="box-body">
                {{-- Buscador --}}
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-sm-8 col-md-6">
                        <div class="input-group">
                            <input type="text" id="inp-buscar" class="form-control"
                                   placeholder="Buscar por cédula, nombre o apellido..."
                                   autocomplete="off">
                            <span class="input-group-btn">
                                <button id="btn-buscar" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-6 text-right">
                        <button id="btn-generar-todos" class="btn btn-success btn-sm" title="Genera códigos para todos los empleados activos que no tienen uno">
                            <i class="fa fa-magic"></i> Generar pendientes
                        </button>
                    </div>
                </div>

                {{-- Tabla resultados --}}
                <div class="table-responsive">
                    <table id="tabla-empleados" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Código</th>
                                <th>Activado el</th>
                                <th>Dispositivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-empleados">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Realiza una búsqueda para ver empleados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Área exclusiva para impresión (invisible en pantalla, visible al imprimir) --}}
<div id="print-codigo">
    <div style="display:inline-block; border:2px solid #333; border-radius:6px; width:320px; overflow:hidden; font-family:Arial,sans-serif;">
        <div style="background:#367fa9; color:#fff; padding:12px 15px; display:flex; align-items:center; gap:8px;">
            <i class="fa fa-key" style="font-size:1.1em;"></i>
            <strong style="font-size:1em;">Código de Activación</strong>
        </div>
        <div style="padding:20px; text-align:center;">
            <p id="print-nombre" style="margin:0 0 4px; font-size:0.95em; color:#555;"></p>
            <p id="print-cedula" style="margin:0 0 16px; font-size:0.9em; color:#555;"></p>
            <div style="background:#f4f4f4; border:1px solid #ddd; border-radius:4px; padding:14px; margin-bottom:14px;">
                <span id="print-codigo-valor" style="font-family:monospace; font-size:1.8em; letter-spacing:4px; font-weight:bold; color:#367fa9;"></span>
            </div>
            <p style="font-size:0.82em; color:#777; margin:0;">Entrega este código al empleado. Solo puede usarse una vez.</p>
        </div>
    </div>
</div>

{{-- Modal impresión / visualización del código --}}
<div class="modal fade" id="modal-codigo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title text-white"><i class="fa fa-key"></i> Código de Activación</h4>
            </div>
            <div class="modal-body text-center">
                <p id="modal-empleado-nombre" class="text-muted" style="margin-bottom:5px;"></p>
                <p id="modal-empleado-ced" class="text-muted" style="margin-bottom:15px;"></p>
                <div class="well well-sm" style="background:#f4f4f4;">
                    <span id="modal-codigo-valor" class="codigo-text text-primary"></span>
                </div>
                <p class="text-muted small">Entrega este código al empleado. Solo puede usarse una vez.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btn-sm" onclick="window._printCodigo()"><i class="fa fa-print"></i> Imprimir</button>
                <button class="btn btn-primary btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection
