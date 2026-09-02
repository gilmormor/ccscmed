$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    

    $('.date-picker').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");

   $('#annomes').on('change', function () {
       /*  data = datos();
        $('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load(); */
        //$('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + $("#annomes").val() + "/sucursal/" + $("#sucursal_id").val() ).load();
        //configurarTabla('#tabla-data');

    });

    
});

function datosRecHon(){
    var data1 = {
        nmcontrol_id      : $("#mov_nummon option:selected").attr('id'),
        mov_nummon        : $("#mov_nummon").val(),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?nmcontrol_id="+data1.nmcontrol_id +
    "&mov_nummon="+data1.mov_nummon +
    "&aprobstatusdesc="+data1.aprobstatusdesc +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
}


$("#btnpdf2").click(function()
{
    aux_titulo = 'Pendientes Solicitud Despacho';
    data = datosRecHon();
    $('#contpdf').attr('src', '/reportrechon/exportPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
});

$("#btnpdf3").click(function()
{
    aux_titulo = 'Relación de Honorarios';
    data = datosRecHon();
    $('#contpdf').attr('src', '/reportrechon/relHonPdf/' + data.data2);
    $("#myModalpdf").modal('show');
});

/* Constancia de honorarios: usa el rango de fechas, no el período de nómina,
   porque el documento certifica un promedio mensual de varios meses. */
$("#constancia").click(function()
{
    var desde = $('#fecha_desde').val();
    var hasta = $('#fecha_hasta').val();

    if (!desde || !hasta) {
        swal({
            title: 'Indique el rango de la constancia',
            text: 'Complete las fechas Desde y Hasta para calcular el promedio mensual facturado.',
            icon: 'warning',
            buttons: { confirm: 'Aceptar' }
        });
        return;
    }
    if (desde > hasta) {
        swal({
            title: 'Rango inválido',
            text: 'La fecha Desde no puede ser posterior a la fecha Hasta.',
            icon: 'warning',
            buttons: { confirm: 'Aceptar' }
        });
        return;
    }

    aux_titulo = 'Constancia de Honorarios';
    var qs = '?fecha_desde=' + desde +
             '&fecha_hasta=' + hasta +
             '&_token=' + $('input[name=_token]').val();

    $('#contpdf').attr('src', '/reportrechon/constanciaHonorarios/' + qs);
    $("#myModalpdf").modal('show');
});