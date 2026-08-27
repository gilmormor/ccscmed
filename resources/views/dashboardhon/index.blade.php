@extends("theme.$theme.layout")

@section('titulo') Honorarios Profesionales @endsection

@section("styles")
<link rel="stylesheet" href="{{ autoVer('assets/lte/bower_components/datatables.net.button/css/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ autoVer('assets/pages/scripts/dashboardhon/index.css') }}">
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}"></script>
    <script src="{{autoVer("assets/pages/scripts/dashboardhon/index.js")}}"></script>
@endsection

@section('contenido')
<div class="dh-root" id="dh-root">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════ --}}
    <header class="dh-header">
        <div class="dh-header-titles">
            <h1 class="dh-title">Honorarios Profesionales</h1>
            <p class="dh-subtitle" id="dh-subtitle">Panel ejecutivo · Centro Clínico San Cristóbal</p>
        </div>

        <div class="dh-header-actions">
            <button type="button" class="dh-icon-btn" id="dh-btn-refresh"
                    data-tip="Actualizar información" aria-label="Actualizar información">
                <i class="fa fa-refresh"></i>
            </button>

            <div class="dh-theme-switch" role="group" aria-label="Tema de color">
                <button type="button" class="dh-theme-opt" data-theme="light" data-tip="Claro" aria-label="Tema claro">
                    <i class="fa fa-sun-o"></i>
                </button>
                <button type="button" class="dh-theme-opt" data-theme="dark" data-tip="Oscuro" aria-label="Tema oscuro">
                    <i class="fa fa-moon-o"></i>
                </button>
                <button type="button" class="dh-theme-opt is-active" data-theme="system" data-tip="Sistema" aria-label="Tema del sistema">
                    <i class="fa fa-desktop"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════
         BARRA DE FILTROS
    ═══════════════════════════════════════════════════ --}}
    <section class="dh-card dh-filters" aria-label="Filtros">
        @csrf
        <div class="dh-filters-row">

            <div class="dh-field dh-field-grow">
                <label class="dh-label">Período</label>
                <div class="dh-segmented" role="group" aria-label="Selector de período">
                    <button type="button" class="dh-seg-btn" data-periodo="hoy">Hoy</button>
                    <button type="button" class="dh-seg-btn" data-periodo="semana">Semana</button>
                    <button type="button" class="dh-seg-btn is-active" data-periodo="mes">Este mes</button>
                    <button type="button" class="dh-seg-btn" data-periodo="mesant">Mes anterior</button>
                    <button type="button" class="dh-seg-btn" data-periodo="anio">Este año</button>
                    <button type="button" class="dh-seg-btn" data-periodo="12m">12M</button>
                    <button type="button" class="dh-seg-btn" data-periodo="24m">24M</button>
                    <button type="button" class="dh-seg-btn" data-periodo="custom">
                        <i class="fa fa-calendar"></i> Personalizado
                    </button>
                </div>
            </div>

            <div class="dh-field dh-field-rango" id="dh-rango-custom">
                <label class="dh-label">Rango</label>
                <div class="dh-rango-inputs">
                    <input type="date" class="dh-input" id="dh-fecha-desde" aria-label="Fecha desde">
                    <span class="dh-rango-sep">→</span>
                    <input type="date" class="dh-input" id="dh-fecha-hasta" aria-label="Fecha hasta">
                </div>
            </div>

            <div class="dh-field">
                <label class="dh-label" for="dh-sel-medico">Médico</label>
                <select id="dh-sel-medico" class="selectpicker dh-select"
                        data-live-search="true" data-size="10" data-width="220px" title="Todos los médicos">
                    <option value="">Todos los médicos</option>
                </select>
            </div>

            <div class="dh-field">
                <label class="dh-label" for="dh-sel-conceptos">Conceptos</label>
                <select id="dh-sel-conceptos" class="selectpicker dh-select" multiple
                        data-live-search="true" data-size="10" data-width="220px"
                        data-selected-text-format="count > 1" title="Todos los conceptos">
                </select>
            </div>

            <div class="dh-field dh-field-actions">
                <button type="button" class="dh-btn dh-btn-primary" id="dh-btn-aplicar">
                    <i class="fa fa-search"></i> Aplicar
                </button>
                <button type="button" class="dh-btn dh-btn-ghost" id="dh-btn-limpiar">
                    Limpiar
                </button>
            </div>
        </div>

        <div class="dh-status" id="dh-status">
            <span class="dh-status-chip" id="dh-status-periodo">Este mes</span>
            <span id="dh-status-filtros"></span>
            <span class="dh-status-time" id="dh-status-time"></span>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════
         ESTADO INICIAL — antes de aplicar
    ═══════════════════════════════════════════════════ --}}
    <div class="dh-onboard" id="dh-onboard">
        <div class="dh-onboard-icon"><i class="fa fa-bar-chart"></i></div>
        <h2 class="dh-onboard-title">Seleccione un período y presione Aplicar</h2>
        <p class="dh-onboard-text">
            El panel no ejecuta consultas hasta que usted lo indique, para mantener la respuesta inmediata.
        </p>
        <button type="button" class="dh-btn dh-btn-primary dh-btn-lg" id="dh-btn-aplicar-2">
            <i class="fa fa-search"></i> Aplicar filtros
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════
         CUERPO
    ═══════════════════════════════════════════════════ --}}
    <div class="dh-body" id="dh-body" hidden>

        {{-- ── KPIs ── --}}
        <section class="dh-kpi-grid" aria-label="Indicadores principales">

            <article class="dh-kpi" data-kpi="asig" tabindex="0" role="button"
                     data-tip="Total de honorarios profesionales asignados en el período">
                <div class="dh-kpi-head">
                    <span class="dh-kpi-name">Honorarios Profesionales</span>
                    <span class="dh-kpi-icon dh-i-accent"><i class="fa fa-stethoscope"></i></span>
                </div>
                <div class="dh-kpi-value" id="kpi-asig-bs">—</div>
                <div class="dh-kpi-alt" id="kpi-asig-usd">—</div>
                <div class="dh-kpi-foot">
                    <span class="dh-delta" id="kpi-asig-delta"></span>
                    <canvas class="dh-spark" id="spark-asig" width="120" height="32"></canvas>
                </div>
            </article>

            <article class="dh-kpi" data-kpi="neto" tabindex="0" role="button"
                     data-tip="Honorarios menos deducciones">
                <div class="dh-kpi-head">
                    <span class="dh-kpi-name">Neto a Pagar</span>
                    <span class="dh-kpi-icon dh-i-primary"><i class="fa fa-money"></i></span>
                </div>
                <div class="dh-kpi-value" id="kpi-neto-bs">—</div>
                <div class="dh-kpi-alt" id="kpi-neto-usd">—</div>
                <div class="dh-kpi-foot">
                    <span class="dh-delta" id="kpi-neto-delta"></span>
                    <canvas class="dh-spark" id="spark-neto" width="120" height="32"></canvas>
                </div>
            </article>

            <article class="dh-kpi" data-kpi="med" tabindex="0" role="button"
                     data-tip="Médicos con honorarios en el período">
                <div class="dh-kpi-head">
                    <span class="dh-kpi-name">Médicos</span>
                    <span class="dh-kpi-icon dh-i-blue"><i class="fa fa-user-md"></i></span>
                </div>
                <div class="dh-kpi-value" id="kpi-med">—</div>
                <div class="dh-kpi-alt" id="kpi-prom-med">—</div>
                <div class="dh-kpi-foot">
                    <span class="dh-delta" id="kpi-med-delta"></span>
                    <canvas class="dh-spark" id="spark-med" width="120" height="32"></canvas>
                </div>
            </article>

            <article class="dh-kpi" data-kpi="tasa" tabindex="0" role="button"
                     data-tip="Tasa promedio BCV registrada en las nóminas del período">
                <div class="dh-kpi-head">
                    <span class="dh-kpi-name">Tasa Promedio BCV</span>
                    <span class="dh-kpi-icon dh-i-orange"><i class="fa fa-exchange"></i></span>
                </div>
                <div class="dh-kpi-value" id="kpi-tasa">—</div>
                <div class="dh-kpi-alt" id="kpi-nominas">—</div>
                <div class="dh-kpi-foot">
                    <span class="dh-delta" id="kpi-tasa-delta"></span>
                    <canvas class="dh-spark" id="spark-tasa" width="120" height="32"></canvas>
                </div>
            </article>
        </section>

        {{-- ── Gráfico principal + distribución ── --}}
        <section class="dh-grid dh-grid-main">

            <article class="dh-card dh-panel">
                <header class="dh-panel-head">
                    <div>
                        <h2 class="dh-panel-title">Evolución</h2>
                        <p class="dh-panel-sub" id="dh-evo-sub">Comparativo mensual</p>
                    </div>
                    <div class="dh-panel-tools">
                        <div class="dh-segmented dh-segmented-sm" role="group" aria-label="Métrica del gráfico">
                            <button type="button" class="dh-seg-btn is-active" data-metrica="bs">Bs</button>
                            <button type="button" class="dh-seg-btn" data-metrica="usd">USD</button>
                            <button type="button" class="dh-seg-btn" data-metrica="med">Médicos</button>
                            <button type="button" class="dh-seg-btn" data-metrica="tasa">Tasa</button>
                        </div>
                        <button type="button" class="dh-icon-btn dh-print" data-print="grafico"
                                data-canvas="chart-evolucion" data-titulo="Evolución de Honorarios"
                                data-tip="Imprimir gráfico"><i class="fa fa-print"></i></button>
                    </div>
                </header>
                <div class="dh-panel-body">
                    <div class="dh-skeleton dh-skeleton-chart" id="sk-evolucion"></div>
                    <canvas id="chart-evolucion" height="110" hidden></canvas>
                    <div class="dh-empty" id="empty-evolucion" hidden>
                        <i class="fa fa-line-chart"></i>
                        <p>Sin datos para el período seleccionado</p>
                    </div>
                </div>
            </article>

            <article class="dh-card dh-panel">
                <header class="dh-panel-head">
                    <div>
                        <h2 class="dh-panel-title">Tipo de Atención</h2>
                        <p class="dh-panel-sub">Distribución de honorarios</p>
                    </div>
                    <button type="button" class="dh-icon-btn dh-print" data-print="grafico"
                            data-canvas="chart-tipo" data-titulo="Honorarios por Tipo de Atención"
                            data-tip="Imprimir gráfico"><i class="fa fa-print"></i></button>
                </header>
                <div class="dh-panel-body">
                    <div class="dh-skeleton dh-skeleton-chart" id="sk-tipo"></div>
                    <canvas id="chart-tipo" height="150" hidden></canvas>
                    <div class="dh-legend" id="legend-tipo" hidden></div>
                    <div class="dh-empty" id="empty-tipo" hidden>
                        <i class="fa fa-pie-chart"></i>
                        <p>Sin datos de pacientes</p>
                    </div>
                </div>
            </article>
        </section>

        {{-- ── Ranking de médicos + totales por nómina ── --}}
        <section class="dh-grid dh-grid-two">

            <article class="dh-card dh-panel">
                <header class="dh-panel-head">
                    <div>
                        <h2 class="dh-panel-title">Top Médicos</h2>
                        <p class="dh-panel-sub">Por honorarios del período</p>
                    </div>
                </header>
                <div class="dh-panel-body">
                    <div class="dh-skeleton dh-skeleton-list" id="sk-medicos"></div>
                    <ol class="dh-rank" id="rank-medicos" hidden></ol>
                    <div class="dh-empty" id="empty-medicos" hidden>
                        <i class="fa fa-user-md"></i>
                        <p>Sin médicos en el período</p>
                    </div>
                </div>
            </article>

            <article class="dh-card dh-panel">
                <header class="dh-panel-head">
                    <div>
                        <h2 class="dh-panel-title">Totales por Nómina</h2>
                        <p class="dh-panel-sub">Monto global consolidado</p>
                    </div>
                    <button type="button" class="dh-icon-btn dh-print" data-print="tabla"
                            data-tabla="nomina" data-titulo="Totales por Nómina"
                            data-tip="Imprimir tabla"><i class="fa fa-print"></i></button>
                </header>
                <div class="dh-panel-body dh-panel-body-flush">
                    <div class="dh-skeleton dh-skeleton-list" id="sk-nomina"></div>
                    <div class="dh-table-wrap" id="wrap-nomina" hidden>
                        <table class="dh-table" id="tabla-nomina" style="width:100%"></table>
                    </div>
                </div>
            </article>
        </section>

        {{-- ── Conceptos ── --}}
        <section class="dh-card dh-panel">
            <header class="dh-panel-head">
                <div>
                    <h2 class="dh-panel-title">Conceptos</h2>
                    <p class="dh-panel-sub">Haga clic en una fila para ver su ficha de datos generales</p>
                </div>
                <button type="button" class="dh-icon-btn dh-print" data-print="tabla"
                        data-tabla="conceptos" data-titulo="Ranking de Conceptos"
                        data-tip="Imprimir tabla"><i class="fa fa-print"></i></button>
            </header>
            <div class="dh-panel-body dh-panel-body-flush">
                <div class="dh-skeleton dh-skeleton-list" id="sk-conceptos"></div>
                <div class="dh-table-wrap" id="wrap-conceptos" hidden>
                    <table class="dh-table" id="tabla-conceptos" style="width:100%"></table>
                </div>
            </div>
        </section>

        {{-- ── Pacientes ── --}}
        <section class="dh-card dh-panel">
            <header class="dh-panel-head">
                <div>
                    <h2 class="dh-panel-title">Relación de Pacientes</h2>
                    <p class="dh-panel-sub">Últimas 500 atenciones facturadas del período · ordenables por médico</p>
                </div>
                <button type="button" class="dh-icon-btn dh-print" data-print="tabla"
                        data-tabla="pacientes" data-titulo="Relación de Pacientes"
                        data-tip="Imprimir tabla"><i class="fa fa-print"></i></button>
            </header>
            <div class="dh-panel-body dh-panel-body-flush">
                <div class="dh-skeleton dh-skeleton-list" id="sk-pacientes"></div>
                <div class="dh-table-wrap" id="wrap-pacientes" hidden>
                    <table class="dh-table" id="tabla-pacientes" style="width:100%"></table>
                </div>
            </div>
        </section>

    </div>{{-- /dh-body --}}

    {{-- ══════════════════════════════════════════════════
         MODAL — Ficha de concepto (req. 8)
    ═══════════════════════════════════════════════════ --}}
    <div class="dh-modal" id="dh-modal-concepto" hidden>
        <div class="dh-modal-backdrop" data-close></div>
        <div class="dh-modal-box" role="dialog" aria-modal="true" aria-labelledby="dh-modal-title">
            <header class="dh-modal-head">
                <div>
                    <p class="dh-modal-eyebrow">Ficha de concepto</p>
                    <h3 class="dh-modal-title" id="dh-modal-title">—</h3>
                </div>
                <button type="button" class="dh-icon-btn" data-close aria-label="Cerrar">
                    <i class="fa fa-times"></i>
                </button>
            </header>
            <div class="dh-modal-body" id="dh-modal-body"></div>
        </div>
    </div>

    {{-- Toasts --}}
    <div class="dh-toasts" id="dh-toasts" aria-live="polite"></div>

    {{-- Tooltip --}}
    <div class="dh-tooltip" id="dh-tooltip" role="tooltip" hidden></div>
</div>
@endsection
