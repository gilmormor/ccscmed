$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#cedula").numeric();
    $("#btnconsultar").click(function()
    {
        data = datosFac();
        $('#tabla-data-consulta').DataTable().ajax.url( "/reportdtefac/reportdtefacpage/" + data.data2 ).load();
    });
});

function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "order"       : [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}


function ajaxRequest(data,url,funcion) {
    aux_data = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='vistonotaventa'){
				if (respuesta.mensaje == "ok") {
					//$("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
            if(funcion=='btndevsol'){
                if (respuesta.mensaje == "ok") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "hijos"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                if(respuesta.mensaje.length > 10){
                                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
                                }else{
                                    Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                                }
                            }
                        }
                    }
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=='btncerrarsol'){
                if (respuesta.mensaje == "ok") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "hijos"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                            }
                        }
                    }
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=="guardarfechaed"){
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                restbotoneditfeced(aux_data.i)
                if(respuesta.error == 0){
                    $("#fechaestdesp" + aux_data.i).html($("#fechaed" + aux_data.i).val());
                    $("#savefed" + aux_data.i).attr('updated_at',respuesta.updated_at);
                }
            }
            if(funcion=='staverfacdesp'){
				if (respuesta.error == 0) {
                    $("#dtefac_updated_at" + aux_data.dte_id).html(respuesta.dtefac_updated_at);
				} else {
                    estaSeleccionado = $("#aux_staverfacdesp" + aux_data.dte_id).is(":checked");
                    if(estaSeleccionado){
                        $("#aux_staverfacdesp" + aux_data.dte_id).prop('checked',false);
                    }else{
                        $("#aux_staverfacdesp" + aux_data.dte_id).prop('checked',true);
                    }
				}
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
		},
		error: function () {
		}
	});
}

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
                                        $("#mov_nummon").append("<option value='" + respuesta[i].cot_numnom + "'>" + respuesta[i].fdesde + " al " + respuesta[i].fhasta + "</option>")
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

function btnpdf(numrep){
    if(numrep==1){
        aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
        data = datosFac();
        cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
                "&fechaestdesp=" + data.fechaestdesp +
                "&cedula=" + data.cedula +
                "&vendedor_id=" + data.vendedor_id +
                "&oc_id=" + data.oc_id +
                "&giro_id=" + data.giro_id + 
                "&areaproduccion_id=" + data.areaproduccion_id +
                "&tipoentrega_id=" + data.tipoentrega_id +
                "&notaventa_id=" + data.notaventa_id +
                "&aprobstatus=" + data.aprobstatus +
                "&comuna_id=" + data.comuna_id +
                "&id=" + data.id +
                "&filtro=" + data.filtro;
        $('#contpdf').attr('src', '/despachosol/pdfpendientesoldesp/'+cadena);
        $("#myModalpdf").modal('show'); 
    }
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
        emp_ced           : $("#cedula").val(),
        mov_nummon        : $("#mov_nummon").val(),
        aprobstatusdesc   : $("#aprobstatus option:selected").html(),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?mov_nummon="+data1.mov_nummon +
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


var eventFired = function ( type ) {
	total = 0;
	$("#pendientesoldesp tr .kgpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totalkg").html(MASKLA(total,2))
	total = 0;
	$("#pendientesoldesp tr .dinpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totaldinero").html(MASKLA(total,0))

}
