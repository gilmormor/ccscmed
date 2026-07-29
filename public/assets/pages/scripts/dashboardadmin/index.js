/* ================================================================
   Dashboard Administrativo — BI Nómina
   ccscmed · Chart.js v2.8.0 + DataTables
   ================================================================ */

$(document).ready(function () {

    /* ────────────────────────────────────────────────────────────
       ESTADO GLOBAL DE FILTROS
    ──────────────────────────────────────────────────────────── */
    var state = {
        periodo     : 'mes',
        fecha_desde : '',
        fecha_hasta : '',
        emp_ced     : '',
        conceptos   : ''
    };

    /* ────────────────────────────────────────────────────────────
       REFERENCIAS A CHARTS Y DATATABLES (para destruir antes de recrear)
    ──────────────────────────────────────────────────────────── */
    var charts = {};
    var dtMov     = null;
    var dtRanking = null;

    /* ────────────────────────────────────────────────────────────
       HELPERS DE FORMATO
    ──────────────────────────────────────────────────────────── */
    function fmtBs(v)  { return 'Bs '  + parseFloat(v || 0).toFixed(2); }
    function fmtUsd(v) { return '$ '   + parseFloat(v || 0).toFixed(2); }
    function fmtNum(v) { return parseInt(v || 0).toLocaleString('es-VE'); }
    function fmtTasa(v){ return 'Bs '  + parseFloat(v || 0).toFixed(2); }

    /* Abreviar etiquetas largas (para ejes de gráficos) */
    function abreviar(str, max) {
        max = max || 22;
        return str && str.length > max ? str.substring(0, max) + '…' : str;
    }

    /* ────────────────────────────────────────────────────────────
       CONSTRUIR QUERYSTRING DESDE EL ESTADO
    ──────────────────────────────────────────────────────────── */
    function buildQS() {
        var p = { periodo: state.periodo };
        if (state.periodo === 'custom') {
            if (state.fecha_desde) p.fecha_desde = state.fecha_desde;
            if (state.fecha_hasta) p.fecha_hasta = state.fecha_hasta;
        }
        if (state.emp_ced)   p.emp_ced   = state.emp_ced;
        if (state.conceptos) p.conceptos = state.conceptos;
        var qs = $.param(p);
        return qs ? '?' + qs : '';
    }

    /* ────────────────────────────────────────────────────────────
       HELPER — DESTRUIR CHART EXISTENTE
    ──────────────────────────────────────────────────────────── */
    function destroyChart(key) {
        if (charts[key]) { charts[key].destroy(); charts[key] = null; }
    }

    /* ────────────────────────────────────────────────────────────
       COLORES CORPORATIVOS
    ──────────────────────────────────────────────────────────── */
    var COLOR = {
        asig  : 'rgba(13,126,110,0.78)',
        asigB : '#0d7e6e',
        ded   : 'rgba(192,57,43,0.70)',
        dedB  : '#c0392b',
        neto  : '#1a3a5c',
        usd   : 'rgba(36,113,163,0.80)',
        usdB  : '#2471a3',
        tasa  : 'rgba(202,111,30,0.85)',
        tasaB : '#ca6f1e',
        pal   : ['#0d7e6e','#1a3a5c','#ca6f1e','#2471a3','#7d3c98','#c0392b','#1abc9c','#e67e22','#2980b9','#8e44ad']
    };

    /* DataTables en español (local — evita CDN externo) */
    var dtES = {
        processing:'Procesando...', search:'Buscar:', lengthMenu:'Mostrar _MENU_',
        info:'_START_–_END_ de _TOTAL_', infoEmpty:'0 registros',
        infoFiltered:'(de _MAX_)', zeroRecords:'Sin resultados',
        emptyTable:'Sin datos', paginate:{ first:'«', previous:'‹', next:'›', last:'»' }
    };

    /* ================================================================
       1. KPIs
       ================================================================ */
    function cargarKpis() {
        var $vals = $('#kpi-asig-bs,#kpi-asig-usd,#kpi-ded-bs,#kpi-neto-bs,#kpi-trab,#kpi-tasa');
        $vals.html('<i class="fa fa-spinner fa-spin"></i>');

        $.get('/dashboardadmin/kpis' + buildQS(), function (r) {
            $('#kpi-asig-bs').text(fmtBs(r.asig_bs));
            $('#kpi-asig-usd').text(fmtUsd(r.asig_usd));
            $('#kpi-ded-bs').text(fmtBs(r.ded_bs));

            var netoColor = parseFloat(r.neto_bs) >= 0 ? 'var(--da-primary)' : 'var(--da-red)';
            $('#kpi-neto-bs').text(fmtBs(r.neto_bs)).css('color', netoColor);

            $('#kpi-trab').text(fmtNum(r.total_trab));
            $('#kpi-prom-trab').text('Prom. ' + fmtBs(r.prom_por_trab));

            $('#kpi-tasa').text(fmtTasa(r.tasa_avg));
            $('#kpi-periodos-sub').text(fmtNum(r.total_periodos) + ' períodos');
        }).fail(function () { $vals.text('—'); });
    }

    /* ================================================================
       2. Evolución mensual (bar + line — Chart.js v2)
       ================================================================ */
    function cargarEvolucion() {
        $('#sp-evolucion').show();
        $('#chart-evolucion,#empty-evolucion').hide();
        destroyChart('evolucion');

        $.get('/dashboardadmin/evolucion' + buildQS(), function (r) {
            $('#sp-evolucion').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-evolucion').show(); return; }

            var labels   = rows.map(function(m){ return m.mes_label; });
            var dataAsig = rows.map(function(m){ return parseFloat(m.asig_bs) || 0; });
            var dataDed  = rows.map(function(m){ return parseFloat(m.ded_bs)  || 0; });
            var dataNeto = rows.map(function(m){ return (parseFloat(m.asig_bs)||0) - (parseFloat(m.ded_bs)||0); });

            var ctx = document.getElementById('chart-evolucion').getContext('2d');
            $('#chart-evolucion').show();

            charts.evolucion = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label:'Asignaciones (Bs)', data:dataAsig, backgroundColor:COLOR.asig, borderColor:COLOR.asigB, borderWidth:1, borderRadius:3 },
                        { label:'Deducciones (Bs)',  data:dataDed,  backgroundColor:COLOR.ded,  borderColor:COLOR.dedB,  borderWidth:1, borderRadius:3 },
                        { label:'Neto (Bs)', type:'line', data:dataNeto, borderColor:COLOR.neto, backgroundColor:'rgba(26,58,92,0.06)',
                          fill:false, tension:0.4, pointRadius:4, pointBackgroundColor:COLOR.neto, borderWidth:2 }
                    ]
                },
                options: {
                    responsive: true,
                    legend: { position:'top', labels:{ fontSize:11, padding:12, usePointStyle:true } },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            label: function(item, data) {
                                var lbl = data.datasets[item.datasetIndex].label || '';
                                return lbl + ': ' + fmtBs(item.yLabel);
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero:true, callback:function(v){ return fmtBs(v); }, fontSize:10 },
                            gridLines: { color:'rgba(0,0,0,0.04)' }
                        }],
                        xAxes: [{ ticks:{ fontSize:10 }, gridLines:{ display:false } }]
                    }
                }
            });
        }).fail(function(){ $('#sp-evolucion').hide(); $('#empty-evolucion').show(); });
    }

    /* ================================================================
       3. Distribución A vs D (doughnut)
       ================================================================ */
    function cargarDistribucion() {
        $('#sp-distribucion').show();
        $('#chart-distribucion,#da-donut-legend,#empty-distribucion').hide();
        destroyChart('distribucion');

        $.get('/dashboardadmin/distribucion' + buildQS(), function (r) {
            $('#sp-distribucion').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-distribucion').show(); return; }

            var total  = rows.reduce(function(s, x){ return s + parseFloat(x.total_bs||0); }, 0);
            var labels = rows.map(function(x){ return x.tipo === 'A' ? 'Asignaciones' : 'Deducciones'; });
            var data   = rows.map(function(x){ return parseFloat(x.total_bs||0); });
            var colors = rows.map(function(x){ return x.tipo === 'A' ? COLOR.asigB : COLOR.dedB; });

            var ctx = document.getElementById('chart-distribucion').getContext('2d');
            $('#chart-distribucion').show();

            charts.distribucion = new Chart(ctx, {
                type: 'doughnut',
                data: { labels:labels, datasets:[{ data:data, backgroundColor:colors, borderWidth:3, borderColor:'#fff' }] },
                options: {
                    responsive: true,
                    cutoutPercentage: 65,
                    legend: { display:false },
                    tooltips: {
                        callbacks: {
                            label: function(item, data) {
                                var val = data.datasets[0].data[item.index] || 0;
                                var pct = total > 0 ? ((val/total)*100).toFixed(1) : 0;
                                return data.labels[item.index] + ': ' + fmtBs(val) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            });

            // Leyenda custom
            var html = '';
            rows.forEach(function(x, i) {
                var pct = total > 0 ? ((parseFloat(x.total_bs||0)/total)*100).toFixed(1) : 0;
                html += '<div class="da-donut-legend-item">' +
                    '<span class="da-donut-legend-dot" style="background:' + colors[i] + '"></span>' +
                    '<span style="flex:1">' + labels[i] + '</span>' +
                    '<strong>' + pct + '%</strong></div>';
            });
            $('#da-donut-legend').html(html).show();
        }).fail(function(){ $('#sp-distribucion').hide(); $('#empty-distribucion').show(); });
    }

    /* ================================================================
       4. Top 10 Conceptos (horizontal bar — Chart.js v2)
       ================================================================ */
    function cargarTopConceptos() {
        $('#sp-top-conceptos').show();
        $('#chart-top-conceptos,#empty-top-conceptos').hide();
        destroyChart('topConceptos');

        $.get('/dashboardadmin/top-conceptos' + buildQS(), function (r) {
            $('#sp-top-conceptos').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-top-conceptos').show(); return; }

            var labels = rows.map(function(x){ return abreviar(x.concepto, 24); });
            var data   = rows.map(function(x){ return parseFloat(x.total_bs||0); });
            var colors = rows.map(function(x){ return x.tipo === 'A' ? COLOR.asig : COLOR.ded; });

            var ctx = document.getElementById('chart-top-conceptos').getContext('2d');
            $('#chart-top-conceptos').show();

            charts.topConceptos = new Chart(ctx, {
                type: 'horizontalBar',
                data: { labels:labels, datasets:[{
                    label:'Total Bs', data:data,
                    backgroundColor:colors, borderColor:colors, borderWidth:1
                }]},
                options: {
                    responsive: true,
                    legend: { display:false },
                    tooltips: {
                        callbacks: {
                            title: function(items, data) { return rows[items[0].index].concepto; },
                            label: function(item, data) {
                                var row = rows[item.index];
                                return [
                                    'Monto: ' + fmtBs(item.xLabel),
                                    'USD: '   + fmtUsd(row.total_usd),
                                    'Veces: ' + fmtNum(row.frecuencia),
                                    'Tipo: '  + (row.tipo === 'A' ? 'Asignación' : 'Deducción')
                                ];
                            }
                        }
                    },
                    scales: {
                        xAxes: [{ ticks:{ callback:function(v){ return fmtBs(v); }, fontSize:9 }, gridLines:{ color:'rgba(0,0,0,0.04)' } }],
                        yAxes: [{ ticks:{ fontSize:10 } }]
                    }
                }
            });
        }).fail(function(){ $('#sp-top-conceptos').hide(); $('#empty-top-conceptos').show(); });
    }

    /* ================================================================
       5. Top 10 Trabajadores (horizontal bar)
       ================================================================ */
    function cargarTopTrabajadores() {
        $('#sp-top-trab').show();
        $('#chart-top-trab,#empty-top-trab').hide();
        destroyChart('topTrab');

        $.get('/dashboardadmin/top-trabajadores' + buildQS(), function (r) {
            $('#sp-top-trab').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-top-trab').show(); return; }

            var labels = rows.map(function(x){ return abreviar(x.emp_nom + ' ' + x.emp_ape, 22); });
            var dataA  = rows.map(function(x){ return parseFloat(x.asig_bs||0); });
            var dataD  = rows.map(function(x){ return parseFloat(x.ded_bs||0);  });

            var ctx = document.getElementById('chart-top-trab').getContext('2d');
            $('#chart-top-trab').show();

            charts.topTrab = new Chart(ctx, {
                type: 'horizontalBar',
                data: { labels:labels, datasets:[
                    { label:'Asig. Bs', data:dataA, backgroundColor:COLOR.asig, borderColor:COLOR.asigB, borderWidth:1 },
                    { label:'Ded. Bs',  data:dataD, backgroundColor:COLOR.ded,  borderColor:COLOR.dedB,  borderWidth:1 }
                ]},
                options: {
                    responsive: true,
                    legend: { position:'top', labels:{ fontSize:10, usePointStyle:true } },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            title: function(items, data) { return rows[items[0].index].emp_nom + ' ' + rows[items[0].index].emp_ape; },
                            label: function(item, data) {
                                return data.datasets[item.datasetIndex].label + ': ' + fmtBs(item.xLabel);
                            }
                        }
                    },
                    scales: {
                        xAxes: [{ stacked:false, ticks:{ callback:function(v){ return fmtBs(v); }, fontSize:9 }, gridLines:{ color:'rgba(0,0,0,0.04)' } }],
                        yAxes: [{ ticks:{ fontSize:10 } }]
                    }
                }
            });
        }).fail(function(){ $('#sp-top-trab').hide(); $('#empty-top-trab').show(); });
    }

    /* ================================================================
       6. Comparativo Bs vs USD (línea doble)
       ================================================================ */
    function cargarComparativo() {
        $('#sp-comparativo').show();
        $('#chart-comparativo,#empty-comparativo').hide();
        destroyChart('comparativo');

        $.get('/dashboardadmin/comparativo-bs-usd' + buildQS(), function (r) {
            $('#sp-comparativo').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-comparativo').show(); return; }

            var labels   = rows.map(function(m){ return m.mes_label; });
            var dataBs   = rows.map(function(m){ return parseFloat(m.total_bs  || 0); });
            var dataUsd  = rows.map(function(m){ return parseFloat(m.total_usd || 0); });

            var ctx = document.getElementById('chart-comparativo').getContext('2d');
            $('#chart-comparativo').show();

            charts.comparativo = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label:'Total Bs',  data:dataBs,  borderColor:COLOR.asigB, backgroundColor:'rgba(13,126,110,0.07)', fill:true,  tension:0.4, pointRadius:4, pointBackgroundColor:COLOR.asigB, borderWidth:2, yAxisID:'y-bs' },
                        { label:'Total USD', data:dataUsd, borderColor:COLOR.usdB,  backgroundColor:'rgba(36,113,163,0.07)',  fill:false, tension:0.4, pointRadius:4, pointBackgroundColor:COLOR.usdB,  borderWidth:2, yAxisID:'y-usd' }
                    ]
                },
                options: {
                    responsive: true,
                    legend: { position:'top', labels:{ fontSize:11, usePointStyle:true } },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            label: function(item, data) {
                                var lbl = data.datasets[item.datasetIndex].label;
                                return lbl + ': ' + (item.datasetIndex === 0 ? fmtBs(item.yLabel) : fmtUsd(item.yLabel));
                            }
                        }
                    },
                    scales: {
                        yAxes: [
                            { id:'y-bs',  position:'left',  ticks:{ callback:function(v){ return fmtBs(v);  }, fontSize:10 }, gridLines:{ color:'rgba(0,0,0,0.04)' } },
                            { id:'y-usd', position:'right', ticks:{ callback:function(v){ return fmtUsd(v); }, fontSize:10 }, gridLines:{ display:false } }
                        ],
                        xAxes: [{ ticks:{ fontSize:10 }, gridLines:{ display:false } }]
                    }
                }
            });
        }).fail(function(){ $('#sp-comparativo').hide(); $('#empty-comparativo').show(); });
    }

    /* ================================================================
       7. Evolución Tasa Dólar (línea)
       ================================================================ */
    function cargarEvolucionDolar() {
        $('#sp-dolar').show();
        $('#chart-dolar,#empty-dolar').hide();
        destroyChart('dolar');

        $.get('/dashboardadmin/evolucion-dolar' + buildQS(), function (r) {
            $('#sp-dolar').hide();
            var rows = r.data || [];
            if (!rows.length) { $('#empty-dolar').show(); return; }

            var labels   = rows.map(function(m){ return m.mes_label; });
            var dataAvg  = rows.map(function(m){ return parseFloat(m.tasa_avg || 0); });
            var dataMax  = rows.map(function(m){ return parseFloat(m.tasa_max || 0); });

            var ctx = document.getElementById('chart-dolar').getContext('2d');
            $('#chart-dolar').show();

            charts.dolar = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label:'Tasa Prom.', data:dataAvg, borderColor:COLOR.tasaB, backgroundColor:'rgba(202,111,30,0.08)', fill:true, tension:0.4, pointRadius:3, pointBackgroundColor:COLOR.tasaB, borderWidth:2 },
                        { label:'Tasa Máx.',  data:dataMax, borderColor:'rgba(202,111,30,0.4)', backgroundColor:'transparent', fill:false, tension:0.4, pointRadius:2, borderWidth:1, borderDash:[4,3] }
                    ]
                },
                options: {
                    responsive: true,
                    legend: { position:'top', labels:{ fontSize:10, usePointStyle:true } },
                    tooltips: {
                        callbacks: {
                            label: function(item, data) {
                                return data.datasets[item.datasetIndex].label + ': ' + fmtTasa(item.yLabel);
                            }
                        }
                    },
                    scales: {
                        yAxes: [{ ticks:{ callback:function(v){ return fmtTasa(v); }, fontSize:9 }, gridLines:{ color:'rgba(0,0,0,0.04)' } }],
                        xAxes: [{ ticks:{ fontSize:9 }, gridLines:{ display:false } }]
                    }
                }
            });
        }).fail(function(){ $('#sp-dolar').hide(); $('#empty-dolar').show(); });
    }

    /* ================================================================
       8. Tabla Últimos Movimientos
       ================================================================ */
    function cargarMovimientos() {
        if (dtMov) { dtMov.destroy(); dtMov = null; $('#da-tabla-mov tbody').empty(); }

        dtMov = $('#da-tabla-mov').DataTable({
            ajax: { url:'/dashboardadmin/movimientos' + buildQS(), dataSrc:'data' },
            pageLength: 10,
            language: dtES,
            order: [[0, 'asc'], [9, 'asc']],
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-5"i><"col-sm-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: null,
                    filename: 'movimientos_' + new Date().toISOString().slice(0,10),
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    className: 'btn btn-default btn-sm',
                    title: 'Últimos Movimientos',
                    exportOptions: { columns: ':visible' },
                    customize: function(win) {
                        $(win.document.body).css('font-size','11px');
                        $(win.document.body).find('table').addClass('compact').css('font-size','10px');
                    }
                }
            ],
            columns: [
                {
                    data:'fecha', title:'Desde',
                    render: function(data, type, row) {
                        return (type === 'sort' || type === 'type') ? row.fecha_ord : data;
                    }
                },
                { data:'fhasta',     title:'Hasta' },
                { data:'emp_ced',    title:'C.I.',  className:'text-right' },
                { data:'trabajador', title:'Trabajador' },
                { data:'concepto',   title:'Concepto' },
                {
                    data:'tipo', title:'Tipo', className:'text-center',
                    render: function(d) {
                        return d === 'A'
                            ? '<span class="badge-tipo-a">Asig.</span>'
                            : '<span class="badge-tipo-d">Ded.</span>';
                    }
                },
                {
                    data:'mov_monto', title:'Monto', className:'text-right',
                    render: function(d, type, row) {
                        var val = parseFloat(d||0);
                        if (row.tipo === 'D') val = -val;
                        if (type !== 'display') return val;
                        var color = row.tipo === 'A' ? 'color:var(--da-accent)' : 'color:var(--da-red)';
                        return '<span style="' + color + '">' + val.toFixed(2) + '</span>';
                    }
                },
                {
                    data:'monto_bs',  title:'Bs ME', className:'text-right',
                    render: function(d, type, row) {
                        if (d === null) return type === 'display' ? '—' : '';
                        var val = parseFloat(d);
                        if (row.tipo === 'D') val = -val;
                        return type === 'display' ? val.toFixed(2) : val;
                    }
                },
                {
                    data:'monto_usd', title:'USD', className:'text-right',
                    render: function(d, type, row) {
                        if (d === null) return type === 'display' ? '—' : '';
                        var val = parseFloat(d);
                        if (row.tipo === 'D') val = -val;
                        return type === 'display' ? val.toFixed(2) : val;
                    }
                },
                { data:'cod_con', title:'', visible:false }
            ]
        });
    }

    /* ================================================================
       9. Tabla Ranking Conceptos
       ================================================================ */
    function cargarRanking() {
        if (dtRanking) { dtRanking.destroy(); dtRanking = null; $('#da-tabla-ranking tbody').empty(); }

        dtRanking = $('#da-tabla-ranking').DataTable({
            ajax: { url:'/dashboardadmin/ranking-conceptos' + buildQS(), dataSrc:'data' },
            pageLength: 10,
            language: dtES,
            order: [[3, 'desc']],
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-5"i><"col-sm-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: null,
                    filename: 'ranking_conceptos_' + new Date().toISOString().slice(0,10),
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    className: 'btn btn-default btn-sm',
                    title: 'Ranking de Conceptos',
                    exportOptions: { columns: ':visible' },
                    customize: function(win) {
                        $(win.document.body).css('font-size','11px');
                        $(win.document.body).find('table').addClass('compact').css('font-size','10px');
                    }
                }
            ],
            columns: [
                { data:'concepto',  title:'Concepto' },
                {
                    data:'tipo', title:'Tipo', className:'text-center',
                    render: function(d) {
                        return d === 'A'
                            ? '<span class="badge-tipo-a">A</span>'
                            : '<span class="badge-tipo-d">D</span>';
                    }
                },
                { data:'frecuencia', title:'#', className:'text-center' },
                {
                    data:'total_monto', title:'Bs', className:'text-right',
                    render: function(d, type, row) {
                        var val = parseFloat(d||0);
                        if (row.tipo === 'D') val = -val;
                        return type === 'display' ? val.toFixed(2) : val;
                    }
                },
                {
                    data:'total_bsme', title:'BsMe', className:'text-right',
                    render: function(d, type, row) {
                        if (d === null) return type === 'display' ? '—' : '';
                        var val = parseFloat(d);
                        if (row.tipo === 'D') val = -val;
                        return type === 'display' ? val.toFixed(2) : val;
                    }
                },
                {
                    data:'total_usd', title:'$', className:'text-right',
                    render: function(d, type, row) {
                        if (d === null) return type === 'display' ? '—' : '';
                        var val = parseFloat(d);
                        if (row.tipo === 'D') val = -val;
                        return type === 'display' ? val.toFixed(2) : val;
                    }
                }
            ]
        });
    }

    /* ================================================================
       IMPRIMIR PANEL INDIVIDUAL
       ================================================================ */

    /* ── Estilos compartidos para todas las impresiones ── */
    var _css =
        '@page { margin: 0mm; }' +
        'html, body { margin: 0; padding: 0; }' +
        'body { padding: 10mm; font-family: Arial, sans-serif; font-size: 11px;' +
        '       print-color-adjust: exact; -webkit-print-color-adjust: exact; }' +
        'h3 { font-size: 14px !important; font-weight: bold; margin: 0 0 8px 0; color: #1a3a5c;' +
        '     border-bottom: 2px solid #0d7e6e; padding-bottom: 5px; page-break-after: avoid; }' +
        'img { max-width: 100%; height: auto; display: block; page-break-before: avoid; }' +
        'table { width: 100%; border-collapse: collapse; page-break-before: avoid; }' +
        'th { background: #1a3a5c !important; color: #fff !important; padding: 5px 8px; font-size: 10px;' +
        '     print-color-adjust: exact; -webkit-print-color-adjust: exact; }' +
        'th.r, td.r { text-align: right; }' +
        'th.c, td.c { text-align: center; }' +
        'td { padding: 4px 8px; border-bottom: 1px solid #eee; font-size: 10px; }' +
        'tr:nth-child(even) td { background: #f7f9fc; print-color-adjust: exact; -webkit-print-color-adjust: exact; }';

    /* ── Helper: imprime HTML usando iframe oculto (evita problemas de window.open) ── */
    function _doPrint(html) {
        var fid = '__da_print_frame';
        $('#' + fid).remove();

        var $frame = $('<iframe id="' + fid + '">').css({
            position: 'fixed', top: '-9999px', left: '-9999px',
            width: '1px', height: '1px', border: 'none'
        }).appendTo('body');

        var doc = $frame[0].contentWindow.document;
        doc.open(); doc.write(html); doc.close();

        var check = setInterval(function() {
            if (doc.readyState === 'complete') {
                clearInterval(check);
                $frame[0].contentWindow.focus();
                $frame[0].contentWindow.print();
                setTimeout(function() { $('#' + fid).remove(); }, 3000);
            }
        }, 50);
    }

    /* Gráfico: captura canvas como imagen PNG y lanza impresión */
    function imprimirGrafico(titulo, canvasId) {
        var canvas = document.getElementById(canvasId);
        if (!canvas || canvas.style.display === 'none') {
            swal('Aviso', 'El gráfico aún no ha cargado.', 'warning');
            return;
        }
        var img = canvas.toDataURL('image/png', 1.0);
        _doPrint(
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + titulo + '</title>' +
            '<style>' + _css + '</style></head><body>' +
            '<h3>' + titulo + '</h3>' +
            '<img src="' + img + '">' +
            '</body></html>'
        );
    }

    /* Tabla: toma TODAS las filas del DataTable y construye HTML limpio */
    function imprimirTabla(titulo, dtInstance, columnas) {
        var filas = dtInstance.rows().data().toArray();

        var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + titulo + '</title>' +
            '<style>' + _css + '</style></head><body>' +
            '<h3>' + titulo + '</h3>' +
            '<table><thead><tr>';

        columnas.forEach(function(col) {
            html += '<th class="' + (col.cls || '') + '">' + col.label + '</th>';
        });
        html += '</tr></thead><tbody>';

        filas.forEach(function(row) {
            html += '<tr>';
            columnas.forEach(function(col) {
                var val = col.render
                    ? col.render(row)
                    : (row[col.data] !== null && row[col.data] !== undefined ? row[col.data] : '—');
                html += '<td class="' + (col.cls || '') + '">' + val + '</td>';
            });
            html += '</tr>';
        });

        html += '</tbody></table></body></html>';
        _doPrint(html);
    }

    /* Definición de columnas para cada tabla */
    var colsMov = [
        { data:'fecha',      label:'Desde' },
        { data:'fhasta',     label:'Hasta' },
        { data:'emp_ced',    label:'C.I.',       cls:'r' },
        { data:'trabajador', label:'Trabajador' },
        { data:'concepto',   label:'Concepto' },
        { data:'tipo',       label:'Tipo',  cls:'c',
          render: function(r){ return r.tipo === 'A' ? 'Asig.' : 'Ded.'; } },
        { data:'mov_monto',  label:'Monto', cls:'r',
          render: function(r){ var v=parseFloat(r.mov_monto||0); if(r.tipo==='D') v=-v; return v.toFixed(2); } },
        { data:'monto_bs',   label:'Bs ME', cls:'r',
          render: function(r){ if(r.monto_bs===null) return '—'; var v=parseFloat(r.monto_bs); if(r.tipo==='D') v=-v; return v.toFixed(2); } },
        { data:'monto_usd',  label:'USD',   cls:'r',
          render: function(r){ if(r.monto_usd===null) return '—'; var v=parseFloat(r.monto_usd); if(r.tipo==='D') v=-v; return v.toFixed(2); } }
    ];

    var colsRanking = [
        { data:'concepto',    label:'Concepto' },
        { data:'tipo',        label:'Tipo',  cls:'c',
          render: function(r){ return r.tipo === 'A' ? 'Asig.' : 'Ded.'; } },
        { data:'frecuencia',  label:'#',     cls:'c' },
        { data:'total_monto', label:'Bs',    cls:'r',
          render: function(r){ var v=parseFloat(r.total_monto||0); if(r.tipo==='D') v=-v; return v.toFixed(2); } },
        { data:'total_bsme',  label:'BsMe',  cls:'r',
          render: function(r){ if(r.total_bsme===null) return '—'; var v=parseFloat(r.total_bsme); if(r.tipo==='D') v=-v; return v.toFixed(2); } },
        { data:'total_usd',   label:'$',     cls:'r',
          render: function(r){ if(r.total_usd===null) return '—'; var v=parseFloat(r.total_usd); if(r.tipo==='D') v=-v; return v.toFixed(2); } }
    ];

    /* Click handler centralizado */
    $(document).on('click', '.da-btn-panel-print', function() {
        var tipo   = $(this).data('print');
        var titulo = $(this).data('titulo');

        if (tipo === 'grafico') {
            imprimirGrafico(titulo, $(this).data('canvas'));
        } else if (tipo === 'tabla') {
            var tabla = $(this).data('tabla');
            if (tabla === 'mov') {
                if (!dtMov) { swal('Aviso', 'La tabla aún no ha cargado.', 'warning'); return; }
                imprimirTabla(titulo, dtMov, colsMov);
            } else {
                if (!dtRanking) { swal('Aviso', 'La tabla aún no ha cargado.', 'warning'); return; }
                imprimirTabla(titulo, dtRanking, colsRanking);
            }
        }
    });

    /* ================================================================
       CARGAR TODO
       ================================================================ */
    function cargarTodo() {
        cargarKpis();
        cargarEvolucion();
        cargarDistribucion();
        cargarTopConceptos();
        cargarTopTrabajadores();
        cargarComparativo();
        cargarEvolucionDolar();
        cargarMovimientos();
        cargarRanking();
        actualizarStatusBar();
    }

    /* ================================================================
       BARRA DE ESTADO
       ================================================================ */
    function actualizarStatusBar() {
        var textos = {
            'mes':    'Mes actual',
            '3m':     'Últimos 3 meses',
            '6m':     'Últimos 6 meses',
            '12m':    'Últimos 12 meses',
            'anio':   'Año actual',
            'custom': (state.fecha_desde || '?') + ' → ' + (state.fecha_hasta || '?')
        };
        $('#da-status-periodo').text(textos[state.periodo] || state.periodo);

        var extras = [];
        if (state.emp_ced) extras.push('<i class="fa fa-user"></i> C.I. ' + parseInt(state.emp_ced).toLocaleString('es-VE'));
        if (state.conceptos) {
            var cnt = state.conceptos.split(',').length;
            extras.push('<i class="fa fa-tags"></i> ' + cnt + ' concepto(s)');
        }
        $('#da-status-filtros').html(extras.join('&nbsp;&nbsp;'));
        $('#da-status-time').text('Actualizado: ' + new Date().toLocaleTimeString('es-VE'));
    }

    /* ================================================================
       CARGA DE OPCIONES DE FILTROS (trabajadores y conceptos)
       ================================================================ */
    function cargarFiltroTrabajadores() {
        $.get('/dashboardadmin/filtro-trabajadores', function(rows) {
            var $sel = $('#da-sel-trabajador');
            $sel.find('option:not(:first)').remove();
            rows.forEach(function(r) {
                $sel.append('<option value="' + r.emp_ced + '">' + r.emp_ced + ' — ' + r.nombre + '</option>');
            });
            $sel.selectpicker('refresh');
        });
    }

    function cargarFiltroConceptos() {
        $.get('/dashboardadmin/filtro-conceptos', function(rows) {
            var $sel = $('#da-sel-conceptos');
            $sel.empty();
            var grupos = { A: [], D: [] };
            rows.forEach(function(r) { (grupos[r.tipo] = grupos[r.tipo]||[]).push(r); });
            [['A','Asignaciones'], ['D','Deducciones']].forEach(function(g) {
                if (!(grupos[g[0]]||[]).length) return;
                $sel.append('<optgroup label="' + g[1] + '">');
                grupos[g[0]].forEach(function(r) {
                    $sel.append('<option value="' + r.cod + '">' + r.descripcion + '</option>');
                });
                $sel.append('</optgroup>');
            });
            $sel.selectpicker('refresh');
        });
    }

    /* ================================================================
       EVENTOS DE FILTROS
       ================================================================ */

    // Botones de período
    $(document).on('click', '.da-period-btn', function() {
        $('.da-period-btn').removeClass('active');
        $(this).addClass('active');
        state.periodo = $(this).data('periodo');
        $('#da-rango-custom').toggle(state.periodo === 'custom');
    });

    // Botón Aplicar
    $('#da-btn-aplicar').on('click', function() {
        state.emp_ced = $('#da-sel-trabajador').val() || '';
        var conceptos = $('#da-sel-conceptos').val() || [];
        state.conceptos = conceptos.join(',');

        if (state.periodo === 'custom') {
            state.fecha_desde = $('#da-fecha-desde').val();
            state.fecha_hasta = $('#da-fecha-hasta').val();
            if (!state.fecha_desde || !state.fecha_hasta) {
                swal('Atención', 'Ingresa el rango de fechas para el período personalizado.', 'warning');
                return;
            }
        } else {
            state.fecha_desde = '';
            state.fecha_hasta = '';
        }

        cargarTodo();
    });

    // Botón Limpiar
    $('#da-btn-limpiar').on('click', function() {
        state = { periodo:'mes', fecha_desde:'', fecha_hasta:'', emp_ced:'', conceptos:'' };
        $('.da-period-btn').removeClass('active').filter('[data-periodo="mes"]').addClass('active');
        $('#da-rango-custom').hide();
        $('#da-fecha-desde,#da-fecha-hasta').val('');
        $('#da-sel-trabajador').val('').selectpicker('refresh');
        $('#da-sel-conceptos').val([]).selectpicker('refresh');
    });

    /* ================================================================
       INICIALIZACIÓN
       ================================================================ */
    cargarFiltroTrabajadores();
    cargarFiltroConceptos();
    // No se ejecutan consultas al cargar — el usuario debe presionar "Aplicar"

});
