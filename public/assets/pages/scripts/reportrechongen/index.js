$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#cedula").numeric();
    
});

function datosFac(){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        cedula               : eliminarFormatoRutret($("#cedula").val()),
        sucursal_id       : $("#sucursal_id").val(),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        aprobstatusdesc   : $("#aprobstatus option:selected").html(),
        comuna_id         : $("#comuna_id").val(),
        dte_id            : $("#dte_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        nrodocto          : $("#nrodocto").val(),
        statusgen         : 1,
        _token            : $('input[name=_token]').val()
    };
/*
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        cedula               : eliminarFormatoRutret($("#cedula").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        dte_id       : $("#dte_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        _token            : $('input[name=_token]').val()
    };
*/
    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&cedula="+data1.cedula +
    "&sucursal_id="+data1.sucursal_id +
    "&vendedor_id="+data1.vendedor_id +
    "&oc_id="+data1.oc_id +
    "&giro_id="+data1.giro_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipoentrega_id="+data1.tipoentrega_id +
    "&notaventa_id="+data1.notaventa_id +
    "&aprobstatus="+data1.aprobstatus +
    "&aprobstatusdesc="+data1.aprobstatusdesc +
    "&comuna_id="+data1.comuna_id +
    "&dte_id="+data1.dte_id +
    "&producto_id="+data1.producto_id +
    "&filtro="+data1.filtro +
    "&nrodocto="+data1.nrodocto +
    "&statusgen="+data1.statusgen +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
}


function consultarpdf(data){
    $.ajax({
        url: '/notaventaconsulta/exportPdf',
        type: 'GET',
        data: data,
        success: function (datos) {
            $("#midiv").html(datos);
            /*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla();
            }
            */
        }
    });
}

$("#cedula").focus(function(){
    $("#mov_nummon").empty();
    $(".selectpicker").selectpicker('refresh');
});

$("#cedula").blur(function(){
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
});

$("#btnbuscarempleado").click(function(event){
    $("#cedula").val("");
    $("#myModalBusqueda").modal('show');
});


function copiar_ced(id,ced){
	$("#myModalBusqueda").modal('hide');
	$("#cedula").val(ced);
	//$("#cedula").focus();
	$("#cedula").blur();
}

$("#btnpdf2").click(function()
{
    aux_titulo = 'Pendientes Solicitud Despacho';
    data = datosRecHon();
    $('#contpdf').attr('src', '/reportrechon/exportPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
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
    aux_titulo = 'Pendientes Solicitud Despacho';
    data = datosRecHon();
    console.log(data);
    $('#contpdf').attr('src', '/reportrechon/relHonPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
});