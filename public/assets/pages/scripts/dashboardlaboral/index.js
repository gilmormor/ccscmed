/* ================================================================
   Dashboard Laboral — Portal Mi Nómina & Honorarios
   ccscmed | Chart.js + DataTables
   ================================================================ */

$(document).ready(function () {

    var chartEvolucion   = null;
    var chartComposicion = null;
    var tablaNomina      = null;
    var tablaHonorarios  = null;

    /* ── Formateo de montos ── */
    function fmtMonto(val) {
        val = parseFloat(val) || 0;
        return 'Bs ' + val.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* ── Año seleccionado ── */
    function getAnio() {
        return $('#sel-anio').val() || new Date().getFullYear();
    }

    /* ================================================================
       1. KPIs
       ================================================================ */
    function cargarKpis() {
        $('#kpi-sueldo, #kpi-honorarios, #kpi-recibos, #kpi-crecimiento')
            .html('<i class="fa fa-spinner fa-spin"></i>');

        $.get('/dashboardlaboral/kpis', { anio: getAnio() }, function (r) {
            $('#kpi-anio-lbl').text(r.anio);
            $('#kpi-sueldo').text(fmtMonto(r.sueldo_actual));
            $('#kpi-honorarios').text(fmtMonto(r.total_honorarios));
            $('#kpi-recibos').text(r.total_recibos);

            var cVal = parseFloat(r.crecimiento) || 0;
            var cls  = cVal > 0 ? 'pos' : (cVal < 0 ? 'neg' : 'neu');
            var signo = cVal > 0 ? '+' : '';
            $('#kpi-crecimiento').html('<span class="badge-crec ' + cls + '">' + signo + cVal + '%</span>');

            if (r.periodo_desde && r.periodo_hasta) {
                var fd = new Date(r.periodo_desde).toLocaleDateString('es-VE', {day:'2-digit',month:'short',year:'numeric'});
                var fh = new Date(r.periodo_hasta).toLocaleDateString('es-VE', {day:'2-digit',month:'short',year:'numeric'});
                $('#kpi-periodo').text(fd + ' — ' + fh);
            }
        }).fail(function () {
            $('#kpi-sueldo, #kpi-honorarios, #kpi-recibos, #kpi-crecimiento').text('—');
        });
    }

    /* ================================================================
       2. Gráfico Evolución de Ingresos (línea + área)
       ================================================================ */
    function cargarEvolucion() {
        $('#loading-evolucion').show();
        $.get('/dashboardlaboral/evolucion', function (r) {
            $('#loading-evolucion').hide();

            var labels  = r.sueldos.map(function (s) { return s.periodo; });
            var dataSud = r.sueldos.map(function (s) { return parseFloat(s.monto) || 0; });

            // Mapear honorarios al mismo eje de períodos
            var honMap = {};
            r.honorarios.forEach(function (h) { honMap[h.periodo] = parseFloat(h.monto) || 0; });
            var dataHon = labels.map(function (l) { return honMap[l] || 0; });

            var ctx = document.getElementById('chart-evolucion').getContext('2d');
            if (chartEvolucion) chartEvolucion.destroy();

            chartEvolucion = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Nómina (Bs)',
                            data: dataSud,
                            borderColor: '#1a3a5c',
                            backgroundColor: 'rgba(26,58,92,0.08)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#1a3a5c'
                        },
                        {
                            label: 'Honorarios (Bs)',
                            data: dataHon,
                            borderColor: '#0d7e6e',
                            backgroundColor: 'rgba(13,126,110,0.08)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#0d7e6e'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.dataset.label + ': ' + fmtMonto(ctx.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: function (v) { return 'Bs ' + v.toLocaleString('es-VE'); },
                                font: { size: 10 }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        }).fail(function () { $('#loading-evolucion').text('Sin datos disponibles.'); });
    }

    /* ================================================================
       3. Gráfico Composición del Recibo (dona)
       ================================================================ */
    function cargarComposicion() {
        $('#loading-composicion').show();
        $.get('/dashboardlaboral/composicion', function (r) {
            $('#loading-composicion').hide();

            var asi = parseFloat(r.asignaciones) || 0;
            var ded = parseFloat(r.deducciones)  || 0;

            var ctx = document.getElementById('chart-composicion').getContext('2d');
            if (chartComposicion) chartComposicion.destroy();

            chartComposicion = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Asignaciones', 'Deducciones'],
                    datasets: [{
                        data: [asi, ded],
                        backgroundColor: ['#0d7e6e', '#e74c3c'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    var total = asi + ded;
                                    var pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                    return ctx.label + ': ' + fmtMonto(ctx.parsed) + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });

            var total = asi + ded;
            var pctD  = total > 0 ? ((ded / total) * 100).toFixed(1) : 0;
            $('#composicion-totales').html(
                '<span style="color:#0d7e6e"><i class="fa fa-arrow-up"></i> ' + fmtMonto(asi) + '</span>' +
                '&nbsp;&nbsp;' +
                '<span style="color:#e74c3c"><i class="fa fa-arrow-down"></i> ' + fmtMonto(ded) + ' (' + pctD + '%)</span>'
            );
        }).fail(function () { $('#loading-composicion').text('—'); });
    }

    /* ================================================================
       4. DataTable Historial Nómina
       ================================================================ */
    function iniciarTablaNomina() {
        if (tablaNomina) { tablaNomina.destroy(); }
        tablaNomina = $('#tabla-nomina').DataTable({
            ajax: { url: '/dashboardlaboral/historial-nomina', dataSrc: 'data' },
            pageLength: 8,
            order: [[0, 'desc']],
            columns: [
                { data: 'fdesde' },
                { data: 'fhasta' },
                {
                    data: 'mov_sueldo',
                    className: 'text-right',
                    render: function (d) { return fmtMonto(d); }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function (row) {
                        return '<a href="/reportrechon/exportPdf?nmcontrol_id=' + row.id +
                            '&mov_nummon=' + row.cot_numnom +
                            '" target="_blank" class="btn btn-xs btn-primary" title="Ver PDF">' +
                            '<i class="fa fa-file-pdf-o"></i> PDF</a>';
                    }
                }
            ],
            language: window.datatablesLangES || {}
        });
    }

    /* ================================================================
       5. DataTable Historial Honorarios
       ================================================================ */
    function iniciarTablaHonorarios() {
        if (tablaHonorarios) { tablaHonorarios.destroy(); }
        tablaHonorarios = $('#tabla-honorarios').DataTable({
            ajax: { url: '/dashboardlaboral/historial-honorarios', dataSrc: 'data' },
            pageLength: 8,
            order: [[0, 'desc']],
            columns: [
                { data: 'fdesde' },
                { data: 'fhasta' },
                {
                    data: 'total_honorarios',
                    className: 'text-right',
                    render: function (d) { return fmtMonto(d); }
                },
                {
                    data: 'pagado',
                    className: 'text-center',
                    render: function (d) {
                        return d == 1
                            ? '<span class="label label-success">Pagado</span>'
                            : '<span class="label label-warning">Pendiente</span>';
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function (row) {
                        return '<a href="/reportrechon/exportPdf?nmcontrol_id=' + row.nmcontrol_id +
                            '&mov_nummon=' + row.cot_numnom +
                            '" target="_blank" class="btn btn-xs btn-success" title="Ver PDF">' +
                            '<i class="fa fa-file-pdf-o"></i> PDF</a>';
                    }
                }
            ],
            language: window.datatablesLangES || {}
        });
    }

    /* ================================================================
       6. Botones último recibo / honorario
       ================================================================ */
    $('#btn-ultimo-recibo').on('click', function () {
        $.get('/dashboardlaboral/historial-nomina', function (r) {
            if (r.data && r.data.length > 0) {
                var row = r.data[0];
                var url = '/reportrechon/exportPdf?nmcontrol_id=' + row.id +
                          '&mov_nummon=' + row.cot_numnom;
                $('#contpdf').attr('src', url);
                $('#myModalpdf').modal('show');
            } else {
                swal('Sin datos', 'No hay recibos de nómina disponibles.', 'info');
            }
        });
    });

    $('#btn-ultimo-hon').on('click', function () {
        $.get('/dashboardlaboral/historial-honorarios', function (r) {
            if (r.data && r.data.length > 0) {
                var row = r.data[0];
                var url = '/reportrechon/exportPdf?nmcontrol_id=' + row.nmcontrol_id +
                          '&mov_nummon=' + row.cot_numnom;
                $('#contpdf').attr('src', url);
                $('#myModalpdf').modal('show');
            } else {
                swal('Sin datos', 'No hay recibos de honorarios disponibles.', 'info');
            }
        });
    });

    /* ================================================================
       7. Cambio de año → recargar KPIs y gráficos
       ================================================================ */
    $('#sel-anio').on('change', function () {
        cargarKpis();
        cargarEvolucion();
    });

    /* ================================================================
       8. Inicializar al cargar
       ================================================================ */
    cargarKpis();
    cargarEvolucion();
    cargarComposicion();
    iniciarTablaNomina();
    iniciarTablaHonorarios();

});
