$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    configTablaEmpleado();
});

function configTablaEmpleado(){
    aux_nfila = 0;
    $("#tabla-data-empleado").attr('style','');
    //$("#tabla-data-empleado").DataTable().fnDestroy();
    $('#tabla-data-empleado').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "empleado/empleadobuscarpage",
        'columns'     : [
            {data: 'emp_ced'},
            {data: 'emp_nomape'},
            {data: 'emp_email'}
        ],
		"language": {
            "sProcessing":   "Procesando...",
            "sLengthMenu":   "Mostrar _MENU_ registros",
            "sZeroRecords":  "No se encontraron resultados",
            "sEmptyTable":   "Ningún dato disponible en esta tabla",
            "sInfo":         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":    "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch":       "Buscar:",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            aux_onclick = "copiar_ced(0,'" + data.emp_ced + "')";

            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            $(row).attr('data-toggle', data.emp_ced);
            $(row).attr('title', "Click para seleccionar Empleado");
            $(row).attr('onClick', aux_onclick + ';');
        }
    });
}