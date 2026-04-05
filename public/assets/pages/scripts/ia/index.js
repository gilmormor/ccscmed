$(document).ready(function () {

    var graficosActivos = {}; // guardamos instancias de Chart.js para destruirlas si se repite

    // ──────────────────────────────────────────────
    // Enviar con botón
    // ──────────────────────────────────────────────
    $('#btnEnviar').on('click', function () {
        enviar();
    });

    // Enviar con Ctrl+Enter
    $('#pregunta').on('keydown', function (e) {
        if (e.ctrlKey && e.key === 'Enter') {
            enviar();
        }
    });

    // Limpiar chat
    $('#btnLimpiar').on('click', function () {
        $('#chat-container').empty();
        graficosActivos = {};
    });

    // Ejemplos de preguntas
    $(document).on('click', '.ejemplo-pregunta', function (e) {
        e.preventDefault();
        $('#pregunta').val($(this).text().trim());
        $('#pregunta').focus();
    });

    // ──────────────────────────────────────────────
    // Función principal de envío
    // ──────────────────────────────────────────────
    function enviar() {
        var pregunta = $('#pregunta').val().trim();
        if (pregunta === '') return;

        agregarBurbujaUsuario(pregunta);
        $('#pregunta').val('');

        var idTyping = mostrarTyping();

        $.ajax({
            url: '/ia/preguntar',
            type: 'POST',
            data: {
                pregunta: pregunta,
                _token: $('input[name=_token]').val() || $('meta[name=csrf-token]').attr('content')
            },
            success: function (resp) {
                quitarTyping(idTyping);
                procesarRespuesta(resp);
            },
            error: function (xhr) {
                quitarTyping(idTyping);
                var msg = 'Error al conectar con la IA.';
                try {
                    var err = JSON.parse(xhr.responseText);
                    if (err.error) msg = err.error;
                } catch (e) {}
                agregarBurbujaError(msg);
            }
        });
    }

    // ──────────────────────────────────────────────
    // Procesar respuesta según tipo
    // ──────────────────────────────────────────────
    function procesarRespuesta(resp) {
        if (resp.error) {
            agregarBurbujaError(resp.error);
            return;
        }

        switch (resp.tipo) {
            case 'texto':
                agregarBurbujaIA('<p>' + escHtml(resp.respuesta) + '</p>');
                break;

            case 'tabla':
                agregarBurbujaIA(construirTabla(resp.datos, resp.respuesta));
                break;

            case 'grafico':
                var htmlGrafico = construirGrafico(resp);
                agregarBurbujaIA(htmlGrafico, function (burbujaId) {
                    renderizarGrafico(burbujaId, resp);
                });
                break;

            case 'excel':
                var htmlExcel = '';
                if (resp.respuesta) {
                    htmlExcel += '<p>' + escHtml(resp.respuesta) + '</p>';
                }
                htmlExcel += construirTabla(resp.datos, null);
                htmlExcel += '<br><button class="btn btn-success btn-sm btn-descargar-excel" '
                    + 'data-sql="' + escAttr(resp.sql) + '" '
                    + 'data-titulo="' + escAttr(resp.titulo_excel || 'reporte') + '">'
                    + '<i class="fa fa-file-excel-o"></i> Descargar Excel</button>';
                agregarBurbujaIA(htmlExcel);
                break;

            default:
                agregarBurbujaIA('<p>' + escHtml(JSON.stringify(resp)) + '</p>');
        }
    }

    // ──────────────────────────────────────────────
    // Descarga Excel
    // ──────────────────────────────────────────────
    $(document).on('click', '.btn-descargar-excel', function () {
        var sql    = $(this).data('sql');
        var titulo = $(this).data('titulo');
        var form   = $('<form method="POST" action="/ia/exportar-excel" target="_blank">'
            + '<input type="hidden" name="_token" value="' + ($('input[name=_token]').val() || $('meta[name=csrf-token]').attr('content')) + '">'
            + '<input type="hidden" name="sql" value="' + escAttr(sql) + '">'
            + '<input type="hidden" name="titulo" value="' + escAttr(titulo) + '">'
            + '</form>');
        $('body').append(form);
        form.submit();
        form.remove();
    });

    // ──────────────────────────────────────────────
    // Helpers de UI
    // ──────────────────────────────────────────────
    function agregarBurbujaUsuario(texto) {
        var html = '<div class="burbuja burbuja-usuario">'
            + '<div class="burbuja-label">Tú</div>'
            + escHtml(texto)
            + '</div>';
        $('#chat-container').append(html);
        scrollAbajo();
    }

    function agregarBurbujaIA(html, callback) {
        var id  = 'burbuja-ia-' + Date.now();
        var div = $('<div class="burbuja burbuja-ia" id="' + id + '">'
            + '<div class="burbuja-label">Asistente IA</div>'
            + html
            + '</div>');
        $('#chat-container').append(div);
        scrollAbajo();
        if (typeof callback === 'function') callback(id);
    }

    function agregarBurbujaError(msg) {
        var html = '<div class="burbuja burbuja-error">'
            + '<div class="burbuja-label"><i class="fa fa-exclamation-triangle"></i> Error</div>'
            + escHtml(msg)
            + '</div>';
        $('#chat-container').append(html);
        scrollAbajo();
    }

    function mostrarTyping() {
        var id = 'typing-' + Date.now();
        var html = '<div class="burbuja burbuja-ia typing-indicator" id="' + id + '">'
            + '<span></span><span></span><span></span>'
            + '</div>';
        $('#chat-container').append(html);
        scrollAbajo();
        return id;
    }

    function quitarTyping(id) {
        $('#' + id).remove();
    }

    function scrollAbajo() {
        var c = document.getElementById('chat-container');
        c.scrollTop = c.scrollHeight;
    }

    // ──────────────────────────────────────────────
    // Construir tabla HTML
    // ──────────────────────────────────────────────
    function construirTabla(datos, mensaje) {
        if (!datos || datos.length === 0) {
            return '<p><em>Sin datos para mostrar.</em></p>';
        }
        var cols = Object.keys(datos[0]);
        var html = '';
        if (mensaje) html += '<p>' + escHtml(mensaje) + '</p>';
        html += '<div style="overflow-x:auto;max-height:300px;overflow-y:auto;">';
        html += '<table class="table table-bordered table-condensed ia-tabla">';
        html += '<thead><tr>';
        cols.forEach(function (c) { html += '<th>' + escHtml(c) + '</th>'; });
        html += '</tr></thead><tbody>';
        datos.forEach(function (fila) {
            html += '<tr>';
            cols.forEach(function (c) {
                html += '<td>' + escHtml(String(fila[c] !== null && fila[c] !== undefined ? fila[c] : '')) + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    // ──────────────────────────────────────────────
    // Construir contenedor de gráfico
    // ──────────────────────────────────────────────
    function construirGrafico(resp) {
        var canvasId = 'chart-' + Date.now();
        var html = '';
        if (resp.grafico && resp.grafico.titulo) {
            html += '<p><strong>' + escHtml(resp.grafico.titulo) + '</strong></p>';
        }
        html += '<div class="ia-grafico-wrap">'
            + '<canvas id="' + canvasId + '" height="260"></canvas>'
            + '</div>';
        // guardamos canvasId en el resp para renderizar luego
        resp._canvasId = canvasId;
        return html;
    }

    function renderizarGrafico(burbujaId, resp) {
        var canvasId = resp._canvasId;
        if (!canvasId || !resp.datos || resp.datos.length === 0) return;

        var g         = resp.grafico || {};
        var labelCol  = g.label_col  || Object.keys(resp.datos[0])[0];
        var valueCol  = g.value_col  || Object.keys(resp.datos[0])[1];
        var tipoGraf  = g.tipo_grafico || 'bar';

        var labels = resp.datos.map(function (r) { return r[labelCol] !== null ? String(r[labelCol]) : ''; });
        var values = resp.datos.map(function (r) { return parseFloat(r[valueCol]) || 0; });

        var colores = generarColores(labels.length);

        var ctx = document.getElementById(canvasId);
        if (!ctx) return;

        if (graficosActivos[canvasId]) {
            graficosActivos[canvasId].destroy();
        }

        graficosActivos[canvasId] = new Chart(ctx, {
            type: tipoGraf,
            data: {
                labels: labels,
                datasets: [{
                    label: valueCol,
                    data: values,
                    backgroundColor: colores.bg,
                    borderColor: colores.border,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                legend: { display: (tipoGraf === 'pie' || tipoGraf === 'doughnut') },
                scales: (tipoGraf === 'pie' || tipoGraf === 'doughnut') ? {} : {
                    yAxes: [{ ticks: { beginAtZero: true } }]
                }
            }
        });

        scrollAbajo();
    }

    // ──────────────────────────────────────────────
    // Utilidades
    // ──────────────────────────────────────────────
    function generarColores(n) {
        var paleta = [
            '#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b',
            '#858796','#5a5c69','#2e59d9','#17a673','#2c9faf'
        ];
        var bg     = [];
        var border = [];
        for (var i = 0; i < n; i++) {
            bg.push(paleta[i % paleta.length] + 'cc'); // con transparencia
            border.push(paleta[i % paleta.length]);
        }
        return { bg: bg, border: border };
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escAttr(str) {
        return String(str).replace(/"/g, '&quot;');
    }
});
