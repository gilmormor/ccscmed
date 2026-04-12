$(document).ready(function () {

    var urlBuscar    = '/appactivaciones/buscar';
    var urlGenerar   = '/appactivaciones/generar';
    var urlRegenerar = '/appactivaciones/regenerar';
    var urlRevocar   = '/appactivaciones/revocar';

    // ── Buscar ────────────────────────────────────────────────────────────────
    $('#btn-buscar').on('click', function () {
        buscar();
    });

    $('#inp-buscar').on('keypress', function (e) {
        if (e.which === 13) buscar();
    });

    function buscar() {
        var q = $('#inp-buscar').val().trim();
        if (q.length < 2) {
            swal('Aviso', 'Ingresa al menos 2 caracteres para buscar.', 'warning');
            return;
        }
        $.ajax({
            url: urlBuscar,
            method: 'POST',
            data: { q: q, _token: $('input[name=_token]').val() },
            beforeSend: function () { $('#btn-buscar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>'); },
            success: function (resp) {
                renderTabla(resp.data);
            },
            error: function () {
                swal('Error', 'No se pudo realizar la búsqueda.', 'error');
            },
            complete: function () { $('#btn-buscar').prop('disabled', false).html('<i class="fa fa-search"></i> Buscar'); }
        });
    }

    // ── Render tabla ─────────────────────────────────────────────────────────
    function renderTabla(data) {
        var tbody = $('#tbody-empleados');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center text-muted">No se encontraron empleados.</td></tr>');
            return;
        }

        $.each(data, function (i, emp) {
            var estadoBadge, estadoLabel;
            if (emp.activo) {
                estadoBadge = 'badge-activo';
                estadoLabel = 'Activado';
            } else if (emp.codigo) {
                estadoBadge = 'badge-pendiente';
                estadoLabel = 'Pendiente';
            } else {
                estadoBadge = 'badge-sincodigo';
                estadoLabel = 'Sin código';
            }

            var codigoCol = emp.codigo
                ? '<span class="codigo-text">' + emp.codigo + '</span>'
                : '<span class="text-muted">—</span>';

            var activadoCol = emp.activado_en || '—';
            var dispositivoCol = emp.dispositivo_id
                ? '<small class="text-muted">' + emp.dispositivo_id.substring(0, 20) + '…</small>'
                : '—';

            var acciones = '';

            if (!emp.activo) {
                if (!emp.codigo) {
                    acciones += '<button class="btn btn-success btn-xs btn-generar" '
                        + 'data-ced="' + emp.emp_ced + '" data-nom="' + emp.emp_nom + ' ' + emp.emp_ape + '"'
                        + ' title="Generar código"><i class="fa fa-plus"></i> Generar</button> ';
                } else {
                    acciones += '<button class="btn btn-warning btn-xs btn-ver-codigo" '
                        + 'data-ced="' + emp.emp_ced + '" data-nom="' + emp.emp_nom + ' ' + emp.emp_ape + '"'
                        + ' data-codigo="' + emp.codigo + '"'
                        + ' title="Ver código"><i class="fa fa-eye"></i> Ver</button> ';
                    acciones += '<button class="btn btn-default btn-xs btn-regenerar" '
                        + 'data-ced="' + emp.emp_ced + '" data-nom="' + emp.emp_nom + ' ' + emp.emp_ape + '"'
                        + ' title="Regenerar código"><i class="fa fa-refresh"></i> Nuevo</button> ';
                }
            } else {
                acciones += '<button class="btn btn-danger btn-xs btn-revocar" '
                    + 'data-ced="' + emp.emp_ced + '" data-nom="' + emp.emp_nom + ' ' + emp.emp_ape + '"'
                    + ' title="Revocar acceso y resetear"><i class="fa fa-ban"></i> Revocar</button> ';
            }

            var tr = '<tr>'
                + '<td>' + emp.emp_ced + '</td>'
                + '<td>' + emp.emp_ape + ', ' + emp.emp_nom + '</td>'
                + '<td><span class="badge ' + estadoBadge + '">' + estadoLabel + '</span></td>'
                + '<td>' + codigoCol + '</td>'
                + '<td>' + activadoCol + '</td>'
                + '<td>' + dispositivoCol + '</td>'
                + '<td>' + acciones + '</td>'
                + '</tr>';

            tbody.append(tr);
        });
    }

    // ── Generar código ────────────────────────────────────────────────────────
    $(document).on('click', '.btn-generar', function () {
        var ced = $(this).data('ced');
        var nom = $(this).data('nom');
        accionConFeedback(urlGenerar, ced, function (resp) {
            mostrarModalCodigo(nom, ced, resp.codigo);
            buscar();
        });
    });

    // ── Ver código existente ──────────────────────────────────────────────────
    $(document).on('click', '.btn-ver-codigo', function () {
        var ced    = $(this).data('ced');
        var nom    = $(this).data('nom');
        var codigo = $(this).data('codigo');
        mostrarModalCodigo(nom, ced, codigo);
    });

    // ── Regenerar ─────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-regenerar', function () {
        var ced = $(this).data('ced');
        var nom = $(this).data('nom');
        swal({
            title: 'Regenerar código',
            text: '¿Invalidar el código actual y generar uno nuevo para ' + nom + '?',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Sí, regenerar' }
        }).then(function (confirma) {
            if (!confirma) return;
            accionConFeedback(urlRegenerar, ced, function (resp) {
                mostrarModalCodigo(nom, ced, resp.codigo);
                buscar();
            });
        });
    });

    // ── Revocar ───────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-revocar', function () {
        var ced = $(this).data('ced');
        var nom = $(this).data('nom');
        swal({
            title: 'Revocar acceso',
            text: '¿Seguro que deseas revocar el acceso de ' + nom + '? Deberá activar la app nuevamente.',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Sí, revocar' }
        }).then(function (confirma) {
            if (!confirma) return;
            accionConFeedback(urlRevocar, ced, function () {
                swal('Revocado', 'Acceso revocado correctamente.', 'success');
                buscar();
            });
        });
    });

    // ── Generar todos pendientes ──────────────────────────────────────────────
    $('#btn-generar-todos').on('click', function () {
        swal({
            title: 'Generar códigos pendientes',
            text: '¿Generar códigos para todos los empleados activos que aún no tienen uno?',
            icon: 'info',
            buttons: { cancel: 'Cancelar', confirm: 'Sí, generar' }
        }).then(function (confirma) {
            if (!confirma) return;
            $.ajax({
                url: '/appactivaciones/generar-todos',
                method: 'POST',
                data: { _token: $('input[name=_token]').val() },
                beforeSend: function () { $('#btn-generar-todos').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>'); },
                success: function (resp) {
                    swal('Listo', resp.message, 'success');
                    var q = $('#inp-buscar').val().trim();
                    if (q.length >= 2) buscar();
                },
                error: function () { swal('Error', 'No se pudo ejecutar la operación.', 'error'); },
                complete: function () { $('#btn-generar-todos').prop('disabled', false).html('<i class="fa fa-magic"></i> Generar pendientes'); }
            });
        });
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function accionConFeedback(url, ced, onSuccess) {
        $.ajax({
            url: url,
            method: 'POST',
            data: { emp_ced: ced, _token: $('input[name=_token]').val() },
            success: function (resp) { onSuccess(resp); },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error inesperado.';
                swal('Error', msg, 'error');
            }
        });
    }

    function mostrarModalCodigo(nombre, cedula, codigo) {
        // Modal en pantalla
        $('#modal-empleado-nombre').text(nombre);
        $('#modal-empleado-ced').text('Cédula: ' + cedula);
        $('#modal-codigo-valor').text(codigo);
        $('#modal-codigo').modal('show');

        // Área de impresión
        $('#print-nombre').text(nombre);
        $('#print-cedula').text('Cédula: ' + cedula);
        $('#print-codigo-valor').text(codigo);

        // Guardar título original y asignar nombre del archivo de impresión
        var tituloOriginal = document.title;
        var tituloPrint = 'codAct ' + codigo + ' CI ' + cedula + '-' + nombre;

        // Sobreescribir título al imprimir y restaurar al cerrar
        $(document).off('keydown.printfix');
        window._printCodigo = function () {
            document.title = tituloPrint;
            window.print();
            setTimeout(function () { document.title = tituloOriginal; }, 1000);
        };
    }

});
