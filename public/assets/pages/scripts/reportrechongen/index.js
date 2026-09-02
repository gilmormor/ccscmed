$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#cedula").numeric();
    
});


$("#cedula").focus(function(){
    $("#mov_nummon").empty();
    $(".selectpicker").selectpicker('refresh');
});

$("#cedula").blur(function(){
    cargardatoscedula();
});
function cargardatoscedula(){
    codigo = $("#cedula").val();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            emp_ced: $("#cedula").val(),
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/empleado/buscarCedula',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.length>0){
                    $.ajax({
                        url: '/reportrechongen/periodos',
                        type: 'POST',
                        data: data,
                        success: function (respuesta) {
                            if(respuesta.length>0){
                                //console.log(respuesta);
                                $("#mov_nummon").empty();
                                for(var i=0;i<respuesta.length;i++){
                                    $("#mov_nummon").append("<option value='" + respuesta[i].cot_numnom + "' id='" + respuesta[i].id + "'>" + respuesta[i].fdesde + " al " + respuesta[i].fhasta + "</option>")
                                }
                                $(".selectpicker").selectpicker('refresh');
                            }else{
                                //formato_cedula($("#cedula"));
                                swal({
                                    title: 'Cedula no tiene periodos de nomina.',
                                    text: "",
                                    icon: 'error',
                                    buttons: {
                                        confirm: "Aceptar",
                                    },
                                }).then((value) => {
                                    if (value) {
                                        $("#cedula").focus();
                                    }
                                });		
                            }
                        }
                    });
        

                }else{
                    //formato_cedula($("#cedula"));
                    swal({
                        title: 'Cedula no existe.',
                        text: "",
                        icon: 'error',
                        buttons: {
                            confirm: "Aceptar",
                            cancel: "Cancelar"
                        },
                    }).then((value) => {
                        if (value) {
                            $("#cedula").focus();
                        }
                    });		
                }
            }
        });
	}
}

$("#btnbuscarempleado").click(function(event){
    $("#cedula").val("");
    $("#myModalBusqueda").modal('show');
});


function copiar_ced(id,ced){
	$("#myModalBusqueda").modal('hide');
	$("#cedula").val(ced);
	//$("#cedula").focus();
	//$("#cedula").blur();
    cargardatoscedula();
}

$("#btnpdf2").click(function()
{
    data = datosRecHon();
    if(parseFloat(data.data1.nmcontrol_id) > 0 && parseFloat(data.data1.emp_ced) > 0 && parseFloat(data.data1.emp_ced) > 0){
        $('#contpdf').attr('src', '/reportrechon/exportPdf/' + data.data2);
        $("#myModalpdf").modal('show');
    }else{
        swal({
        title: 'Cedula o Periodo no pueden quedar vacios.',
        text: "",
        icon: 'warning',
        buttons: {
            confirm: "Aceptar"
        },
        }).then((value) => {
            if (value) {
            }
        });

    }

});

function datosRecHon(){
    var data1 = {
        nmcontrol_id      : $("#mov_nummon option:selected").attr('id'),
        emp_ced           : $("#cedula").val(),
        mov_nummon        : $("#mov_nummon").val(),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?nmcontrol_id="+data1.nmcontrol_id +
    "&mov_nummon="+data1.mov_nummon +
    "&emp_ced="+data1.emp_ced +
    "&aprobstatusdesc="+data1.aprobstatusdesc +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
}

$("#btnpdf3").click(function()
{
    data = datosRecHon();
    if(parseFloat(data.data1.nmcontrol_id) > 0 && parseFloat(data.data1.emp_ced) > 0 && parseFloat(data.data1.emp_ced) > 0){
        $('#contpdf').attr('src', '/reportrechon/relHonPdf/' + data.data2);
        $("#myModalpdf").modal('show'); 
    }else{
        swal({
        title: 'Cedula o Periodo no pueden quedar vacios.',
        text: "",
        icon: 'warning',
        buttons: {
            confirm: "Aceptar"
        },
        }).then((value) => {
            if (value) {
            }
        });

    }

});
/* Constancia de honorarios: usa el rango de fechas, no el período de nómina,
   porque el documento certifica un promedio mensual de varios meses. */
$("#constancia").click(function()
{
    var cedula = $('#cedula').val();
    var desde  = $('#fecha_desde').val();
    var hasta  = $('#fecha_hasta').val();

    if (!cedula) {
        swal({
            title: 'Indique la cédula del médico',
            text: '',
            icon: 'warning',
            buttons: { confirm: 'Aceptar' }
        });
        return;
    }
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

    var qs = '?emp_ced=' + cedula +
             '&fecha_desde=' + desde +
             '&fecha_hasta=' + hasta +
             '&_token=' + $('input[name=_token]').val();

    $('#contpdf').attr('src', '/reportrechon/constanciaHonorarios/' + qs);
    $("#myModalpdf").modal('show');
});
