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
            {data: 'emp_nomape'}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
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