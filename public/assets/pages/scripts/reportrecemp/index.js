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

    $('.date-picker-mes').datepicker({
        language: "es",
        format: "mm/yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
        todayHighlight: true
    });

    // Si solo hay 1 empresa disponible (vista empleado), seleccionarla automáticamente
    var opcionesEmpresa = $('#emp_codh option[value!=""]');
    if (opcionesEmpresa.length === 1) {
        $('#emp_codh').val(opcionesEmpresa.first().val());
        $(".selectpicker").selectpicker('refresh');
    }

    // Pulso inicial para llamar la atención sobre el botón
    $('#btnFiltrarPeriodos').addClass('btn-atencion');

    $('#btnFiltrarPeriodos').on('click', function() {
        if ($('#emp_codh').val() != "") {
            periodos();
            $(this).removeClass('btn-atencion');
            $('#mov_nummon').closest('.bootstrap-select').removeClass('periodo-requerido');
            $('#mov_nummon').closest('.col-xs-12.col-md-8').prev().find('label').removeClass('periodo-requerido-label');
        }
    });

   $('#annomes').on('change', function () {
       /*  data = datos();
        $('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load(); */
        //$('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + $("#annomes").val() + "/sucursal/" + $("#sucursal_id").val() ).load();
        //configurarTabla('#tabla-data');

    });

    $('#cono_monetario').on('change', function() {
        $('#mov_nummon').empty();
        $('#mov_nummon').append('<option value="" mov_codcar="" cot_tipo="" mov_codubica="">Seleccione...</option>');
        $(".selectpicker").selectpicker('refresh');
    });

    // Al recargar la página (F5) el browser puede restaurar el valor de emp_ced,
    // pero no dispara 'change'. Si tiene valor y emp_codh está vacío, llenamos las empresas.
    if ($('#emp_ced').length && $('#emp_ced').val() != '' && $('#emp_codh').val() == '') {
        empresasPorCedula();
    }

    // Limitar ítems visibles según tamaño de pantalla (Bootstrap Select inyecta max-height inline,
    // se necesita style.setProperty con 'important' para sobreescribirlo)
    $('#mov_nummon, #emp_codh').on('shown.bs.select', function() {
        var w = $(window).width();
        var maxItems = w <= 480 ? 4 : w <= 767 ? 6 : 10;
        var itemPx   = maxItems * 38;
        var $bs = $(this).closest('.bootstrap-select');
        $bs.find('.dropdown-menu')[0].style.setProperty('max-height', (itemPx + 52) + 'px', 'important');
        $bs.find('div.inner')[0].style.setProperty('max-height', itemPx + 'px', 'important');
        $bs.find('div.inner')[0].style.setProperty('overflow-y', 'scroll', 'important');
    });

});

