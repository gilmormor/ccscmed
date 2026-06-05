@extends("theme.$theme.layout")

@section('titulo')
    Portal Laboral — Mi Nómina & Honorarios
@endsection

@section("styles")
<style>
    /* ── Paleta corporativa ── */
    :root {
        --dl-primary   : #1a3a5c;
        --dl-accent    : #0d7e6e;
        --dl-warning   : #e67e22;
        --dl-info      : #2980b9;
        --dl-light     : #f4f6f9;
        --dl-card-shadow: 0 2px 12px rgba(0,0,0,0.10);
    }

    /* ── Header de bienvenida ── */
    .dl-welcome {
        background: linear-gradient(135deg, var(--dl-primary) 0%, #2c5f8a 100%);
        border-radius: 8px;
        padding: 22px 28px;
        color: #fff;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .dl-welcome .avatar {
        width: 68px; height: 68px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; flex-shrink: 0;
    }
    .dl-welcome h2 { margin: 0 0 4px; font-size: 1.4rem; font-weight: 700; }
    .dl-welcome p  { margin: 0; opacity: 0.85; font-size: 0.9rem; }
    .dl-welcome .badge-empresa {
        background: rgba(255,255,255,0.18);
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 0.78rem;
        margin-top: 6px;
        display: inline-block;
    }

    /* ── KPI Cards ── */
    .dl-kpi-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px 22px;
        box-shadow: var(--dl-card-shadow);
        border-left: 4px solid var(--dl-primary);
        margin-bottom: 20px;
        transition: transform .15s;
    }
    .dl-kpi-card:hover { transform: translateY(-2px); }
    .dl-kpi-card.accent  { border-left-color: var(--dl-accent); }
    .dl-kpi-card.warning { border-left-color: var(--dl-warning); }
    .dl-kpi-card.info    { border-left-color: var(--dl-info); }
    .dl-kpi-card .kpi-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #888;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .dl-kpi-card .kpi-value {
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--dl-primary);
        line-height: 1.1;
    }
    .dl-kpi-card.accent  .kpi-value { color: var(--dl-accent); }
    .dl-kpi-card.warning .kpi-value { color: var(--dl-warning); }
    .dl-kpi-card.info    .kpi-value { color: var(--dl-info); }
    .dl-kpi-card .kpi-sub {
        font-size: 0.78rem;
        color: #aaa;
        margin-top: 4px;
    }
    .dl-kpi-card .kpi-icon {
        font-size: 2rem;
        float: right;
        opacity: 0.15;
        margin-top: -32px;
    }

    /* ── Paneles de gráficos ── */
    .dl-panel {
        background: #fff;
        border-radius: 8px;
        box-shadow: var(--dl-card-shadow);
        margin-bottom: 22px;
    }
    .dl-panel .dl-panel-header {
        padding: 14px 20px 10px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dl-panel .dl-panel-header h4 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--dl-primary);
    }
    .dl-panel .dl-panel-body { padding: 16px 20px; }

    /* ── Tabla de documentos ── */
    .doc-row { display: flex; align-items: center; padding: 9px 0; border-bottom: 1px solid #f5f5f5; }
    .doc-row:last-child { border-bottom: none; }
    .doc-row .doc-icon { width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; }
    .doc-row .doc-info { flex: 1; }
    .doc-row .doc-info strong { display: block; font-size: 0.85rem; color: #333; }
    .doc-row .doc-info span   { font-size: 0.76rem; color: #999; }

    /* ── Spinner de carga ── */
    .dl-loading { text-align: center; padding: 30px; color: #bbb; }

    /* ── Selector de año ── */
    .dl-year-select { font-size: 0.82rem; padding: 3px 8px; border: 1px solid #ddd; border-radius: 4px; color: #555; }

    /* ── Tabs de historial ── */
    .nav-tabs-dl > li > a { font-size: 0.82rem; }
    .nav-tabs-dl > li.active > a { color: var(--dl-primary); border-bottom: 2px solid var(--dl-primary); font-weight: 600; }

    /* ── Crecimiento badge ── */
    .badge-crec { font-size: 0.8rem; padding: 3px 9px; border-radius: 20px; font-weight: 600; }
    .badge-crec.pos { background: #e8f8f5; color: #27ae60; }
    .badge-crec.neg { background: #fdf0f0; color: #e74c3c; }
    .badge-crec.neu { background: #f5f5f5; color: #888; }

    /* ── Buscador empleado (admin) ── */
    .dl-emp-search {
        background: rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 10px;
    }
    .dl-emp-search label { font-size: 0.76rem; opacity: .8; margin-bottom: 4px; display: block; }
    .dl-emp-search .input-group input {
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 6px 0 0 6px;
        font-size: 0.83rem;
        color: #333;
    }
    .dl-emp-search .input-group .btn {
        background: rgba(255,255,255,0.25);
        border: none;
        color: #fff;
        border-radius: 0 6px 6px 0;
    }
    .dl-emp-result {
        font-size: 0.78rem;
        margin-top: 6px;
        min-height: 16px;
    }
    .dl-emp-result .emp-badge {
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 2px 10px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .dl-emp-result .emp-badge .btn-clear {
        background: none; border: none; color: rgba(255,255,255,0.7);
        padding: 0; font-size: 0.85rem; cursor: pointer; line-height:1;
    }

    /* ── IA Banner ── */
    .dl-ia-banner {
        background: linear-gradient(135deg, #0d7e6e 0%, #1a3a5c 100%);
        border-radius: 8px;
        padding: 18px 22px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }
    .dl-ia-banner .ia-icon { font-size: 2rem; flex-shrink: 0; }
    .dl-ia-banner h5 { margin: 0 0 3px; font-weight: 700; }
    .dl-ia-banner p  { margin: 0; font-size: 0.83rem; opacity: 0.88; }
    .dl-ia-banner .btn-ia {
        background: rgba(255,255,255,0.2);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 20px;
        padding: 6px 18px;
        font-size: 0.82rem;
        white-space: nowrap;
        flex-shrink: 0;
        text-decoration: none;
    }
    .dl-ia-banner .btn-ia:hover { background: rgba(255,255,255,0.3); }
</style>
@endsection

@section('contenido')

{{-- ── BIENVENIDA ── --}}
<div class="dl-welcome">
    <div class="avatar">
        <i class="fa fa-user-md"></i>
    </div>
    <div style="flex:1">
        @if($nm_empleado)
            <h2>{{ ucwords(strtolower(trim($nm_empleado->emp_nom . ' ' . $nm_empleado->emp_ape))) }}</h2>
            <p>
                Cédula: <strong>V-{{ number_format($nm_empleado->emp_ced, 0, ',', '.') }}</strong>
                &nbsp;|&nbsp;
                Ingreso: <strong>{{ $nm_empleado->emp_fecing ? date('d/m/Y', strtotime($nm_empleado->emp_fecing)) : '--' }}</strong>
            </p>
            <span class="badge-empresa">
                <i class="fa fa-building-o"></i>&nbsp;{{ strtoupper(auth()->user()->name ?? 'Sistema ccscmed') }}
            </span>
        @else
            <h2>Bienvenido, {{ auth()->user()->name ?? auth()->user()->usuario }}</h2>
            <p>Portal de Gestión Laboral</p>
            {{-- Buscador de empleado para usuarios admin/no-empleado --}}
            <div class="dl-emp-search">
                <label><i class="fa fa-search"></i> Consultar empleado por cédula:</label>
                <div class="input-group" style="max-width:320px">
                    <input type="number" id="input-cedula-emp" class="form-control"
                           placeholder="Ej: 2450604" min="1">
                    <span class="input-group-btn">
                        <button class="btn" id="btn-buscar-emp" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <div class="dl-emp-result" id="emp-result-label"></div>
            </div>
        @endif
    </div>
    <div class="text-right hidden-xs" style="flex-shrink:0">
        <select id="sel-anio" class="dl-year-select">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <div style="font-size:0.72rem; opacity:.7; margin-top:4px;">Filtrar por año</div>
    </div>
</div>
{{-- cédula activa (JS la lee) --}}
<input type="hidden" id="cedula-activa" value="{{ $esEmpleado && $nm_empleado ? $nm_empleado->emp_ced : '' }}">

{{-- ── KPI CARDS ── --}}
<div class="row" id="kpi-section">
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi-card">
            <div class="kpi-label"><i class="fa fa-money"></i> Sueldo Actual</div>
            <div class="kpi-value" id="kpi-sueldo"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub" id="kpi-periodo">Último período</div>
            <i class="fa fa-money kpi-icon"></i>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi-card accent">
            <div class="kpi-label"><i class="fa fa-stethoscope"></i> Honorarios <span id="kpi-anio-lbl"></span></div>
            <div class="kpi-value" id="kpi-honorarios"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub">Total cobrado en el año</div>
            <i class="fa fa-stethoscope kpi-icon"></i>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi-card info">
            <div class="kpi-label"><i class="fa fa-file-text-o"></i> Recibos Disponibles</div>
            <div class="kpi-value" id="kpi-recibos"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub">Períodos con nómina</div>
            <i class="fa fa-file-text-o kpi-icon"></i>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="dl-kpi-card warning">
            <div class="kpi-label"><i class="fa fa-line-chart"></i> Crecimiento Salarial</div>
            <div class="kpi-value" id="kpi-crecimiento"><i class="fa fa-spinner fa-spin"></i></div>
            <div class="kpi-sub">vs año anterior</div>
            <i class="fa fa-line-chart kpi-icon"></i>
        </div>
    </div>
</div>

{{-- ── GRÁFICOS ── --}}
<div class="row">
    {{-- Evolución de ingresos --}}
    <div class="col-xs-12 col-md-8">
        <div class="dl-panel">
            <div class="dl-panel-header">
                <h4><i class="fa fa-area-chart" style="color:var(--dl-primary)"></i>&nbsp; Evolución de Ingresos — últimos períodos</h4>
                <small class="text-muted">Nómina y Honorarios</small>
            </div>
            <div class="dl-panel-body">
                <canvas id="chart-evolucion" height="110"></canvas>
                <div class="dl-loading" id="loading-evolucion" style="display:none"><i class="fa fa-spinner fa-spin"></i> Cargando...</div>
            </div>
        </div>
    </div>

    {{-- Composición del recibo --}}
    <div class="col-xs-12 col-md-4">
        <div class="dl-panel">
            <div class="dl-panel-header">
                <h4><i class="fa fa-pie-chart" style="color:var(--dl-accent)"></i>&nbsp; Composición del Recibo</h4>
                <small class="text-muted">Último período</small>
            </div>
            <div class="dl-panel-body" style="position:relative; min-height:220px">
                <canvas id="chart-composicion" height="180"></canvas>
                <div class="dl-loading" id="loading-composicion" style="display:none"><i class="fa fa-spinner fa-spin"></i></div>
                <div id="composicion-totales" class="text-center" style="margin-top:10px; font-size:0.83rem; color:#666"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── BANNER IA ── --}}
@if(Route::has('ia'))
<div class="dl-ia-banner">
    <div class="ia-icon">🤖</div>
    <div style="flex:1">
        <h5>Asistente IA de Nómina</h5>
        <p>Pregunta en lenguaje natural: "¿Cuánto recibí en honorarios en 2025?" · "¿Cuál fue mi deducción más alta?"</p>
    </div>
    <a href="{{ route('ia') }}" class="btn-ia"><i class="fa fa-external-link"></i> Abrir Asistente</a>
</div>
@endif

{{-- ── DOCUMENTOS + HISTORIAL ── --}}
<div class="row">
    {{-- Centro de documentos --}}
    <div class="col-xs-12 col-md-4">
        <div class="dl-panel">
            <div class="dl-panel-header">
                <h4><i class="fa fa-folder-open" style="color:var(--dl-warning)"></i>&nbsp; Centro de Documentos</h4>
            </div>
            <div class="dl-panel-body">
                <div class="doc-row">
                    <div class="doc-icon" style="background:#fdf0e0">
                        <i class="fa fa-file-pdf-o" style="color:var(--dl-warning)"></i>
                    </div>
                    <div class="doc-info">
                        <strong>Constancia de Trabajo</strong>
                        <span>Generada al instante con tus datos actuales</span>
                    </div>
                    <a href="{{ route('dashboardlaboral_constancia_pdf') }}" target="_blank"
                       class="btn btn-xs btn-warning" title="Descargar PDF">
                        <i class="fa fa-download"></i>
                    </a>
                </div>

                <div class="doc-row">
                    <div class="doc-icon" style="background:#e8f4ff">
                        <i class="fa fa-file-text-o" style="color:var(--dl-info)"></i>
                    </div>
                    <div class="doc-info">
                        <strong>Recibo de Nómina</strong>
                        <span>Último período disponible</span>
                    </div>
                    <button class="btn btn-xs btn-info" id="btn-ultimo-recibo" title="Ver último recibo">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>

                <div class="doc-row">
                    <div class="doc-icon" style="background:#e8f8f5">
                        <i class="fa fa-stethoscope" style="color:var(--dl-accent)"></i>
                    </div>
                    <div class="doc-info">
                        <strong>Recibo de Honorarios</strong>
                        <span>Último período disponible</span>
                    </div>
                    <button class="btn btn-xs btn-success" id="btn-ultimo-hon" title="Ver último honorario">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial con tabs --}}
    <div class="col-xs-12 col-md-8">
        <div class="dl-panel">
            <div class="dl-panel-header">
                <h4><i class="fa fa-history" style="color:var(--dl-info)"></i>&nbsp; Historial de Pagos</h4>
            </div>
            <div class="dl-panel-body" style="padding-top:0">
                <ul class="nav nav-tabs nav-tabs-dl" style="margin-bottom:14px">
                    <li class="active"><a href="#tab-nomina" data-toggle="tab">Nómina</a></li>
                    <li><a href="#tab-honorarios" data-toggle="tab">Honorarios</a></li>
                </ul>
                <div class="tab-content">
                    {{-- Tab Nómina --}}
                    <div class="tab-pane active" id="tab-nomina">
                        <table id="tabla-nomina" class="table table-hover table-condensed" style="font-size:0.83rem; width:100%">
                            <thead>
                                <tr>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th class="text-right">Sueldo</th>
                                    <th class="text-center">Recibo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    {{-- Tab Honorarios --}}
                    <div class="tab-pane" id="tab-honorarios">
                        <table id="tabla-honorarios" class="table table-hover table-condensed" style="font-size:0.83rem; width:100%">
                            <thead>
                                <tr>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th class="text-right">Total Hon.</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Recibo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')

@endsection

@section("scripts")
<script src="{{ autoVer('assets/pages/scripts/general.js') }}"></script>
<script src="{{ autoVer('assets/pages/scripts/dashboardlaboral/index.js') }}"></script>
@endsection
