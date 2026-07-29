@extends("theme.$theme.layout")

@section('titulo') Dashboard Administrativo — Nómina @endsection

@section("styles")
<link rel="stylesheet" href="{{ autoVer('assets/lte/bower_components/datatables.net.button/css/buttons.dataTables.min.css') }}">
<style>
/* ═══════════════════════════════════════════════════
   DASHBOARD ADMIN — Variables y reset
═══════════════════════════════════════════════════ */
:root {
    --da-primary  : #1a3a5c;
    --da-accent   : #0d7e6e;
    --da-blue     : #2471a3;
    --da-orange   : #ca6f1e;
    --da-red      : #c0392b;
    --da-purple   : #7d3c98;
    --da-shadow   : 0 2px 10px rgba(0,0,0,0.08);
    --da-radius   : 8px;
    --da-bg       : #f0f3f7;
}
.content-wrapper { background: var(--da-bg) !important; }

/* ── Barra de filtros ── */
.da-filter-bar {
    background: #fff;
    border-radius: var(--da-radius);
    box-shadow: var(--da-shadow);
    padding: 14px 18px;
    margin-bottom: 20px;
}
.da-filter-bar .da-filter-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #aaa; margin-bottom: 7px;
}
.da-period-btn {
    font-size: 12px !important; padding: 4px 10px !important;
    border-radius: 4px !important;
}
.da-period-btn.active {
    background: var(--da-primary) !important;
    border-color: var(--da-primary) !important;
    color: #fff !important;
}
.da-filter-bar .form-control {
    font-size: 13px; height: 32px; padding: 4px 10px;
}
#da-rango-custom { display: none; }
.da-filter-sep { border-left: 1px solid #eee; margin: 0 12px; }
.btn-da-aplicar {
    background: var(--da-primary); color: #fff; border: none;
    border-radius: 5px; padding: 5px 16px; font-size: 13px;
    font-weight: 600;
}
.btn-da-aplicar:hover { background: #14304d; color: #fff; }
.btn-da-limpiar {
    background: #f4f6f9; color: #666; border: 1px solid #ddd;
    border-radius: 5px; padding: 5px 12px; font-size: 13px;
}

/* ── Barra de estado ── */
.da-status-bar {
    display: flex; align-items: center; gap: 14px;
    font-size: 12px; color: #888; margin-bottom: 16px;
    flex-wrap: wrap;
}
.da-status-bar .da-badge {
    background: rgba(26,58,92,.08); color: var(--da-primary);
    border-radius: 20px; padding: 2px 10px; font-weight: 600;
}
.da-status-bar .da-last-upd { margin-left: auto; font-size: 11px; color: #bbb; }

/* ── KPI Cards ── */
.da-kpi {
    background: #fff;
    border-radius: var(--da-radius);
    box-shadow: var(--da-shadow);
    padding: 16px 18px;
    margin-bottom: 18px;
    display: flex; align-items: center; gap: 14px;
    border-left: 4px solid var(--da-primary);
    transition: transform .12s, box-shadow .12s;
    position: relative; overflow: hidden;
}
.da-kpi:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.11); }
.da-kpi .da-kpi-ico {
    width: 46px; height: 46px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0; background: rgba(26,58,92,.08); color: var(--da-primary);
}
.da-kpi .da-kpi-body { flex: 1; min-width: 0; }
.da-kpi .da-kpi-val  { font-size: 1.3rem; font-weight: 700; color: var(--da-primary); line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.da-kpi .da-kpi-lbl  { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: #aaa; font-weight: 600; margin-top: 2px; }
.da-kpi .da-kpi-sub  { font-size: .72rem; color: #bbb; margin-top: 2px; }
/* Variantes de color */
.da-kpi.accent  { border-left-color: var(--da-accent);  }
.da-kpi.accent  .da-kpi-ico { background: rgba(13,126,110,.1); color: var(--da-accent); }
.da-kpi.accent  .da-kpi-val { color: var(--da-accent); }
.da-kpi.blue    { border-left-color: var(--da-blue);    }
.da-kpi.blue    .da-kpi-ico { background: rgba(36,113,163,.1); color: var(--da-blue); }
.da-kpi.blue    .da-kpi-val { color: var(--da-blue); }
.da-kpi.red     { border-left-color: var(--da-red);     }
.da-kpi.red     .da-kpi-ico { background: rgba(192,57,43,.1); color: var(--da-red); }
.da-kpi.red     .da-kpi-val { color: var(--da-red); }
.da-kpi.orange  { border-left-color: var(--da-orange);  }
.da-kpi.orange  .da-kpi-ico { background: rgba(202,111,30,.1); color: var(--da-orange); }
.da-kpi.orange  .da-kpi-val { color: var(--da-orange); }
.da-kpi.purple  { border-left-color: var(--da-purple);  }
.da-kpi.purple  .da-kpi-ico { background: rgba(125,60,152,.1); color: var(--da-purple); }
.da-kpi.purple  .da-kpi-val { color: var(--da-purple); }

/* ── Paneles de gráficos ── */
.da-panel {
    background: #fff;
    border-radius: var(--da-radius);
    box-shadow: var(--da-shadow);
    margin-bottom: 20px;
}
.da-panel-hd {
    padding: 12px 18px 10px;
    border-bottom: 1px solid #f2f2f2;
    display: flex; align-items: center; justify-content: space-between;
}
.da-panel-hd h4 { margin: 0; font-size: 13px; font-weight: 700; color: var(--da-primary); }
.da-panel-hd .da-panel-sub { font-size: 11px; color: #bbb; }
.da-btn-panel-print {
    background: none; border: 1px solid #ddd; border-radius: 5px;
    padding: 4px 9px; cursor: pointer; color: #aaa; font-size: 13px;
    transition: all .15s; white-space: nowrap; flex-shrink: 0; margin-left: 10px;
    line-height: 1.4;
}
.da-btn-panel-print:hover { color: #1a3a5c; border-color: #1a3a5c; background: #f0f4f8; }
.da-panel-bd { padding: 14px 18px; }
.da-spinner { text-align: center; padding: 30px; color: #ccc; font-size: .85rem; }

/* ── Tablas ── */
.da-panel .table th { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #888; font-weight: 600; border-top: none; }
.da-panel .table td { font-size: 12px; vertical-align: middle; }
.da-panel .table-hover tbody tr:hover { background: #f8fbff; }
.badge-tipo-a { background: rgba(13,126,110,.12); color: var(--da-accent); padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-tipo-d { background: rgba(192,57,43,.1);  color: var(--da-red);    padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input { font-size: 12px; }
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate { font-size: 12px; }

/* ── Separador de sección ── */
.da-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #aaa;
    margin: 4px 0 14px; display: flex; align-items: center; gap: 8px;
}
.da-section-title::after {
    content: ''; flex: 1; height: 1px; background: #e8ecf0;
}

/* ── Donut legend custom ── */
.da-donut-legend { margin-top: 10px; }
.da-donut-legend-item {
    display: flex; align-items: center; gap: 7px;
    font-size: 12px; color: #555; margin-bottom: 5px;
}
.da-donut-legend-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}

@media(max-width: 767px) {
    .da-filter-bar .row > div { margin-bottom: 8px; }
    .da-kpi .da-kpi-val { font-size: 1.1rem; }
}
/* ── Print ── */
@media print {
    .da-filter-bar, .da-status-bar,
    .main-header, .main-sidebar, .control-sidebar,
    .content-header, .breadcrumb,
    .dt-buttons, .dataTables_filter, .dataTables_length,
    .dataTables_paginate, .dataTables_info { display: none !important; }

    .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
    .da-panel { box-shadow: none !important; border: 1px solid #ccc !important; }
    .da-section-title { color: #333 !important; }

    canvas { max-width: 100% !important; }

    table { page-break-inside: avoid; }
    .col-xs-12, .col-md-12, .col-md-8, .col-md-4,
    .col-md-6, .col-md-3, .col-md-2 { width: 100% !important; float: none !important; }

    .row { display: block !important; }
    .da-kpi-card { display: inline-block !important; width: 30% !important; margin: 4px !important; }
}
</style>
@endsection

@section('contenido')

{{-- ══════════════════════════════════════════════════════
     BARRA DE FILTROS
══════════════════════════════════════════════════════ --}}
<div class="da-filter-bar">
    <div class="row">

        {{-- Período --}}
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="da-filter-title"><i class="fa fa-calendar-o"></i> Período</div>
            <div class="btn-group btn-group-sm" role="group" id="da-period-group">
                <button type="button" class="btn btn-default da-period-btn active" data-periodo="mes">Mes</button>
                <button type="button" class="btn btn-default da-period-btn" data-periodo="3m">3M</button>
                <button type="button" class="btn btn-default da-period-btn" data-periodo="12m">12M</button>
                <button type="button" class="btn btn-default da-period-btn" data-periodo="anio">Año</button>
                <button type="button" class="btn btn-default da-period-btn" data-periodo="custom">
                    <i class="fa fa-calendar"></i> Rango
                </button>
            </div>
            <div id="da-rango-custom" style="margin-top:8px">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">Desde</span>
                    <input type="date" id="da-fecha-desde" class="form-control" style="max-width:130px">
                    <span class="input-group-addon">Hasta</span>
                    <input type="date" id="da-fecha-hasta" class="form-control" style="max-width:130px">
                </div>
            </div>
        </div>

        {{-- Trabajador --}}
        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="da-filter-title"><i class="fa fa-user-o"></i> Trabajador</div>
            <select id="da-sel-trabajador" class="form-control selectpicker"
                    data-live-search="true" data-size="8"
                    data-none-selected-text="Todos los trabajadores">
                <option value="">Todos</option>
            </select>
        </div>

        {{-- Concepto --}}
        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="da-filter-title"><i class="fa fa-tags"></i> Conceptos</div>
            <select id="da-sel-conceptos" class="form-control selectpicker"
                    multiple data-live-search="true" data-size="8"
                    data-none-selected-text="Todos los conceptos"
                    data-selected-text-format="count > 2">
            </select>
        </div>

        {{-- Acciones --}}
        <div class="col-xs-12 col-sm-6 col-md-2">
            <div class="da-filter-title">&nbsp;</div>
            <div style="display:flex; gap:6px; margin-top:4px; flex-wrap:wrap">
                <button class="btn-da-aplicar" id="da-btn-aplicar">
                    <i class="fa fa-search"></i> Aplicar
                </button>
                <button class="btn-da-limpiar" id="da-btn-limpiar">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Barra de estado --}}
<div class="da-status-bar" id="da-status-bar">
    <span><i class="fa fa-circle" style="color:#0d7e6e; font-size:8px"></i> Dashboard Administrativo</span>
    <span class="da-badge" id="da-status-periodo">Últimos 12 meses</span>
    <span id="da-status-filtros"></span>
    <span class="da-last-upd" id="da-status-time"></span>
</div>


{{-- ══════════════════════════════════════════════════════
     KPI CARDS — FILA 1
══════════════════════════════════════════════════════ --}}
<div class="da-section-title"><i class="fa fa-tachometer"></i> Indicadores Clave</div>
<div class="row">

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="da-kpi accent">
            <div class="da-kpi-ico"><i class="fa fa-money"></i></div>
            <div class="da-kpi-body">
                <div class="da-kpi-val" id="kpi-asig-bs"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="da-kpi-lbl">Asig. Total Bs</div>
                <div class="da-kpi-sub">Asignaciones</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="da-kpi blue">
            <div class="da-kpi-ico"><i class="fa fa-dollar"></i></div>
            <div class="da-kpi-body">
                <div class="da-kpi-val" id="kpi-asig-usd"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="da-kpi-lbl">Asig. Total USD</div>
                <div class="da-kpi-sub">Asignaciones</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="da-kpi red">
            <div class="da-kpi-ico"><i class="fa fa-arrow-down"></i></div>
            <div class="da-kpi-body">
                <div class="da-kpi-val" id="kpi-ded-bs"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="da-kpi-lbl">Deducciones Bs</div>
                <div class="da-kpi-sub">Total deducido</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="da-kpi">
            <div class="da-kpi-ico"><i class="fa fa-balance-scale"></i></div>
            <div class="da-kpi-body">
                <div class="da-kpi-val" id="kpi-neto-bs"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="da-kpi-lbl">Neto Pagado Bs</div>
                <div class="da-kpi-sub">Asig − Ded</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="da-kpi orange">
            <div class="da-kpi-ico"><i class="fa fa-users"></i></div>
            <div class="da-kpi-body">
                <div class="da-kpi-val" id="kpi-trab"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="da-kpi-lbl">Trabajadores</div>
                <div class="da-kpi-sub" id="kpi-prom-trab">Prom. —</div>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="da-kpi purple">
            <div class="da-kpi-ico"><i class="fa fa-exchange"></i></div>
            <div class="da-kpi-body">
                <div class="da-kpi-val" id="kpi-tasa"><i class="fa fa-spinner fa-spin"></i></div>
                <div class="da-kpi-lbl">Tasa Prom. $</div>
                <div class="da-kpi-sub" id="kpi-periodos-sub">— períodos</div>
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════
     GRÁFICOS — FILA 1: Evolución + Distribución
══════════════════════════════════════════════════════ --}}
<div class="da-section-title"><i class="fa fa-bar-chart"></i> Análisis Financiero</div>
<div class="row">

    <div class="col-xs-12 col-md-8">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-area-chart" style="color:var(--da-primary)"></i>&nbsp; Evolución Mensual</h4><span class="da-panel-sub">Asignaciones · Deducciones · Neto — por mes</span></div>
                <button class="da-btn-panel-print" data-print="grafico" data-canvas="chart-evolucion" data-titulo="Evolución Mensual" title="Imprimir"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd">
                <div class="da-spinner" id="sp-evolucion"><i class="fa fa-spinner fa-spin"></i> Cargando...</div>
                <canvas id="chart-evolucion" height="110" style="display:none"></canvas>
                <p id="empty-evolucion" style="display:none;text-align:center;color:#ccc;padding:30px 0;margin:0">Sin datos en el período seleccionado.</p>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-4">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-pie-chart" style="color:var(--da-accent)"></i>&nbsp; Asig. vs Deducciones</h4><span class="da-panel-sub">Distribución en Bs</span></div>
                <button class="da-btn-panel-print" data-print="grafico" data-canvas="chart-distribucion" data-titulo="Asig. vs Deducciones" title="Imprimir"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd" style="min-height:200px;position:relative">
                <div class="da-spinner" id="sp-distribucion"><i class="fa fa-spinner fa-spin"></i></div>
                <canvas id="chart-distribucion" style="display:none" height="180"></canvas>
                <div id="da-donut-legend" class="da-donut-legend" style="display:none"></div>
                <p id="empty-distribucion" style="display:none;text-align:center;color:#ccc;padding:24px 0;margin:0">Sin datos.</p>
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════
     GRÁFICOS — FILA 2: Top Conceptos + Top Trabajadores
══════════════════════════════════════════════════════ --}}
<div class="row">

    <div class="col-xs-12 col-md-6">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-list-ol" style="color:var(--da-orange)"></i>&nbsp; Top 10 Conceptos</h4><span class="da-panel-sub">Mayor monto acumulado en Bs</span></div>
                <button class="da-btn-panel-print" data-print="grafico" data-canvas="chart-top-conceptos" data-titulo="Top 10 Conceptos" title="Imprimir"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd">
                <div class="da-spinner" id="sp-top-conceptos"><i class="fa fa-spinner fa-spin"></i></div>
                <canvas id="chart-top-conceptos" style="display:none" height="220"></canvas>
                <p id="empty-top-conceptos" style="display:none;text-align:center;color:#ccc;padding:24px 0;margin:0">Sin datos.</p>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-6">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-trophy" style="color:var(--da-blue)"></i>&nbsp; Top 10 Trabajadores</h4><span class="da-panel-sub">Mayor asignación acumulada en Bs</span></div>
                <button class="da-btn-panel-print" data-print="grafico" data-canvas="chart-top-trab" data-titulo="Top 10 Trabajadores" title="Imprimir"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd">
                <div class="da-spinner" id="sp-top-trab"><i class="fa fa-spinner fa-spin"></i></div>
                <canvas id="chart-top-trab" style="display:none" height="220"></canvas>
                <p id="empty-top-trab" style="display:none;text-align:center;color:#ccc;padding:24px 0;margin:0">Sin datos.</p>
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════
     GRÁFICOS — FILA 3: Comparativo Bs/USD + Tasa Dólar
══════════════════════════════════════════════════════ --}}
<div class="da-section-title"><i class="fa fa-line-chart"></i> Análisis de Divisas</div>
<div class="row">

    <div class="col-xs-12 col-md-8">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-exchange" style="color:var(--da-blue)"></i>&nbsp; Comparativo Bs vs USD</h4><span class="da-panel-sub">Asignaciones mensuales en ambas monedas</span></div>
                <button class="da-btn-panel-print" data-print="grafico" data-canvas="chart-comparativo" data-titulo="Comparativo Bs vs USD" title="Imprimir"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd">
                <div class="da-spinner" id="sp-comparativo"><i class="fa fa-spinner fa-spin"></i></div>
                <canvas id="chart-comparativo" style="display:none" height="110"></canvas>
                <p id="empty-comparativo" style="display:none;text-align:center;color:#ccc;padding:30px 0;margin:0">Sin datos.</p>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-4">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-dollar" style="color:var(--da-orange)"></i>&nbsp; Evolución Tasa $</h4><span class="da-panel-sub">Tasa de cambio en nómina</span></div>
                <button class="da-btn-panel-print" data-print="grafico" data-canvas="chart-dolar" data-titulo="Evolución Tasa $" title="Imprimir"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd" style="min-height:200px;position:relative">
                <div class="da-spinner" id="sp-dolar"><i class="fa fa-spinner fa-spin"></i></div>
                <canvas id="chart-dolar" style="display:none" height="180"></canvas>
                <p id="empty-dolar" style="display:none;text-align:center;color:#ccc;padding:24px 0;margin:0">Sin datos.</p>
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════
     TABLAS — Movimientos + Ranking
══════════════════════════════════════════════════════ --}}
<div class="da-section-title"><i class="fa fa-table"></i> Detalle</div>
<div class="row">

    <div class="col-xs-12 col-md-12">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-history" style="color:var(--da-primary)"></i>&nbsp; Últimos Movimientos</h4><span class="da-panel-sub">Hasta 200 registros más recientes</span></div>
                <button class="da-btn-panel-print" data-print="tabla" data-tabla="mov" data-titulo="Últimos Movimientos" title="Imprimir tabla"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd" style="padding-top:8px">
                <table id="da-tabla-mov" class="table table-hover table-condensed" style="width:100%">
                    <thead>
                        <tr>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>C.I.</th>
                            <th>Trabajador</th>
                            <th>Concepto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-right">Monto</th>
                            <th class="text-right">Bs ME</th>
                            <th class="text-right">USD</th>
                            <th style="display:none"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-12">
        <div class="da-panel">
            <div class="da-panel-hd">
                <div><h4><i class="fa fa-sort-amount-desc" style="color:var(--da-accent)"></i>&nbsp; Ranking Conceptos</h4><span class="da-panel-sub">Total acumulado por concepto</span></div>
                <button class="da-btn-panel-print" data-print="tabla" data-tabla="ranking" data-titulo="Ranking Conceptos" title="Imprimir tabla"><i class="fa fa-print"></i></button>
            </div>
            <div class="da-panel-bd" style="padding-top:8px">
                <table id="da-tabla-ranking" class="table table-hover table-condensed" style="width:100%">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">#</th>
                            <th class="text-right">Bs</th>
                            <th class="text-right">BsMe</th>
                            <th class="text-right">$</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@section("scripts")
<script src="{{ autoVer('assets/pages/scripts/general.js') }}"></script>
<script src="{{ autoVer('assets/lte/bower_components/datatables.net.button/js/jszip.min.js') }}"></script>
<script src="{{ autoVer('assets/lte/bower_components/datatables.net.button/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ autoVer('assets/lte/bower_components/datatables.net.button/js/buttons.html5.min.js') }}"></script>
<script src="{{ autoVer('assets/lte/bower_components/datatables.net.button/js/buttons.print.min.js') }}"></script>
<script src="{{ autoVer('assets/pages/scripts/dashboardadmin/index.js') }}"></script>
@endsection