function periodos(){
    $('#mov_nummon').empty();
    $(".selectpicker").selectpicker('refresh');
    var data = {
        emp_codh       : $('#emp_codh').val(),
        cono_monetario : $("#cono_monetario").val(),
        emp_ced        : $('#emp_ced').val() || "",
        fecha_desde    : $('#fecha_desde').val() || "",
        fecha_hasta    : $('#fecha_hasta').val() || "",
        _token         : $('input[name=_token]').val()
    };
    $("#mov_nummon").append(`<option value="" 
        mov_codcar=""
        cot_tipo=""
        mov_codubica=""
        >
            Seleccione...
        </option>`)
    if(data.emp_codh != ""){
        $.ajax({
            url: '/reportrecemp/periodos',
            type: 'POST',
            data: data,
            success: function(response) {
                //console.log(response.data);
                for (let i = 0; i < response.data.length; i++) {
                    $("#mov_nummon").append(`<option value="${response.data[i].cot_numnom}"
                        mov_codcar="${response.data[i].mov_codcar}"
                        cot_tipo="${response.data[i].cot_tipo}"
                        mov_codubica="${response.data[i].mov_codubica}"
                        >
                            ${response.data[i].fdesde} al ${response.data[i].fhasta} ${response.data[i].tmo_desc}
                        </option>`)
                }
                var primerPeriodo = $('#mov_nummon option[value!=""]').first();
                if (primerPeriodo.length) {
                    $('#mov_nummon').val(primerPeriodo.val());
                }
                $(".selectpicker").selectpicker('refresh');
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    }
}

function datosRecEmp(){
    var data1 = {
        cono_monetario    : $("#cono_monetario").val(),
        cono_monetarioletra : $("#cono_monetario option:selected").html(),
        mov_nummon        : $("#mov_nummon").val(),
        emp_codh          : $("#emp_codh").val(),
        emp_ced           : $("#emp_ced").val() || "",
        mov_codcar        : $("#mov_nummon option:selected").attr("mov_codcar"),
        cot_tipo          : $("#mov_nummon option:selected").attr("cot_tipo"),
        mov_codubica      : $("#mov_nummon option:selected").attr("mov_codubica"),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?mov_nummon="+data1.mov_nummon +
    "&cono_monetario="+data1.cono_monetario +
    "&cono_monetarioletra="+data1.cono_monetarioletra +
    "&emp_codh="+data1.emp_codh +
    "&emp_ced="+data1.emp_ced +
    "&mov_codcar="+data1.mov_codcar +
    "&cot_tipo="+data1.cot_tipo +
    "&mov_codubica="+data1.mov_codubica +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
}

function copiar_ced(_id, ced){
    $("#myModalBusqueda").modal('hide');
    $("#emp_ced").val(ced);
    empresasPorCedula();
}

$("#btnbuscarempleado").click(function(){
    $("#myModalBusqueda").modal('show');
});

$("#emp_ced").on('keypress', function(e){
    if(!/[0-9]/.test(String.fromCharCode(e.which))) {
        e.preventDefault();
    }
});

$("#emp_ced").on('input', function(){
    var val = $(this).val().replace(/[^0-9]/g, '').substring(0, 8);
    $(this).val(val);
});

$("#emp_ced").on('change', function(){
    if($(this).val() != ""){
        empresasPorCedula();
    } else {
        $('#emp_codh').empty();
        $('#emp_codh').append('<option value="">Seleccione...</option>');
        $('#mov_nummon').empty();
        $('#mov_nummon').append('<option value="" mov_codcar="" cot_tipo="" mov_codubica="">Seleccione...</option>');
        $(".selectpicker").selectpicker('refresh');
    }
});

function empresasPorCedula(){
    var cedula = $('#emp_ced').val();
    if(cedula == "") return;

    $('#emp_codh').empty();
    $('#emp_codh').append('<option value="">Seleccione...</option>');
    $('#mov_nummon').empty();
    $('#mov_nummon').append('<option value="" mov_codcar="" cot_tipo="" mov_codubica="">Seleccione...</option>');
    $(".selectpicker").selectpicker('refresh');

    $.ajax({
        url: '/reportrecemp/empresas',
        type: 'POST',
        data: {
            emp_ced: cedula,
            _token: $('input[name=_token]').val()
        },
        success: function(response) {
            if(response.data.length === 0){
                swal({
                    title: 'Sin información',
                    text: 'La cédula ' + cedula + ' no tiene información para mostrar.',
                    icon: 'warning',
                    buttons: { confirm: 'Aceptar' }
                });
                return;
            }
            for(var i = 0; i < response.data.length; i++){
                $('#emp_codh').append('<option value="' + response.data[i].emp_codh + '">' + response.data[i].emp_nombre + '</option>');
            }
            if (response.data.length === 1) {
                $('#emp_codh').val(response.data[0].emp_codh);
            }
            $(".selectpicker").selectpicker('refresh');
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    });
}


function alertaCamposRequeridos(data1) {
    var sinPeriodo = data1.mov_nummon == "";
    var sinEmpresa = data1.emp_codh == "";

    // Marcar visualmente el campo Período si está vacío
    if (sinPeriodo) {
        $('#mov_nummon').closest('.bootstrap-select').addClass('periodo-requerido');
        $('#mov_nummon').closest('.col-xs-12.col-md-8').prev().find('label').addClass('periodo-requerido-label');
        $('#btnFiltrarPeriodos').addClass('btn-atencion');
    }

    var texto = sinPeriodo
        ? 'Seleccione la Empresa, luego haga clic en "Filtrar períodos" y elija un Período de la lista.'
        : 'Debe seleccionar el Cono Monetario, Empresa y Período' + ($('#emp_ced').length ? ' y Cédula' : '') + '.';

    swal({
        title: 'Faltan datos requeridos',
        text: texto,
        icon: 'warning',
        buttons: { confirm: "Aceptar" },
    });
}

$("#btnpdf2").click(function()
{
    aux_titulo = 'Recibo de pago';
    data = datosRecEmp();
    var cedValida = ($('#emp_ced').length === 0) || (data.data1.emp_ced != "");
    if(data.data1.cono_monetario != "" && data.data1.emp_codh != "" && data.data1.mov_nummon != "" && cedValida){
        $('#contpdf').attr('src', '/reportrecemp/exportPdf/' + data.data2);
        $("#myModalpdf").modal('show');
    }else{
        alertaCamposRequeridos(data.data1);
    }
});

$("#constancia").click(function()
{
    aux_titulo = 'Constancia de Trabajo';
    data = datosRecEmp();
    var cedValida = ($('#emp_ced').length === 0) || (data.data1.emp_ced != "");
    if(data.data1.cono_monetario != "" && data.data1.emp_codh != "" && data.data1.mov_nummon != "" && cedValida){
        $('#contpdf').attr('src', '/reportrecemp/constanciaTrabajo/' + data.data2);
        $("#myModalpdf").modal('show');
    }else{
        alertaCamposRequeridos(data.data1);
    }
});


$("#btnpdf3").click(function()
{
    aux_titulo = 'Pendientes Solicitud Despacho';
    data = datosRecHon();
    $('#contpdf').attr('src', '/reportrechon/relHonPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
});

function cedulaPuntos(cedula)
{
	var num = cedula.replace(/\./g,'');
	if(!isNaN(num))
	{
		num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
		num = num.split('').reverse().join('').replace(/^[\.]/,'');
		var primer = cedula.substr(0,1);
		return primer+'-'+num;
	}
}