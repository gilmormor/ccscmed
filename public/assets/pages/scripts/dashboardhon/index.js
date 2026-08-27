/* ==================================================================
   DASHBOARD HONORARIOS PROFESIONALES
   ccscmed · Chart.js v2.8 + DataTables + jQuery
   ------------------------------------------------------------------
   Ninguna consulta se ejecuta al cargar: el usuario debe pulsar Aplicar.
   Los montos llegan del backend en positivo; el signo se deriva del tipo.
   ================================================================== */

$(document).ready(function () {

    /* ────────────────────────────────────────────
       ESTADO
    ──────────────────────────────────────────── */
    var state = {
        periodo     : 'mes',
        fecha_desde : '',
        fecha_hasta : '',
        emp_ced     : '',
        conceptos   : ''
    };

    var metricaEvo = 'bs';
    var charts     = {};
    var tablas     = { nomina: null, conceptos: null, pacientes: null };
    var cacheEvo   = [];
    var yaAplico   = false;

    /* ────────────────────────────────────────────
       FORMATO
    ──────────────────────────────────────────── */
    function nf(v, dec) {
        return (parseFloat(v) || 0).toLocaleString('es-VE', {
            minimumFractionDigits: dec === undefined ? 2 : dec,
            maximumFractionDigits: dec === undefined ? 2 : dec
        });
    }
    function fmtBs(v)   { return 'Bs ' + nf(v); }
    function fmtUsd(v)  { return '$ '  + nf(v); }
    function fmtInt(v)  { return nf(v, 0); }

    /** Abrevia montos grandes para ejes y tarjetas: 1.250.000 → 1,25 M */
    function fmtCorto(v, pre) {
        v = parseFloat(v) || 0;
        pre = pre || '';
        var abs = Math.abs(v);
        if (abs >= 1e9) return pre + nf(v / 1e9, 2) + ' MM';
        if (abs >= 1e6) return pre + nf(v / 1e6, 2) + ' M';
        if (abs >= 1e3) return pre + nf(v / 1e3, 1) + ' K';
        return pre + nf(v, 0);
    }
    function abreviar(s, max) {
        s = s || '';
        max = max || 26;
        return s.length > max ? s.substring(0, max) + '…' : s;
    }
    function escapar(s) {
        return $('<div>').text(s == null ? '' : s).html();
    }

    /* ────────────────────────────────────────────
       TOKENS DE COLOR (leídos del CSS para respetar el tema)
    ──────────────────────────────────────────── */
    function token(nombre) {
        return getComputedStyle(document.getElementById('dh-root'))
            .getPropertyValue(nombre).trim();
    }
    function paleta() {
        return {
            accent : token('--dh-accent')  || '#0d7e6e',
            primary: token('--dh-primary') || '#1a3a5c',
            blue   : token('--dh-blue')    || '#2471a3',
            orange : token('--dh-orange')  || '#ca6f1e',
            red    : token('--dh-red')     || '#c0392b',
            text   : token('--dh-text-2')  || '#56637a',
            text3  : token('--dh-text-3')  || '#8b96a9',
            grid   : token('--dh-grid')    || 'rgba(0,0,0,.06)',
            surface: token('--dh-surface') || '#fff'
        };
    }
    function alpha(hex, a) {
        hex = (hex || '').replace('#', '');
        if (hex.length !== 6) return 'rgba(13,126,110,' + a + ')';
        return 'rgba(' + parseInt(hex.substr(0,2),16) + ','
                       + parseInt(hex.substr(2,2),16) + ','
                       + parseInt(hex.substr(4,2),16) + ',' + a + ')';
    }

    var dtES = {
        processing  : 'Procesando…',      search: '',
        searchPlaceholder: 'Buscar…',     lengthMenu: 'Mostrar _MENU_',
        info        : '_START_–_END_ de _TOTAL_',
        infoEmpty   : '0 registros',      infoFiltered: '(de _MAX_)',
        zeroRecords : 'Sin resultados',   emptyTable: 'Sin datos',
        paginate    : { first:'«', previous:'‹', next:'›', last:'»' }
    };

    /* ────────────────────────────────────────────
       TEMA
    ──────────────────────────────────────────── */
    var LS_TEMA = 'dh-tema';

    function aplicarTema(modo) {
        if (modo === 'system') {
            document.documentElement.removeAttribute('data-dh-theme');
        } else {
            document.documentElement.setAttribute('data-dh-theme', modo);
        }
        $('.dh-theme-opt').removeClass('is-active')
            .filter('[data-theme="' + modo + '"]').addClass('is-active');
        try { localStorage.setItem(LS_TEMA, modo); } catch (e) { /* modo privado */ }

        // Los gráficos toman color de los tokens: hay que redibujarlos
        if (yaAplico) { redibujarGraficos(); }
    }

    (function initTema() {
        var guardado = 'system';
        try { guardado = localStorage.getItem(LS_TEMA) || 'system'; } catch (e) {}
        aplicarTema(guardado);
    })();

    $('.dh-theme-opt').on('click', function () {
        aplicarTema($(this).data('theme'));
    });

    /* ────────────────────────────────────────────
       TOOLTIP
    ──────────────────────────────────────────── */
    var $tip = $('#dh-tooltip');

    $(document).on('mouseenter focus', '.dh-root [data-tip]', function () {
        var texto = $(this).data('tip');
        if (!texto) return;
        var r = this.getBoundingClientRect();
        $tip.text(texto).prop('hidden', false);
        var ancho = $tip.outerWidth();
        var izq = Math.min(
            Math.max(8, r.left + r.width / 2 - ancho / 2),
            window.innerWidth - ancho - 8
        );
        var arriba = r.top - $tip.outerHeight() - 8;
        if (arriba < 8) arriba = r.bottom + 8;
        $tip.css({ left: izq + 'px', top: arriba + 'px' }).addClass('is-visible');
    });

    $(document).on('mouseleave blur', '.dh-root [data-tip]', function () {
        $tip.removeClass('is-visible');
        setTimeout(function () {
            if (!$tip.hasClass('is-visible')) $tip.prop('hidden', true);
        }, 160);
    });

    /* ────────────────────────────────────────────
       TOASTS
    ──────────────────────────────────────────── */
    function toast(mensaje, tipo) {
        tipo = tipo || 'ok';
        var iconos = { ok: 'fa-check-circle', error: 'fa-exclamation-circle', warn: 'fa-exclamation-triangle' };
        var $t = $('<div class="dh-toast is-' + tipo + '">' +
                   '<i class="fa ' + (iconos[tipo] || iconos.ok) + '"></i>' +
                   '<span>' + escapar(mensaje) + '</span></div>');
        $('#dh-toasts').append($t);
        setTimeout(function () {
            $t.addClass('is-out');
            setTimeout(function () { $t.remove(); }, 240);
        }, 3200);
    }

    /* ────────────────────────────────────────────
       QUERYSTRING
    ──────────────────────────────────────────── */
    function qs() {
        var p = { periodo: state.periodo };
        if (state.periodo === 'custom') {
            if (state.fecha_desde) p.fecha_desde = state.fecha_desde;
            if (state.fecha_hasta) p.fecha_hasta = state.fecha_hasta;
        }
        if (state.emp_ced)   p.emp_ced   = state.emp_ced;
        if (state.conceptos) p.conceptos = state.conceptos;
        return '?' + $.param(p);
    }

    /* ────────────────────────────────────────────
       HELPERS DE PANEL
    ──────────────────────────────────────────── */
    function cargando(skeleton, ocultar) {
        $(skeleton).show();
        $(ocultar).prop('hidden', true);
    }
    function destruir(clave) {
        if (charts[clave]) { charts[clave].destroy(); charts[clave] = null; }
    }

    /* ==================================================================
       1. KPIs
       ================================================================== */
    function pintarDelta($el, valor) {
        if (valor === null || valor === undefined) {
            $el.attr('class', 'dh-delta is-flat').text('sin comparativo');
            return;
        }
        var arriba = valor > 0, plano = Math.abs(valor) < 0.05;
        var cls = plano ? 'is-flat' : (arriba ? 'is-up' : 'is-down');
        var ico = plano ? 'fa-minus' : (arriba ? 'fa-arrow-up' : 'fa-arrow-down');
        $el.attr('class', 'dh-delta ' + cls)
           .html('<i class="fa ' + ico + '"></i> ' + nf(Math.abs(valor), 1) + '%');
    }

    function cargarKpis() {
        var $v = $('#kpi-asig-bs,#kpi-neto-bs,#kpi-med,#kpi-tasa');
        $v.html('<i class="fa fa-spinner fa-spin" style="font-size:16px;opacity:.4"></i>');

        return $.get('/dashboardhon/kpis' + qs(), function (r) {
            $('#kpi-asig-bs').text(fmtBs(r.asig_bs));
            $('#kpi-asig-usd').text(fmtUsd(r.asig_usd) + ' · ' + fmtInt(r.total_mov) + ' movimientos');

            $('#kpi-neto-bs').text(fmtBs(r.neto_bs));
            $('#kpi-neto-usd').text(fmtUsd(r.neto_usd) + ' · deducciones ' + fmtBs(r.ded_bs));

            $('#kpi-med').text(fmtInt(r.total_med));
            $('#kpi-prom-med').text('Promedio ' + fmtBs(r.prom_por_med) + ' c/u');

            $('#kpi-tasa').text(fmtBs(r.tasa_avg));
            $('#kpi-nominas').text(fmtInt(r.total_nominas) + ' nóminas · ' +
                                   fmtInt(r.total_conceptos) + ' conceptos');

            pintarDelta($('#kpi-asig-delta'), r.var_asig);
            pintarDelta($('#kpi-neto-delta'), r.var_neto);
            pintarDelta($('#kpi-med-delta'),  r.var_med);
            pintarDelta($('#kpi-tasa-delta'), r.var_tasa);
        }).fail(function () {
            $v.text('—');
            toast('No se pudieron cargar los indicadores', 'error');
        });
    }

    /* ==================================================================
       2. Sparklines
       ================================================================== */
    function sparkline(canvasId, datos, color) {
        destruir(canvasId);
        var el = document.getElementById(canvasId);
        if (!el || !datos || datos.length < 2) return;

        charts[canvasId] = new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels: datos.map(function (_, i) { return i; }),
                datasets: [{
                    data: datos,
                    borderColor: color,
                    backgroundColor: alpha(color, .12),
                    fill: true, borderWidth: 1.6, pointRadius: 0, tension: .35
                }]
            },
            options: {
                responsive: false,
                legend  : { display: false },
                tooltips: { enabled: false },
                scales  : {
                    xAxes: [{ display: false }],
                    yAxes: [{ display: false }]
                },
                layout: { padding: 1 }
            }
        });
    }

    function cargarSparklines() {
        return $.get('/dashboardhon/sparklines' + qs(), function (r) {
            var c = paleta();
            sparkline('spark-asig', r.asig, c.accent);
            sparkline('spark-neto', r.neto, c.primary);
            sparkline('spark-med',  r.med,  c.blue);
            sparkline('spark-tasa', r.tasa, c.orange);
        });
    }

    /* ==================================================================
       3. Evolución (métrica conmutable)
       ================================================================== */
    var METRICAS = {
        bs   : { titulo: 'Honorarios y deducciones en bolívares', fmt: fmtBs,  corto: function (v) { return fmtCorto(v, 'Bs '); } },
        usd  : { titulo: 'Honorarios y deducciones en dólares',   fmt: fmtUsd, corto: function (v) { return fmtCorto(v, '$ '); } },
        med  : { titulo: 'Médicos con honorarios por mes',        fmt: fmtInt, corto: function (v) { return fmtInt(v); } },
        tasa : { titulo: 'Tasa promedio BCV por mes',             fmt: fmtBs,  corto: function (v) { return fmtCorto(v, 'Bs '); } }
    };

    function dibujarEvolucion() {
        destruir('evolucion');
        var rows = cacheEvo;
        if (!rows.length) { $('#empty-evolucion').prop('hidden', false); return; }

        var c = paleta();
        var m = METRICAS[metricaEvo];
        $('#dh-evo-sub').text(m.titulo);

        var labels = rows.map(function (x) { return x.mes_label; });
        var sets;

        if (metricaEvo === 'bs' || metricaEvo === 'usd') {
            var kA = metricaEvo === 'bs' ? 'asig_bs' : 'asig_usd';
            var kD = metricaEvo === 'bs' ? 'ded_bs'  : 'ded_usd';
            var asig = rows.map(function (x) { return parseFloat(x[kA]) || 0; });
            var ded  = rows.map(function (x) { return parseFloat(x[kD]) || 0; });
            var neto = rows.map(function (_, i) { return asig[i] - ded[i]; });

            sets = [
                { label: 'Honorarios Profesionales', data: asig,
                  backgroundColor: alpha(c.accent, .82), borderColor: c.accent, borderWidth: 0 },
                { label: 'Deducciones', data: ded,
                  backgroundColor: alpha(c.red, .70), borderColor: c.red, borderWidth: 0 },
                { label: 'Neto', data: neto, type: 'line',
                  borderColor: c.primary, backgroundColor: 'transparent',
                  fill: false, tension: .35, borderWidth: 2,
                  pointRadius: 3, pointBackgroundColor: c.primary, pointBorderColor: c.surface,
                  pointBorderWidth: 1.5 }
            ];
        } else if (metricaEvo === 'med') {
            sets = [{ label: 'Médicos',
                      data: rows.map(function (x) { return parseInt(x.med) || 0; }),
                      backgroundColor: alpha(c.blue, .80), borderColor: c.blue, borderWidth: 0 }];
        } else {
            sets = [{ label: 'Tasa promedio BCV', type: 'line',
                      data: rows.map(function (x) { return parseFloat(x.tasa_avg) || 0; }),
                      borderColor: c.orange, backgroundColor: alpha(c.orange, .10),
                      fill: true, tension: .35, borderWidth: 2,
                      pointRadius: 3, pointBackgroundColor: c.orange }];
        }

        var el = document.getElementById('chart-evolucion');
        $('#box-evolucion').prop('hidden', false);

        charts.evolucion = new Chart(el.getContext('2d'), {
            type: metricaEvo === 'tasa' ? 'line' : 'bar',
            data: { labels: labels, datasets: sets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'top', align: 'end',
                    labels: { fontSize: 11, fontColor: c.text, padding: 14,
                              usePointStyle: true, boxWidth: 7 }
                },
                tooltips: {
                    mode: 'index', intersect: false,
                    backgroundColor: c.surface, titleFontColor: c.text,
                    bodyFontColor: c.text, borderColor: c.grid, borderWidth: 1,
                    cornerRadius: 8, xPadding: 12, yPadding: 10, displayColors: true,
                    callbacks: {
                        label: function (item, data) {
                            return ' ' + data.datasets[item.datasetIndex].label + ': ' + m.fmt(item.yLabel);
                        }
                    }
                },
                hover: { mode: 'index', intersect: false },
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, fontSize: 10, fontColor: c.text3,
                                 callback: function (v) { return m.corto(v); } },
                        gridLines: { color: c.grid, drawBorder: false, zeroLineColor: c.grid }
                    }],
                    xAxes: [{
                        ticks: { fontSize: 10, fontColor: c.text3, maxRotation: 0, autoSkipPadding: 12 },
                        gridLines: { display: false, drawBorder: false }
                    }]
                }
            }
        });
    }

    function cargarEvolucion() {
        cargando('#sk-evolucion', '#box-evolucion,#empty-evolucion');
        destruir('evolucion');

        return $.get('/dashboardhon/evolucion' + qs(), function (r) {
            $('#sk-evolucion').hide();
            cacheEvo = r.data || [];
            dibujarEvolucion();
        }).fail(function () {
            $('#sk-evolucion').hide();
            $('#empty-evolucion').prop('hidden', false);
        });
    }

    $('.dh-segmented-sm .dh-seg-btn').on('click', function () {
        $(this).addClass('is-active').siblings().removeClass('is-active');
        metricaEvo = $(this).data('metrica');
        dibujarEvolucion();
    });

    /* ==================================================================
       4. Distribución por tipo de atención
       ================================================================== */
    function cargarDistribucionTipo() {
        cargando('#sk-tipo', '#box-tipo,#legend-tipo,#empty-tipo');
        destruir('tipo');

        return $.get('/dashboardhon/distribucion-tipo' + qs(), function (r) {
            $('#sk-tipo').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-tipo').prop('hidden', false); return; }

            var c = paleta();
            // Hues bien separados: en modo oscuro primary y blue son casi el
            // mismo azul, así que primary no entra en esta paleta categórica.
            var colores = [c.accent, c.blue, c.orange, c.red, c.primary];
            var total = rows.reduce(function (s, x) { return s + (parseFloat(x.total_bs) || 0); }, 0);

            var el = document.getElementById('chart-tipo');
            $('#box-tipo').prop('hidden', false);

            charts.tipo = new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: rows.map(function (x) { return x.tipo; }),
                    datasets: [{
                        data: rows.map(function (x) { return parseFloat(x.total_bs) || 0; }),
                        backgroundColor: rows.map(function (_, i) { return colores[i % colores.length]; }),
                        borderColor: c.surface, borderWidth: 3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    cutoutPercentage: 68,
                    legend: { display: false },
                    tooltips: {
                        backgroundColor: c.surface, titleFontColor: c.text,
                        bodyFontColor: c.text, borderColor: c.grid, borderWidth: 1,
                        cornerRadius: 8, xPadding: 12, yPadding: 10,
                        callbacks: {
                            label: function (item, data) {
                                var v = data.datasets[0].data[item.index] || 0;
                                var p = total > 0 ? (v / total * 100).toFixed(1) : 0;
                                return ' ' + data.labels[item.index] + ': ' + fmtBs(v) + ' (' + p + '%)';
                            }
                        }
                    }
                }
            });

            var html = rows.map(function (x, i) {
                var v = parseFloat(x.total_bs) || 0;
                var p = total > 0 ? (v / total * 100).toFixed(1) : '0.0';
                return '<div class="dh-legend-item">' +
                       '<span class="dh-legend-dot" style="background:' + colores[i % colores.length] + '"></span>' +
                       '<span class="dh-legend-label">' + escapar(x.tipo) +
                       ' <span style="opacity:.6">· ' + fmtInt(x.cantidad) + '</span></span>' +
                       '<span class="dh-legend-val">' + p + '%</span></div>';
            }).join('');
            $('#legend-tipo').html(html).prop('hidden', false);

        }).fail(function () {
            $('#sk-tipo').hide();
            $('#empty-tipo').prop('hidden', false);
        });
    }

    /* ==================================================================
       5. Top médicos
       ================================================================== */
    function cargarTopMedicos() {
        cargando('#sk-medicos', '#rank-medicos,#empty-medicos');

        return $.get('/dashboardhon/top-medicos' + qs(), function (r) {
            $('#sk-medicos').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-medicos').prop('hidden', false); return; }

            var max = Math.max.apply(null, rows.map(function (x) { return parseFloat(x.asig_bs) || 0; }));

            var html = rows.map(function (x, i) {
                var v   = parseFloat(x.asig_bs) || 0;
                var pct = max > 0 ? (v / max * 100) : 0;
                var nombre = (x.emp_nom || '') + ' ' + (x.emp_ape || '');
                return '<li class="dh-rank-item">' +
                    '<span class="dh-rank-pos">' + (i + 1) + '</span>' +
                    '<div class="dh-rank-main">' +
                        '<div class="dh-rank-name">' + escapar(nombre.trim()) + '</div>' +
                        '<div class="dh-rank-meta">C.I. ' + fmtInt(x.emp_ced) + '</div>' +
                        '<div class="dh-rank-bar"><span class="dh-rank-fill" style="width:' + pct.toFixed(1) + '%"></span></div>' +
                    '</div>' +
                    '<div class="dh-rank-vals">' +
                        '<div class="dh-rank-bs">' + fmtCorto(v, 'Bs ') + '</div>' +
                        '<div class="dh-rank-usd">' + fmtCorto(x.asig_usd, '$ ') + '</div>' +
                    '</div>' +
                '</li>';
            }).join('');

            $('#rank-medicos').html(html).prop('hidden', false);
        }).fail(function () {
            $('#sk-medicos').hide();
            $('#empty-medicos').prop('hidden', false);
        });
    }

    /* ==================================================================
       6. TABLAS
       ================================================================== */
    function botonesExport(nombre, titulo) {
        return [
            { extend: 'excelHtml5', text: '<i class="fa fa-file-excel-o"></i> Excel',
              title: null, filename: nombre + '_' + new Date().toISOString().slice(0, 10),
              exportOptions: { columns: ':visible' },
              action: function (e, dt, node, config) {
                  $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
                  toast('Archivo Excel generado');
              } },
            { extend: 'print', text: '<i class="fa fa-print"></i> Imprimir',
              title: titulo, exportOptions: { columns: ':visible' },
              customize: function (win) {
                  $(win.document.body).css('font-size', '11px')
                      .find('table').addClass('compact').css('font-size', '10px');
              } }
        ];
    }

    var DOM_TABLA = '<"row"<"col-sm-7"B><"col-sm-5"f>>rt<"row"<"col-sm-5"i><"col-sm-7"p>>';

    /* ── 6.1 Totales por nómina ── */
    function cargarTotalesNomina() {
        cargando('#sk-nomina', '#wrap-nomina');
        if (tablas.nomina) { tablas.nomina.destroy(); tablas.nomina = null; $('#tabla-nomina').empty(); }

        return $.get('/dashboardhon/totales-nomina' + qs(), function (r) {
            $('#sk-nomina').hide();
            $('#wrap-nomina').prop('hidden', false);

            tablas.nomina = $('#tabla-nomina').DataTable({
                data: r.data || [],
                pageLength: 8, lengthChange: false,
                language: dtES, dom: DOM_TABLA, order: [[0, 'desc']],
                buttons: botonesExport('totales_nomina', 'Totales por Nómina'),
                columns: [
                    { data: 'fecha_ord', title: 'Nómina', className: 'dh-ctr',
                      render: function (d, t, row) {
                          if (t !== 'display') return d;
                          return '<span class="dh-badge dh-badge-n">#' + escapar(row.nomina) + '</span>';
                      } },
                    { data: 'fdesde', title: 'Desde' },
                    { data: 'fhasta', title: 'Hasta' },
                    { data: 'medicos', title: 'Médicos', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? fmtInt(d) : d; } },
                    { data: 'asig_bs', title: 'Honorarios Bs', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'ded_bs', title: 'Deducciones Bs', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: null, title: 'Neto Bs', className: 'dh-num',
                      render: function (row, t) {
                          var v = (parseFloat(row.asig_bs) || 0) - (parseFloat(row.ded_bs) || 0);
                          return t === 'display' ? '<strong>' + nf(v) + '</strong>' : v;
                      } },
                    { data: 'asig_usd', title: 'Honorarios USD', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'tasa_avg', title: 'Tasa BCV', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } }
                ]
            });
        }).fail(function () {
            $('#sk-nomina').hide();
            toast('No se pudieron cargar los totales por nómina', 'error');
        });
    }

    /* ── 6.2 Conceptos (con ficha en modal) ── */
    var datosConceptos = [];

    function cargarConceptos() {
        cargando('#sk-conceptos', '#wrap-conceptos');
        if (tablas.conceptos) { tablas.conceptos.destroy(); tablas.conceptos = null; $('#tabla-conceptos').empty(); }

        return $.get('/dashboardhon/ranking-conceptos' + qs(), function (r) {
            $('#sk-conceptos').hide();
            $('#wrap-conceptos').prop('hidden', false);
            datosConceptos = r.data || [];

            tablas.conceptos = $('#tabla-conceptos').DataTable({
                data: datosConceptos,
                pageLength: 10, lengthChange: false,
                language: dtES, dom: DOM_TABLA,
                order: [[1, 'asc']],   // alfabético por defecto (req. 2)
                buttons: botonesExport('conceptos', 'Conceptos de Honorarios'),
                createdRow: function (fila) { $(fila).addClass('dh-clickable'); },
                columns: [
                    { data: 'tipo', title: 'Tipo', className: 'dh-ctr',
                      render: function (d) {
                          return d === 'A'
                              ? '<span class="dh-badge dh-badge-a">Asig.</span>'
                              : '<span class="dh-badge dh-badge-d">Ded.</span>';
                      } },
                    { data: 'concepto', title: 'Concepto' },
                    { data: 'medicos', title: 'Médicos', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? fmtInt(d) : d; } },
                    { data: 'frecuencia', title: 'Veces', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? fmtInt(d) : d; } },
                    { data: 'total_bs', title: 'Total Bs', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'total_usd', title: 'Total USD', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'prom_bs', title: 'Promedio Bs', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } }
                ]
            });
        }).fail(function () {
            $('#sk-conceptos').hide();
            toast('No se pudieron cargar los conceptos', 'error');
        });
    }

    /* Ficha de concepto (req. 8).
       Se delega desde document: DataTables reemplaza el tbody, así que enlazar
       sobre él en el ready dejaría el handler colgando de un nodo descartado. */
    $(document).on('click', '#tabla-conceptos tbody tr', function () {
        if (!tablas.conceptos) return;
        var d = tablas.conceptos.row(this).data();
        if (!d) return;

        $('#dh-modal-title').text(d.concepto);
        $('#dh-modal-body').html(
            '<div class="dh-facts">' +
            fact('Código',      d.cod) +
            fact('Tipo',        d.tipo === 'A' ? 'Asignación' : 'Deducción') +
            fact('Médicos',     fmtInt(d.medicos)) +
            fact('Nóminas',     fmtInt(d.nominas)) +
            fact('Veces',       fmtInt(d.frecuencia)) +
            fact('Total Bs',    nf(d.total_bs)) +
            fact('Total USD',   nf(d.total_usd)) +
            fact('Promedio Bs', nf(d.prom_bs)) +
            fact('Mínimo Bs',   nf(d.min_bs)) +
            fact('Máximo Bs',   nf(d.max_bs)) +
            '</div>'
        );
        abrirModal();
    });

    function fact(label, valor) {
        return '<div class="dh-fact">' +
               '<div class="dh-fact-label">' + escapar(label) + '</div>' +
               '<div class="dh-fact-value">' + escapar(valor) + '</div></div>';
    }

    function abrirModal() { $('#dh-modal-concepto').prop('hidden', false); }
    function cerrarModal() { $('#dh-modal-concepto').prop('hidden', true); }

    $('#dh-modal-concepto').on('click', '[data-close]', cerrarModal);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && !$('#dh-modal-concepto').prop('hidden')) cerrarModal();
    });

    /* ── 6.3 Pacientes ── */
    function cargarPacientes() {
        cargando('#sk-pacientes', '#wrap-pacientes');
        if (tablas.pacientes) { tablas.pacientes.destroy(); tablas.pacientes = null; $('#tabla-pacientes').empty(); }

        return $.get('/dashboardhon/pacientes' + qs(), function (r) {
            $('#sk-pacientes').hide();
            $('#wrap-pacientes').prop('hidden', false);

            tablas.pacientes = $('#tabla-pacientes').DataTable({
                data: r.data || [],
                pageLength: 10, lengthChange: false,
                language: dtES, dom: DOM_TABLA,
                order: [[2, 'asc']],   // alfabético por médico (req. 2)
                buttons: botonesExport('pacientes', 'Relación de Pacientes'),
                columns: [
                    { data: 'fecha_ord', title: 'Fecha',
                      render: function (d, t, row) { return t === 'display' ? row.fecha : d; } },
                    { data: 'factura', title: 'Factura' },
                    { data: 'medico',  title: 'Médico' },
                    { data: 'paciente', title: 'Paciente' },
                    { data: 'tipo', title: 'Atención', className: 'dh-ctr' },
                    { data: 'concepto', title: 'Concepto' },
                    { data: 'honorario', title: 'Honorario Bs', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'pagado', title: 'Pagado Bs', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'monto_usd', title: 'USD', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } },
                    { data: 'tasa_cambio', title: 'Tasa', className: 'dh-num',
                      render: function (d, t) { return t === 'display' ? nf(d) : (parseFloat(d) || 0); } }
                ]
            });
        }).fail(function () {
            $('#sk-pacientes').hide();
            toast('No se pudo cargar la relación de pacientes', 'error');
        });
    }

    /* ==================================================================
       IMPRESIÓN POR PANEL
       ================================================================== */
    var CSS_PRINT =
        '@page{margin:12mm}' +
        'body{font-family:Arial,sans-serif;font-size:11px;color:#222;' +
        '-webkit-print-color-adjust:exact;print-color-adjust:exact}' +
        'h3{font-size:14px;margin:0 0 10px;color:#1a3a5c;' +
        'border-bottom:2px solid #0d7e6e;padding-bottom:6px}' +
        'img{max-width:100%;height:auto}' +
        'table{width:100%;border-collapse:collapse}' +
        'th{background:#1a3a5c;color:#fff;padding:6px 8px;font-size:10px;text-align:left}' +
        'td{padding:5px 8px;border-bottom:1px solid #eee;font-size:10px}' +
        'tr:nth-child(even) td{background:#f7f9fc}' +
        '.r{text-align:right}.c{text-align:center}';

    function imprimirHtml(html) {
        var $f = $('<iframe>').css({
            position: 'fixed', top: '-9999px', left: '-9999px',
            width: '1px', height: '1px', border: 0
        }).appendTo('body');

        var doc = $f[0].contentWindow.document;
        doc.open(); doc.write(html); doc.close();

        var timer = setInterval(function () {
            if (doc.readyState !== 'complete') return;
            clearInterval(timer);
            $f[0].contentWindow.focus();
            $f[0].contentWindow.print();
            setTimeout(function () { $f.remove(); }, 2500);
        }, 50);
    }

    $(document).on('click', '.dh-print', function () {
        var tipo   = $(this).data('print');
        var titulo = $(this).data('titulo');

        if (tipo === 'grafico') {
            var canvas = document.getElementById($(this).data('canvas'));
            var visible = canvas && canvas.offsetParent !== null;
            if (!visible) { toast('El gráfico aún no ha cargado', 'warn'); return; }
            imprimirHtml('<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + titulo + '</title>' +
                '<style>' + CSS_PRINT + '</style></head><body><h3>' + titulo + '</h3>' +
                '<img src="' + canvas.toDataURL('image/png', 1) + '"></body></html>');
            return;
        }

        var dt = tablas[$(this).data('tabla')];
        if (!dt) { toast('La tabla aún no ha cargado', 'warn'); return; }
        dt.button(1).trigger();   // reutiliza el botón "Imprimir" de DataTables
    });

    /* ==================================================================
       BARRA DE ESTADO
       ================================================================== */
    var ETIQUETAS = {
        hoy: 'Hoy', semana: 'Esta semana', mes: 'Este mes', mesant: 'Mes anterior',
        anio: 'Este año', '3m': 'Últimos 3 meses', '6m': 'Últimos 6 meses',
        '12m': 'Últimos 12 meses', '24m': 'Últimos 24 meses'
    };

    function actualizarStatus() {
        var etiqueta = state.periodo === 'custom'
            ? (state.fecha_desde || '?') + ' → ' + (state.fecha_hasta || '?')
            : (ETIQUETAS[state.periodo] || state.periodo);
        $('#dh-status-periodo').text(etiqueta);

        var extras = [];
        if (state.emp_ced) {
            var nombre = $('#dh-sel-medico option:selected').text();
            extras.push('<i class="fa fa-user-md"></i> ' + escapar(abreviar(nombre, 34)));
        }
        if (state.conceptos) {
            extras.push('<i class="fa fa-tags"></i> ' + state.conceptos.split(',').length + ' concepto(s)');
        }
        $('#dh-status-filtros').html(extras.join(' &nbsp;·&nbsp; '));
        $('#dh-status-time').text('Actualizado ' + new Date().toLocaleTimeString('es-VE'));
    }

    /* ==================================================================
       CARGA COMPLETA
       ================================================================== */
    function redibujarGraficos() {
        dibujarEvolucion();
        cargarSparklines();
        cargarDistribucionTipo();
    }

    function cargarTodo() {
        $('#dh-onboard').prop('hidden', true);
        $('#dh-body').prop('hidden', false);
        $('#dh-btn-refresh').addClass('is-spinning');
        yaAplico = true;

        $.when(
            cargarKpis(),
            cargarSparklines(),
            cargarEvolucion(),
            cargarDistribucionTipo(),
            cargarTopMedicos(),
            cargarTotalesNomina(),
            cargarConceptos(),
            cargarPacientes()
        ).always(function () {
            $('#dh-btn-refresh').removeClass('is-spinning');
            actualizarStatus();
        });
    }

    /* ==================================================================
       FILTROS
       ================================================================== */
    function cargarFiltroMedicos() {
        $.get('/dashboardhon/filtro-medicos', function (rows) {
            var $s = $('#dh-sel-medico');
            $s.find('option:not(:first)').remove();
            rows.forEach(function (r) {
                $s.append('<option value="' + r.emp_ced + '">' +
                          escapar(r.nombre) + ' — ' + r.emp_ced + '</option>');
            });
            $s.selectpicker('refresh');
        });
    }

    function cargarFiltroConceptos() {
        $.get('/dashboardhon/filtro-conceptos', function (rows) {
            var $s = $('#dh-sel-conceptos').empty();
            var grupos = { A: [], D: [] };
            rows.forEach(function (r) { (grupos[r.tipo] = grupos[r.tipo] || []).push(r); });

            [['A', 'Asignaciones'], ['D', 'Deducciones']].forEach(function (g) {
                var lista = grupos[g[0]] || [];
                if (!lista.length) return;
                var $og = $('<optgroup>').attr('label', g[1]);
                lista.forEach(function (r) {
                    $og.append($('<option>').val(r.cod).text(r.descripcion));
                });
                $s.append($og);
            });
            $s.selectpicker('refresh');
        });
    }

    $('.dh-filters .dh-seg-btn').on('click', function () {
        $('.dh-filters .dh-seg-btn').removeClass('is-active');
        $(this).addClass('is-active');
        state.periodo = $(this).data('periodo');
        $('#dh-rango-custom').toggleClass('is-visible', state.periodo === 'custom');
    });

    function aplicar() {
        state.emp_ced   = $('#dh-sel-medico').val() || '';
        state.conceptos = ($('#dh-sel-conceptos').val() || []).join(',');

        if (state.periodo === 'custom') {
            state.fecha_desde = $('#dh-fecha-desde').val();
            state.fecha_hasta = $('#dh-fecha-hasta').val();
            if (!state.fecha_desde || !state.fecha_hasta) {
                toast('Indique el rango de fechas Desde y Hasta', 'warn');
                return;
            }
            if (state.fecha_desde > state.fecha_hasta) {
                toast('La fecha Desde no puede ser posterior a Hasta', 'warn');
                return;
            }
        } else {
            state.fecha_desde = '';
            state.fecha_hasta = '';
        }

        cargarTodo();
    }

    $('#dh-btn-aplicar, #dh-btn-aplicar-2').on('click', aplicar);
    $('#dh-btn-refresh').on('click', function () {
        if (!yaAplico) { toast('Primero seleccione un período y presione Aplicar', 'warn'); return; }
        cargarTodo();
    });

    $('#dh-btn-limpiar').on('click', function () {
        state = { periodo: 'mes', fecha_desde: '', fecha_hasta: '', emp_ced: '', conceptos: '' };
        $('.dh-filters .dh-seg-btn').removeClass('is-active').filter('[data-periodo="mes"]').addClass('is-active');
        $('#dh-rango-custom').removeClass('is-visible');
        $('#dh-fecha-desde,#dh-fecha-hasta').val('');
        $('#dh-sel-medico').val('').selectpicker('refresh');
        $('#dh-sel-conceptos').val([]).selectpicker('refresh');
        toast('Filtros restablecidos');
    });

    /* ==================================================================
       INICIALIZACIÓN — solo los combos, sin consultas pesadas
       ================================================================== */
    cargarFiltroMedicos();
    cargarFiltroConceptos();
});
