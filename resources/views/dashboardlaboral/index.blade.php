@extends("theme.$theme.layout")

@section('titulo')
    Portal Laboral — Mi Nómina & Honorarios
@endsection

@section("styles")
<style>
    /* ── Variables corporativas ── */
    :root {
        --dl-primary   : #1a3a5c;
        --dl-accent    : #0d7e6e;
        --dl-warning   : #e67e22;
        --dl-info      : #2980b9;
        --dl-danger    : #e74c3c;
        --dl-shadow    : 0 2px 12px rgba(0,0,0,0.09);
        --dl-radius    : 8px;
    }

    /* ── Banner de bienvenida ── */
    .dl-welcome {
        background: linear-gradient(135deg, var(--dl-primary) 0%, #2c5f8a 100%);
        border-radius: var(--dl-radius);
        padding: 20px 24px;
        color: #fff;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }
    .dl-welcome .dl-avatar {
        width: 60px; height: 60px; border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.7rem; flex-shrink: 0;
    }
    .dl-welcome .dl-info { flex: 1; min-width: 0; }
    .dl-welcome h2 { margin: 0 0 3px; font-size: 1.3rem; font-weight: 700; }
    .dl-welcome p  { margin: 0; opacity: .82; font-size: .87rem; }
    .dl-welcome .dl-badge {
        display: inline-block;
        background: rgba(255,255,255,0.15);
        border-radius: 20px;
        padding: 2px 11px;
        font-size: .75rem;
        margin-top: 6px;
    }
    .dl-welcome .dl-controls {
        display: flex; flex-direction: column; align-items: flex-end;
        gap: 8px; flex-shrink: 0;
    }
    .dl-year-select {
        font-size: .82rem; padding: 4px 8px;
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 5px;
        background: rgba(255,255,255,0.12);
        color: #fff;
        cursor: pointer;
    }
    .dl-year-select option { color: #333; background: #fff; }
    .dl-year-lbl { font-size: .7rem; opacity: .7; text-align: right; }

    /* ── Buscador empleado (admin) ── */
    .dl-emp-search { margin-top: 12px; }
    .dl-emp-search label {
        font-size: 13px; opacity: .88; margin-bottom: 5px;
        display: block; font-weight: 500;
    }
    .dl-emp-search .input-group input {
        background: rgba(255,255,255,.92); border: none;
        border-radius: 6px 0 0 6px; font-size: 14px;
        color: #333; padding: 8px 12px; height: 38px;
    }
    .dl-emp-search .input-group input::placeholder { color: #999; }
    .dl-emp-search .input-group .btn-cedula {
        background: rgba(255,255,255,.22); border: none; color: #fff;
        height: 38px; padding: 0 12px; font-size: 13px;
        border-radius: 0; white-space: nowrap;
    }
    .dl-emp-search .input-group .btn-nombre {
        background: rgba(255,255,255,.12); border: none; color: #fff;
        height: 38px; padding: 0 12px; font-size: 13px;
        border-radius: 0 6px 6px 0; white-space: nowrap;
        border-left: 1px solid rgba(255,255,255,.25);
    }
    .dl-emp-search .input-group .btn-cedula:hover,
    .dl-emp-search .input-group .btn-nombre:hover {
        background: rgba(255,255,255,.3);
    }
    .dl-emp-result { font-size: 13px; margin-top: 7px; min-height: 18px; }
    .emp-badge {
        background: rgba(255,255,255,.2); border-radius: 20px;
        padding: 3px 12px; display: inline-flex; align-items: center; gap: 7px;
        font-size: 13px;
    }
    .btn-clear {
        background: none; border: none; color: rgba(255,255,255,.75);
        padding: 0; font-size: 14px; cursor: pointer; line-height: 1;
    }

    /* ── Selector de año ── */
    .dl-year-select {
        font-size: 14px !important; padding: 6px 10px !important;
        border: 1px solid rgba(255,255,255,.4) !important;
        border-radius: 6px !important;
        background: rgba(255,255,255,.12) !important;
        color: #fff !important; cursor: pointer; min-width: 100px;
        height: 36px;
    }
    .dl-year-select option { color: #333; background: #fff; font-size: 14px; }
    .dl-year-lbl { font-size: 12px; opacity: .75; text-align: right; margin-top: 3px; }

    /* ── Mejoras de legibilidad general ── */
    #tabla-honorarios td,
    #tabla-honorarios th { font-size: 13px !important; }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input { font-size: 13px; }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate { font-size: 13px; }
    .dl-panel-hd h4     { font-size: 14px; }
    .dl-panel-hd .dl-panel-sub { font-size: 12px; }
    .doc-info strong    { font-size: 14px; }
    .doc-info span      { font-size: 12px; }

    /* ── KPI Cards ── */
    .dl-kpi {
        background: #fff;
        border-radius: var(--dl-radius);
        padding: 18px 20px;
        box-shadow: var(--dl-shadow);
        border-left: 4px solid var(--dl-primary);
        margin-bottom: 20px;
        transition: transform .12s;
        position: relative;
        overflow: hidden;
    }
    .dl-kpi:hover { transform: translateY(-2px); }
    .dl-kpi.accent  { border-left-color: var(--dl-accent); }
    .dl-kpi.info    { border-left-color: var(--dl-info); }
    .dl-kpi.warning { border-left-color: var(--dl-warning); }
    .dl-kpi .kpi-label {
        font-size: .73rem; text-transform: uppercase;
        letter-spacing: .04em; color: #999; font-weight: 600; margin-bottom: 5px;
    }
    .dl-kpi .kpi-value {
        font-size: 1.55rem; font-weight: 700; color: var(--dl-primary); line-height: 1.1;
    }
    .dl-kpi.accent  .kpi-value { color: var(--dl-accent); }
    .dl-kpi.info    .kpi-value { color: var(--dl-info); }
    .dl-kpi.warning .kpi-value { color: var(--dl-warning); }
    .dl-kpi .kpi-sub { font-size: .76rem; color: #bbb; margin-top: 4px; }
    .dl-kpi .kpi-icon {
        position: absolute; right: 14px; top: 14px;
        font-size: 2rem; opacity: .08;
    }

    /* ── Paneles generales ── */
    .dl-panel {
        background: #fff; border-radius: var(--dl-radius);
        box-shadow: var(--dl-shadow); margin-bottom: 20px;
    }
    .dl-panel-hd {
        padding: 13px 18px 10px;
        border-bottom: 1px solid #f0f0f0;
        display: flex; align-items: center; justify-content: space-between;
    }
    .dl-panel-hd h4 {
        margin: 0; font-size: .9rem; font-weight: 700; color: var(--dl-primary);
    }
    .dl-panel-hd .dl-panel-sub { font-size: .76rem; color: #bbb; }
    .dl-panel-bd { padding: 14px 18px; }

    /* ── Centro de documentos ── */
    .doc-row {
        display: flex; align-items: center; padding: 10px 0;
        border-bottom: 1px solid #f6f6f6;
    }
    .doc-row:last-child { border-bottom: none; }
    .doc-icon {
        width: 36px; height: 36px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 12px; flex-shrink: 0;
    }
    .doc-info strong { display: block; font-size: .84rem; color: #333; }
    .doc-info span   { font-size: .75rem; color: #aaa; }

    /* ── Banner IA ── */
    .dl-ia {
        background: linear-gradient(135deg, var(--dl-accent) 0%, var(--dl-primary) 100%);
        border-radius: var(--dl-radius); padding: 16px 20px; color: #fff;
        display: flex; align-items: center; gap: 14px; margin-bottom: 20px;
    }
    .dl-ia .ia-ico { font-size: 1.8rem; flex-shrink: 0; }
    .dl-ia h5 { margin: 0 0 2px; font-weight: 700; font-size: .95rem; }
    .dl-ia p  { margin: 0; font-size: .8rem; opacity: .88; }
    .btn-ia {
        background: rgba(255,255,255,.18); color: #fff;
        border: 1px solid rgba(255,255,255,.4); border-radius: 20px;
        padding: 5px 16px; font-size: .8rem; white-space: nowrap;
        flex-shrink: 0; text-decoration: none;
    }
    .btn-ia:hover { background: rgba(255,255,255,.3); color: #fff; }

    /* ── Spinner ── */
    .dl-spinner { text-align: center; padding: 24px; color: #ccc; font-size: .9rem; }

    /* ── Modal PDF mejorado ── */
    #dl-modal-pdf .modal-body { padding: 0; }
    #dl-modal-pdf iframe { display: block; width: 100%; height: 520px; border: none; }
    #dl-modal-pdf .modal-header { padding: 10px 15px; background: var(--dl-primary); }
    #dl-modal-pdf .modal-header h4 { color: #fff; font-size: .95rem; margin: 0; }
    #dl-modal-pdf .modal-header .close { color: #fff; opacity: .8; margin-top: 0; }
    #dl-modal-pdf .modal-footer { padding: 8px 14px; }

    @media (max-width: 767px) {
        .dl-welcome { flex-wrap: wrap; }
        .dl-welcome .dl-controls { flex-direction: row; align-items: center; width: 100%; }
        #dl-modal-pdf iframe { height: 360px; }
    }
</style>
@endsection

@section('contenido')

{{-- ══════════════════════════════════════════════════════
     BANNER DE BIENVENIDA
══════════════════════════════════════════════════════ --}}
<div class="dl-welcome">
    <div class="dl-avatar">
        <i class="fa fa-user-md"></i>
    </div>

    <div class="dl-info">
        @if($nm_empleado)
            <h2>{{ ucwords(strtolower(trim($nm_empleado->emp_nom . ' ' . $nm_empleado->emp_ape))) }}</h2>
            <p>
                C.I. <strong>V-{{ number_format($nm_empleado->emp_ced, 0, ',', '.') }}</strong>
                &nbsp;·&nbsp;
                Ingreso: <strong>{{ $nm_empleado->emp_fecing ? date('d/m/Y', strtotime($nm_empleado->emp_fecing)) : '--' }}</strong>
            </p>
            <span class="dl-badge"><i class="fa fa-building-o"></i>&nbsp;Portal Laboral</span>
        @else
            <h2>Bienvenido, {{ auth()->user()->name ?? auth()->user()->usuario }}</h2>
            <p>Portal de Gestión Laboral — Honorarios Médicos</p>
            {{-- Buscador de empleado para usuarios admin/no-empleado --}}
            <div class="dl-emp-search">
                <label><i class="fa fa-search"></i> Consultar empleado:</label>
                <div class="input-group" style="max-width:400px">
                    <input type="number" id="input-cedula-emp" class="form-control"
                           placeholder="Nro. de cédula..." min="1">
                    <span class="input-group-btn">
                        <button class="btn btn-cedula" id="btn-buscar-emp" type="button"
                                title="Buscar por cédula">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-nombre" id="btn-buscar-nombre" type="button"
                                title="Buscar por nombre o apellido">
                            <i class="fa fa-user"></i> Por nombre
                        </button>
                    </span>
                </div>
                <div class="dl-emp-result" id="emp-result-label"></div>
            </div>
        @endif
    </div>

    <div class="dl-controls">
        <select id="sel-anio" class="dl-year-select">
            <option value="" selected>Todos</option>
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <span class="dl-year-lbl">Filtrar año</span>
    </div>
</div>
{{-- cédula activa leída por JS --}}
<input type="hidden" id="cedula-activa" value="{{ $esEmpleado && $nm_empleado ? $nm_empleado->emp_ced : '' }}">


{{-- ══════════════════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════════════════ --}}
<div class="row">
    {{-- Último Neto --}}
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi">
            <div class="kpi-label"><i class="fa fa-money"></i> Último Neto</div>
            <div class="kpi-value" id="kpi-ultimo-hon"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub" id="kpi-periodo">Asig − Ded · último período</div>
            <i class="fa fa-money kpi-icon"></i>
        </div>
    </div>
    {{-- Neto total --}}
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi accent">
            <div class="kpi-label"><i class="fa fa-line-chart"></i> Neto <span id="kpi-anio-lbl"></span></div>
            <div class="kpi-value" id="kpi-hon-12m"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub" id="kpi-hon-sub">Total neto</div>
            <i class="fa fa-line-chart kpi-icon"></i>
        </div>
    </div>
    {{-- Períodos disponibles --}}
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi info">
            <div class="kpi-label"><i class="fa fa-file-text-o"></i> Recibos Disponibles</div>
            <div class="kpi-value" id="kpi-periodos"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub">Períodos con recibo</div>
            <i class="fa fa-file-text-o kpi-icon"></i>
        </div>
    </div>
    {{-- Promedio mensual --}}
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi warning">
            <div class="kpi-label"><i class="fa fa-bar-chart"></i> Promedio Mensual</div>
            <div class="kpi-value" id="kpi-promedio"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub">Neto promedio por mes</div>
            <i class="fa fa-bar-chart kpi-icon"></i>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     GRÁFICOS
══════════════════════════════════════════════════════ --}}
<div class="row">
    {{-- Evolución mensual --}}
    <div class="col-xs-12 col-md-8">
        <div class="dl-panel">
            <div class="dl-panel-hd">
                <h4><i class="fa fa-bar-chart" style="color:var(--dl-primary)"></i>&nbsp; Evolución de Ingresos</h4>
                <span class="dl-panel-sub">Asignaciones · Deducciones · Neto — agrupado por mes</span>
            </div>
            <div class="dl-panel-bd">
                <div class="dl-spinner" id="loading-evolucion" style="display:none">
                    <i class="fa fa-spinner fa-spin"></i> Cargando...
                </div>
                <canvas id="chart-evolucion" height="110"></canvas>
                <p id="evolucion-empty" style="display:none; text-align:center; color:#ccc; padding:30px 0; margin:0">
                    Sin datos de honorarios disponibles.
                </p>
            </div>
        </div>
    </div>

    {{-- Honorarios por tipo de documento --}}
    <div class="col-xs-12 col-md-4">
        <div class="dl-panel">
            <div class="dl-panel-hd">
                <h4><i class="fa fa-pie-chart" style="color:var(--dl-accent)"></i>&nbsp; Por Tipo de Documento</h4>
                <span class="dl-panel-sub">Histórico total</span>
            </div>
            <div class="dl-panel-bd" style="min-height:240px; position:relative">
                <div class="dl-spinner" id="loading-composicion">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
                <canvas id="chart-composicion" height="180" style="display:none"></canvas>
                <div id="composicion-totales" class="text-center" style="margin-top:8px; font-size:.8rem; color:#777"></div>
                <p id="composicion-empty" style="display:none; text-align:center; color:#ccc; padding:24px 0; margin:0">
                    Sin datos de honorarios.
                </p>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     BANNER IA
══════════════════════════════════════════════════════ --}}
@if(Route::has('ia'))
<div class="dl-ia">
    <div class="ia-ico">🤖</div>
    <div style="flex:1">
        <h5>Asistente IA de Nómina</h5>
        <p>Pregunta en lenguaje natural: "¿Cuánto recibí en honorarios en 2025?" · "¿Cuál fue mi mayor pago?"</p>
    </div>
    <a href="{{ route('ia') }}" class="btn-ia"><i class="fa fa-external-link"></i> Abrir</a>
</div>
@endif


{{-- ══════════════════════════════════════════════════════
     DOCUMENTOS + HISTORIAL
══════════════════════════════════════════════════════ --}}
<div class="row">
    {{-- Centro de Documentos ── --}}
    <div class="col-xs-12 col-md-4">
        <div class="dl-panel">
            <div class="dl-panel-hd">
                <h4><i class="fa fa-folder-open" style="color:var(--dl-warning)"></i>&nbsp; Centro de Documentos</h4>
            </div>
            <div class="dl-panel-bd">

                {{-- Constancia de Trabajo --}}
                <div class="doc-row">
                    <div class="doc-icon" style="background:#fdf0e0">
                        <i class="fa fa-file-pdf-o" style="color:var(--dl-warning)"></i>
                    </div>
                    <div class="doc-info" style="flex:1">
                        <strong>Constancia de Trabajo</strong>
                        <span>Generada al instante con tus datos</span>
                    </div>
                    <button class="btn btn-xs btn-warning" id="btn-constancia" title="Ver Constancia">
                        <i class="fa fa-eye"></i> Ver
                    </button>
                </div>

                {{-- Último Recibo de Honorarios --}}
                <div class="doc-row">
                    <div class="doc-icon" style="background:#e8f8f5">
                        <i class="fa fa-stethoscope" style="color:var(--dl-accent)"></i>
                    </div>
                    <div class="doc-info" style="flex:1">
                        <strong>Último Recibo de Honorarios</strong>
                        <span>Período más reciente disponible</span>
                    </div>
                    <button class="btn btn-xs btn-success" id="btn-ultimo-hon" title="Ver Recibo">
                        <i class="fa fa-eye"></i> Ver
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Historial de Recibos ── --}}
    <div class="col-xs-12 col-md-8">
        <div class="dl-panel">
            <div class="dl-panel-hd">
                <h4><i class="fa fa-history" style="color:var(--dl-info)"></i>&nbsp; Historial de Recibos</h4>
                <span class="dl-panel-sub">Todos los períodos</span>
            </div>
            <div class="dl-panel-bd" style="padding-top:8px">
                <table id="tabla-honorarios" class="table table-hover table-condensed"
                       style="font-size:.82rem; width:100%">
                    <thead>
                        <tr>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th class="text-right">Asignaciones</th>
                            <th class="text-right">Deducciones</th>
                            <th class="text-right">Neto</th>
                            <th class="text-center">Recibo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     MODAL PDF MEJORADO (propio del módulo)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="dl-modal-pdf" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dl-modal-pdf-titulo">Documento</h4>
            </div>
            <div class="modal-body">
                <iframe id="dl-modal-pdf-frame" src="" frameborder="0"
                        allowfullscreen scrolling="yes"></iframe>
            </div>
            <div class="modal-footer">
                <button id="dl-modal-pdf-print" class="btn btn-default" title="Imprimir">
                    <i class="fa fa-print"></i> Imprimir
                </button>
                <a id="dl-modal-pdf-download" href="#" target="_blank"
                   class="btn btn-primary" title="Abrir en nueva pestaña para descargar">
                    <i class="fa fa-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal buscador de empleado por nombre (reutilizado de reportrechongen) --}}
@include('generales.buscarempleado')

@endsection

@section("scripts")
<script src="{{ autoVer('assets/pages/scripts/general.js') }}"></script>
{{-- buscar.js inicializa la tabla de empleados y define copiar_ced() globalmente --}}
<script src="{{ autoVer('assets/pages/scripts/empleado/buscar.js') }}"></script>
<script src="{{ autoVer('assets/pages/scripts/dashboardlaboral/index.js') }}"></script>
@endsection
