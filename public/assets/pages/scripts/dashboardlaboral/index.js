/* ================================================================
   Dashboard Laboral — Portal Mi Nómina & Honorarios
   ccscmed · Chart.js + DataTables (ES)
   ================================================================ */

$(document).ready(function () {

    var chartEvolucion   = null;
    var chartComposicion = null;
    var tablaHistorial   = null;

    /* ── DataTables en español ── */
    var dtES = {
        processing:   'Procesando...',
        search:       'Buscar:',
        lengthMenu:   'Mostrar _MENU_ registros',
        info:         'Mostrando _START_ a _END_ de _TOTAL_ registros',
        infoEmpty:    'Mostrando 0 registros',
        infoFiltered: '(filtrado de _MAX_ registros totales)',
        zeroRecords:  'No se encontraron resultados',
        emptyTable:   'No hay datos disponibles',
        paginate: { first:'Primera', previous:'Anterior', next:'Siguiente', last:'Última' }
    };

    /* ── Formateo de montos ── */
    function fmtMonto(val) {
        return 'Bs ' + parseFloat(val || 0).toFixed(2);
    }

    /* ── Helpers de estado ── */
    function getAnio()    { return $('#sel-anio').val(); }
    function getCedula()  { return $('#cedula-activa').val() || ''; }
    function setCedula(c) { $('#cedula-activa').val(c); }

    function buildQS(extra) {
        var p = {};
        var a = getAnio(), c = getCedula();
        if (a) p.anio    = a;
        if (c) p.emp_ced = c;
        if (extra) $.extend(p, extra);
        var qs = $.param(p);
        return qs ? '?' + qs : '';
    }

    /* ================================================================
       SPINNER EN BOTONES
       Cualquier botón que dispare una acción backend debe llamar
       btnLoading($btn) al inicio y se restaura automáticamente.
       ================================================================ */
    var _activeBtn     = null;
    var _activeBtnHtml = null;

    function btnLoading($btn) {
        if (!$btn || !$btn.length) return;
        // Nunca sobreescribir si ya hay un botón activo en espera
        // (evita guardar el spinner como "html original")
        if (_activeBtn) return;
        _activeBtn     = $btn;
        _activeBtnHtml = $btn.html();
        $btn.prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i>');
    }

    function btnRestore() {
        if (_activeBtn) {
            _activeBtn.prop('disabled', false).html(_activeBtnHtml);
            _activeBtn     = null;
            _activeBtnHtml = null;
        }
    }

    /* ================================================================
       MODAL PDF centralizado
       $btn: botón que originó la acción — muestra spinner hasta que
             el PDF carga o el usuario cierra el modal.
       ================================================================ */
    function abrirPdfModal(url, titulo, $btn) {
        btnLoading($btn);

        $('#dl-modal-pdf-titulo').text(titulo || 'Documento');
        $('#dl-modal-pdf-download').attr('href', url);

        var $frame = $('#dl-modal-pdf-frame');
        $frame.attr('src', '');

        // Restaurar botón cuando el PDF termina de cargarse en el iframe
        $frame.off('load').on('load', function () {
            btnRestore();
        });

        $('#dl-modal-pdf-print').off('click').on('click', function () {
            var f = document.getElementById('dl-modal-pdf-frame');
            if (f && f.contentWindow) {
                try { f.contentWindow.focus(); f.contentWindow.print(); }
                catch(e) { window.open(url, '_blank'); }
            }
        });

        $('#dl-modal-pdf').modal('show');
        // Asignar src después del show para que el iframe se inicie correctamente
        setTimeout(function() { $frame.attr('src', url); }, 100);
    }

    // Restaurar botón si el modal se cierra antes de que termine la carga
    $('#dl-modal-pdf').on('hidden.bs.modal', function () {
        $('#dl-modal-pdf-frame').attr('src', '');
        btnRestore();
    });

    /**
     * URL del recibo incluyendo emp_ced cuando el admin
     * consulta un empleado ajeno.
     */
    function buildPdfUrl(nmcontrol_id, mov_nummon) {
        var url = '/reportrechon/exportPdf?nmcontrol_id=' + nmcontrol_id +
                  '&mov_nummon=' + mov_nummon;
        var ced = getCedula();
        if (ced) url += '&emp_ced=' + ced;
        return url;
    }

    /* ================================================================
       1. KPIs
       ================================================================ */
    function cargarKpis() {
        $('#kpi-ultimo-hon, #kpi-hon-12m, #kpi-periodos, #kpi-promedio')
            .html('<i class="fa fa-spinner fa-spin"></i>');

        var anio = getAnio();
        $.get('/dashboardlaboral/kpis' + buildQS(), function (r) {
            if (r.sin_cedula) {
                // Admin sin empleado seleccionado — mostrar guión, no es un error
                $('#kpi-ultimo-hon, #kpi-hon-12m, #kpi-periodos, #kpi-promedio').text('—');
                $('#kpi-periodo').text('Selecciona un empleado para ver datos');
                return;
            }
            $('#kpi-ultimo-hon').text(fmtMonto(r.ultimo_neto));
            $('#kpi-hon-12m').text(fmtMonto(r.total_neto));
            $('#kpi-periodos').text(r.total_periodos);
            $('#kpi-promedio').text(fmtMonto(r.promedio));

            $('#kpi-anio-lbl').text(anio ? anio : '(histórico)');
            $('#kpi-hon-sub').text(anio ? 'Neto año ' + anio : 'Neto histórico total');

            if (r.periodo_fdesde && r.periodo_fhasta) {
                $('#kpi-periodo').text(r.periodo_fdesde + ' — ' + r.periodo_fhasta);
            }
        }).fail(function () {
            $('#kpi-ultimo-hon, #kpi-hon-12m, #kpi-periodos, #kpi-promedio').text('—');
        });
    }

    /* ================================================================
       2. Gráfico Evolución mensual — neto por mes
       ================================================================ */
    function cargarEvolucion() {
        $('#loading-evolucion').show();
        $('#evolucion-empty').hide();
        if (chartEvolucion) { chartEvolucion.destroy(); chartEvolucion = null; }

        $.get('/dashboardlaboral/evolucion' + buildQS(), function (r) {
            $('#loading-evolucion').hide();
            if (r.sin_cedula) { $('#evolucion-empty').show(); return; }

            var meses = r.meses || [];
            if (!meses.length) { $('#evolucion-empty').show(); return; }

            var labels = meses.map(function(m) { return m.mes_label; });
            var dataAsig = meses.map(function(m) { return parseFloat(m.asignaciones) || 0; });
            var dataDed  = meses.map(function(m) { return parseFloat(m.deducciones)  || 0; });
            var dataNeto = meses.map(function(m) {
                return (parseFloat(m.asignaciones) || 0) - (parseFloat(m.deducciones) || 0);
            });

            var ctx = document.getElementById('chart-evolucion').getContext('2d');
            chartEvolucion = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Asignaciones (Bs)',
                            data: dataAsig,
                            backgroundColor: 'rgba(13,126,110,0.72)',
                            borderColor: '#0d7e6e',
                            borderWidth: 1,
                            borderRadius: 3
                        },
                        {
                            label: 'Deducciones (Bs)',
                            data: dataDed,
                            backgroundColor: 'rgba(231,76,60,0.65)',
                            borderColor: '#e74c3c',
                            borderWidth: 1,
                            borderRadius: 3
                        },
                        {
                            label: 'Neto (Bs)',
                            data: dataNeto,
                            type: 'line',
                            borderColor: '#1a3a5c',
                            backgroundColor: 'rgba(26,58,92,0.08)',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#1a3a5c',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: { position:'top', labels:{ fontSize:11, padding:10 } },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                return label + ': Bs ' + parseFloat(tooltipItem.yLabel || 0).toFixed(2);
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(v) { return 'Bs ' + parseFloat(v).toFixed(2); },
                                fontSize: 10
                            },
                            gridLines: { color: 'rgba(0,0,0,0.04)' }
                        }],
                        xAxes: [{ ticks:{ fontSize:10 }, gridLines:{ display:false } }]
                    }
                }
            });
        }).fail(function() { $('#loading-evolucion').hide(); $('#evolucion-empty').show(); });
    }

    /* ================================================================
       3. Donut — honorarios por tipo de documento (nm_honpacientedet)
       Siempre histórico total, independiente del año
       ================================================================ */
    function cargarComposicion() {
        $('#loading-composicion').show();
        $('#chart-composicion').hide();
        $('#composicion-empty').hide();
        if (chartComposicion) { chartComposicion.destroy(); chartComposicion = null; }

        var qs = getCedula() ? '?emp_ced=' + getCedula() : '';
        $.get('/dashboardlaboral/composicion' + qs, function (r) {
            $('#loading-composicion').hide();
            if (r.sin_cedula) { $('#composicion-empty').show(); return; }
            var tipos = r.tipos || [];
            if (!tipos.length || !r.total) { $('#composicion-empty').show(); return; }

            var labels = tipos.map(function(t) { return t.tipo; });
            var data   = tipos.map(function(t) { return parseFloat(t.total) || 0; });
            var colors = ['#0d7e6e','#1a3a5c','#e67e22','#2980b9','#8e44ad','#e74c3c'];
            var ctx = document.getElementById('chart-composicion').getContext('2d');
            $('#chart-composicion').show();

            chartComposicion = new Chart(ctx, {
                type: 'doughnut',
                data: { labels:labels, datasets:[{ data:data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth:2, borderColor:'#fff' }] },
                options: {
                    responsive: true,
                    cutoutPercentage: 60,
                    legend: { position:'bottom', labels:{ fontSize:10, padding:8 } },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label  = data.labels[tooltipItem.index] || '';
                                var value  = data.datasets[0].data[tooltipItem.index] || 0;
                                var pct    = r.total > 0 ? ((value / r.total) * 100).toFixed(1) : 0;
                                return label + ': Bs ' + parseFloat(value).toFixed(2) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            });
            $('#composicion-totales').html(
                '<strong>' + fmtMonto(r.total) + '</strong> total · ' +
                tipos.length + ' tipo(s)'
            );
        }).fail(function() { $('#loading-composicion').hide(); $('#composicion-empty').show(); });
    }

    /* ================================================================
       4. DataTable — historial de recibos (nm_movhist + nm_control)
       ================================================================ */
    function iniciarTablaHistorial() {
        if (tablaHistorial) { tablaHistorial.destroy(); tablaHistorial = null; }

        tablaHistorial = $('#tabla-honorarios').DataTable({
            ajax: { url: '/dashboardlaboral/historial-honorarios' + buildQS(), dataSrc:'data' },
            pageLength: 10,
            order: [[0, 'desc']],
            language: dtES,
            columns: [
                {
                    data:'fdesde', title:'Desde',
                    render: function(data, type, row) {
                        return (type === 'sort' || type === 'type') ? row.fecha_orden : data;
                    }
                },
                {
                    data:'fhasta', title:'Hasta',
                    render: function(data, type, row) {
                        return (type === 'sort' || type === 'type') ? row.fecha_orden : data;
                    }
                },
                {
                    data:'asignaciones', title:'Asignaciones', className:'text-right',
                    render: function(d) { return fmtMonto(d); }
                },
                {
                    data:'deducciones', title:'Deducciones', className:'text-right',
                    render: function(d) {
                        return '<span style="color:#e74c3c">' + fmtMonto(d) + '</span>';
                    }
                },
                {
                    data:'neto', title:'Neto', className:'text-right',
                    render: function(d) {
                        var v = parseFloat(d) || 0;
                        var color = v >= 0 ? '#0d7e6e' : '#e74c3c';
                        return '<strong style="color:' + color + '">' + fmtMonto(v) + '</strong>';
                    }
                },
                {
                    data:null, title:'Recibo', className:'text-center', orderable:false,
                    render: function(row) {
                        return '<button class="btn btn-xs btn-success btn-ver-pdf" ' +
                            'data-ctrl="' + row.nmcontrol_id + '" ' +
                            'data-nom="'  + row.cot_numnom   + '" ' +
                            'data-desde="' + row.fdesde + '" ' +
                            'data-hasta="' + row.fhasta + '">' +
                            '<i class="fa fa-file-pdf-o"></i> Ver</button>';
                    }
                }
            ]
        });
    }

    /* ================================================================
       5. Botones de documentos
       ================================================================ */
    $('#btn-constancia').on('click', function () {
        // La constancia necesita una cédula válida — empleado logueado o seleccionado por admin
        var ced = getCedula();
        var empCedula = $('#cedula-activa').val() || ced;
        if (!empCedula) {
            swal('Sin empleado', 'Selecciona un empleado primero usando el buscador.', 'info');
            return;
        }
        var url = '/dashboardlaboral/constancia-pdf' + (ced ? '?emp_ced='+ced : '');
        abrirPdfModal(url, 'Constancia de Trabajo', $(this));
    });

    $('#btn-ultimo-hon').on('click', function () {
        var $btn = $(this);
        // 1. Activar spinner y guardar el HTML original ("Ver")
        btnLoading($btn);

        var qs = getCedula() ? '?emp_ced='+getCedula() : '';
        $.get('/dashboardlaboral/historial-honorarios' + qs, function(r) {
            if (r.data && r.data.length > 0) {
                var row = r.data[0];
                var url = buildPdfUrl(row.nmcontrol_id, row.cot_numnom);
                // 2. Abrir modal SIN pasar $btn — btnLoading ya fue llamado arriba
                //    _activeBtn ya apunta a $btn con el HTML "Ver" guardado
                //    abrirPdfModal no sobreescribe _activeBtnHtml
                abrirPdfModal(url, 'Recibo — ' + row.fdesde + ' al ' + row.fhasta);
            } else {
                btnRestore(); // AJAX ok pero sin datos
                swal('Sin datos', 'No hay recibos disponibles.', 'info');
            }
        }).fail(function() {
            btnRestore(); // Error de red
        });
    });

    $(document).on('click', '.btn-ver-pdf', function () {
        var $btn = $(this);
        btnLoading($btn);
        var url  = buildPdfUrl($btn.data('ctrl'), $btn.data('nom'));
        abrirPdfModal(url, 'Recibo — ' + $btn.data('desde') + ' al ' + $btn.data('hasta'));
    });

    /* ================================================================
       6. Selector de año
       ================================================================ */
    $('#sel-anio').on('change', function () {
        cargarKpis();
        cargarEvolucion();
        iniciarTablaHistorial();
    });

    /* ================================================================
       7. Buscador de empleado (admin)
       ================================================================ */
    function recargarTodo() {
        cargarKpis();
        cargarEvolucion();
        cargarComposicion();
        iniciarTablaHistorial();
    }

    $('#btn-buscar-emp').on('click', function () {
        var ced = $.trim($('#input-cedula-emp').val());
        if (!ced || isNaN(ced)) { swal('Atención', 'Ingresa una cédula numérica válida.', 'warning'); return; }
        var $btn = $(this).html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
        $.get('/dashboardlaboral/kpis?emp_ced=' + ced, function (r) {
            $btn.html('<i class="fa fa-search"></i>').prop('disabled', false);
            if (r.error) {
                swal('No encontrado', 'Cédula ' + parseInt(ced).toLocaleString('es-VE') + ' no registrada.', 'error');
                return;
            }
            setCedula(ced);
            $('#emp-result-label').html(
                '<span class="emp-badge"><i class="fa fa-user"></i> C.I. ' +
                parseInt(ced).toLocaleString('es-VE') +
                ' &nbsp;<button class="btn-clear" id="btn-limpiar-emp">✕</button></span>'
            );
            recargarTodo();
        }).fail(function() {
            $btn.html('<i class="fa fa-search"></i>').prop('disabled', false);
            swal('Error', 'No se pudo consultar la cédula.', 'error');
        });
    });

    $('#input-cedula-emp').on('keypress', function(e) {
        if (e.which === 13) $('#btn-buscar-emp').trigger('click');
    });

    // Botón "Por nombre" — abre el modal de búsqueda de empleados
    $('#btn-buscar-nombre').on('click', function () {
        $('#input-cedula-emp').val('');
        $('#myModalBusqueda').modal('show');
    });

    $(document).on('click', '#btn-limpiar-emp', function() {
        setCedula(''); $('#emp-result-label').html(''); $('#input-cedula-emp').val('');
        recargarTodo();
    });

    /*
     * Sobreescribimos copiar_ced() definida en empleado/buscar.js
     * para que copie la cédula al campo del dashboard (no a #cedula)
     */
    window.copiar_ced = function (id, ced) {
        $('#myModalBusqueda').modal('hide');
        $('#input-cedula-emp').val(ced);
        // Disparamos la búsqueda automáticamente al seleccionar
        $('#btn-buscar-emp').trigger('click');
    };

    /* ================================================================
       INIT
       ================================================================ */
    cargarKpis();
    cargarEvolucion();
    cargarComposicion();
    iniciarTablaHistorial();

});
