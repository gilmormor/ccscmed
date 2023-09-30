$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    data = datosod();

    $('#tabla-data-despachoord').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "listarorddesppage/"+data.data2,
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'fechaestdesp'},
            {data: 'razonsocial'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
            {data: 'tipoentrega_nombre',className:"ocultar"},
            {data: 'icono',className:"ocultar"},
            {data: 'clientebloqueado_descripcion',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
            {data: 'rutacrear',className:"ocultar"},
            {data: 'updated_at',className:"ocultar"},            
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            //console.log(data.rutacrear);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden despacho: " + data.id + "' onclick='genpdfOD(" + data.id + ",1)'>"+
                    + data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(2).attr('data-order',data.fechaestdesp);
            aux_fecha = new Date(data.fechaestdesp);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

            
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(4).html(aux_text);
            }
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    data.notaventa_id +
                "</a>";
            $('td', row).eq(5).html(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" + 
                    data.despachosol_id + 
                "</a>";
            $('td', row).eq(6).html(aux_text);


            $('td', row).eq(8).attr('data-order',data.aux_totalkg);
            $('td', row).eq(8).attr('style','text-align:right');
            aux_text = MASKLA(data.aux_totalkg,2);
            $('td', row).eq(8).html(aux_text);
            $('td', row).eq(8).addClass('subtotalkg');

            
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(9).html(aux_text);
            $('td', row).eq(9).attr('style','text-align:center');

            if(data.clientebloqueado_descripcion != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cliente Bloqueado: " + data.clientebloqueado_descripcion + "'>"+
                        "<span class='fa fa-fw fa-lock text-danger text-danger' style='bottom: 0px;top: 2px;'></span>"+
                    "</a>";
            }else{
                /*
                "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='aprobarsol(" + i + "," + data.id + ")' title='Aprobar Orden Despacho'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+*/
/*
                "<a href='/despachoord/aproborddesp' class='btn-accion-tabla btn-sm tooltipsC btnaprobar' title='Aprobar Orden Despacho'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
*/
                aux_text = 
                "<a id='bntaproord'" + data.id + " name='bntaproord'" + data.id + " class='btn-accion-tabla btn-sm' onclick='aprobarord(" + data.id + "," + data.id + ")' title='Aprobar Orden Despacho' data-toggle='tooltip'>"+
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='despachoord' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"
                "</a>";
            }
            $('td', row).eq(14).addClass('updated_at');
            $('td', row).eq(14).attr('id','updated_at' + data.id);
            $('td', row).eq(14).attr('name','updated_at' + data.id);

            aux_text = "<a href='" + data.rutacrear + "' class='btn-accion-tabla tooltipsC' title='Hacer Guia Despacho: " + data.tipoentrega_nombre + "'>" +
                            "<button type='button' class='btn btn-default btn-xs'>" +
                                "<i class='fa fa-fw " + data.icono + "'></i>"+
                            "</button>" +
                        "</a>|" +
                        "<a onclick='anularguiafact(" + data.id + "," + data.id + ")' class='btn-accion-tabla btn-sm tooltipsC btndevord' title='Devolver Orden Despacho' data-toggle='tooltip'>" +
                            "<button type='button' class='btn btn-warning btn-xs'><i class='fa fa-fw fa-reply'></i></button>" +
                        "</a>";
            $('td', row).eq(15).html(aux_text);
        }
    });

/*
    $('.tablas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});
*/
    //consultar(datosod());
    $("#btnconsultar").click(function()
    {
        //consultar(datosod());
        data = datosod();
        console.log(data.data2);
        $('#tabla-data-despachoord').DataTable().ajax.url( "listarorddesppage/"+data.data2 ).load();

    });



    $("#btnpdf1").click(function()
    {
        consultarpdf(datosod());
    });

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });

    configurarTabla('.tablas');

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
            if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
					//$(".requeridos").keyup();
					quitarvalidacioneach();
                    $("#tituloAGFAC").html("Devolver Orden Despacho");
                    $("#statusM").val('2');
                    $(".selectpicker").selectpicker('refresh');
					$("#myModalanularguiafact").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
            if(funcion=='buscarTipoBodegaOrdDesp'){
                if(respuesta.datas.length > 0){
                    if(respuesta.datas.length == 1){	
						var data = {
							id    : $("#idanul").val(),
							nfila : $("#nfilaanul").val(),
							observacion : $("#observacionanul").val(),
							statusM : $("#statusM").val(),
							invbodega_id : respuesta.datas[0].id,
							pantalla_origen  : 1, //Para saber de donde viene la anulacion en este caso de la pantalla Asignar Guia
							_token: $('input[name=_token]').val()
						};
						var ruta = '/guardaranularguia';
						swal({
							title: '¿ Seguro desea continuar ?',
							text: "Esta acción no se puede deshacer!",
								icon: 'warning',
							buttons: {
								cancel: "Cancelar",
								confirm: "Aceptar"
							},
						}).then((value) => {
							if (value) {
								ajaxRequest(data,ruta,'guardaranularguia');
							}
						});

                    }else{
                        swal({
                            title: 'Existe mas de una Bodega de Despacho',
                            text: "Debe seleccionar una",
                            icon: 'warning',
                            buttons: {
                                confirm: "Aceptar"
                            },
                        }).then((value) => {
                            if (value) {
                            }
                        });
    
                    }
                }else{
                    swal({
                        title: 'No existe Bodega de Despacho',
                        text: "Debe ser creada la bodega de Despacho",
                        icon: 'warning',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                        }
                    });

                }
                return respuesta;
            }
            if(funcion=='guardaranularguia'){
				if (respuesta.mensaje == "ok") {
					$("#fila" + respuesta.nfila).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
		},
		error: function () {
		}
	});
}

function datosod(){
    var data = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        fechaestdesp      : $("#fechaestdesp").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        despachoord_id    : $("#despachoord_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?fechad="+data.fechad+"&fechah="+data.fechah +
    "&fechaestdesp=" + data.fechaestdesp +
    "&rut=" + data.rut +
    "&vendedor_id=" + data.vendedor_id +
    "&oc_id=" + data.oc_id +
    "&giro_id=" + data.giro_id + 
    "&areaproduccion_id=" + data.areaproduccion_id +
    "&tipoentrega_id=" + data.tipoentrega_id +
    "&notaventa_id=" + data.notaventa_id +
    "&aprobstatus=" + data.aprobstatus +
    "&comuna_id=" + data.comuna_id +
    "&despachoord_id=" + data.despachoord_id +
    "&filtro=" + data.filtro;

    var data = {
        data1 : data,
        data2 : data2
    };

    return data;
}

function consultar(data){
    $.ajax({
        url: '/despachosol/reportesoldesp',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('#pendientesoldesp');
                $('.datepickerfed').datepicker({
                    language: "es",
                    autoclose: true,
                    todayHighlight: true
                }).datepicker("setDate");
                let  table = $('#pendientesoldesp').DataTable();
                table
                    .on('draw', function () {
                        eventFired( 'Page' );
                    });
            
            }
        }
    });
}

function consultarcerrarNV(data){
    $.ajax({
        url: '/despachosol/reportesoldespcerrarNV',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('.tablascons');
            }
        }
    });
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

$("#rut").blur(function(){
	codigo = $("#rut").val();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		if(!dgv(codigo.substr(0, codigo.length-1))){
			swal({
				title: 'Dígito verificador no es Válido.',
				text: "",
				icon: 'error',
				buttons: {
					confirm: "Aceptar"
				},
			}).then((value) => {
				if (value) {
					//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
					$("#rut").focus();
				}
			});
			//$(this).val('');
		}else{
			var data = {
				rut: $("#rut").val(),
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/cliente/buscarCli',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						formato_rut($("#rut"));
					}else{
                        formato_rut($("#rut"));
                        swal({
                            title: 'Cliente no existe.',
                            text: "Aceptar para crear cliente temporal",
                            icon: 'error',
                            buttons: {
                                confirm: "Aceptar",
                                cancel: "Cancelar"
                            },
                        }).then((value) => {
                            if (value) {
                                limpiarclientemp();
                                
                                $("#myModalClienteTemp").modal('show');
                            }else{
                                $("#rut").focus();
                                //$("#rut").val('');
                            }
                        });		
					}
				}
			});
		}
	}
});

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
    $("#myModalBusqueda").modal('show');
});


function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}

function visto(id,visto){
    //alert($(this).attr("value"));
    var data = {
        id     : id,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/notaventa/visto/' + id;
    ajaxRequest(data,ruta,'vistonotaventa');
}

$(document).on("click", ".btndevsol", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Devolver Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("1");
    $("#boton").val("btndevsol");
    quitarValidacion($(".requeridos").prop('name'),$(".requeridos").attr('tipoval'));
    $("#myModaldevsoldeps").modal('show');
    
});

$(document).on("click", ".btncerrarsol", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Cerrar Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("2");
    $("#boton").val("btncerrarsol");
    quitarValidacion($(".requeridos").prop('name'),$(".requeridos").attr('tipoval'));
    $("#myModaldevsoldeps").modal('show');
    
});

$("#btnGuardarDSD").click(function(event){
    if(verificarFact())
	{
        swal({
            title: '¿ Desea ' + $('.modal-title').html() + ' ?',
            text: "Esta acción no se puede deshacer!",
            icon: 'warning',
            buttons: {
                cancel: "Cancelar",
                confirm: "Aceptar"
            },
        }).then((value) => {
            /*
            fila = $(this).closest("tr");
            form = $(this);
            id = fila.find('td:eq(0)').text();
                //alert(id);
            */
            var data = {
                id     : $("#despachosol_id").val(),
                nfila  : $("#nfilaDel").val(),
                obs    : $("#observacion").val(),
                status : $("#status").val(),
                _token : $('input[name=_token]').val()
            };
            if (value) {
                ajaxRequest(data,$("#ruta").val(),$("#boton").val(),form);
            }
        });
    }else{
		alertify.error("Falta incluir informacion");
	}
});


function verificarFact()
{
	var v1=0;
	var v2=0;
	
	v1=validacion('observacion','texto');
	v2=true;
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}


$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

function btnpdf(numrep){
    if(numrep==1){
        aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
        data = datosod();
        cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
                "&fechaestdesp=" + data.fechaestdesp +
                "&rut=" + data.rut +
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
    data = datosod();
    cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
            "&fechaestdesp=" + data.fechaestdesp +
            "&rut=" + data.rut +
            "&vendedor_id=" + data.vendedor_id +
            "&oc_id=" + data.oc_id +
            "&giro_id=" + data.giro_id + 
            "&areaproduccion_id=" + data.areaproduccion_id +
            "&tipoentrega_id=" + data.tipoentrega_id +
            "&notaventa_id=" + data.notaventa_id +
            "&aprobstatus=" + data.aprobstatus +
            "&comuna_id=" + data.comuna_id +
            "&id=" + data.id +
            "&filtro=" + data.filtro +
            "&producto_id=" + data.producto_id +
            "&aux_titulo=" + aux_titulo;
    $('#contpdf').attr('src', '/despachosol/pdfpendientesoldesp/'+cadena);
    $("#myModalpdf").modal('show'); 
});


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

function editfeced(id,i){
    $(".fechaestdesp").show();
    $(".fechaed").hide();
    $(".editfed").show();
    $(".savefed").hide();
    $("#fechaestdesp" + i).hide();
    $("#fechaed" + i).val($("#fechaestdesp" + i).html());
    $("#fechaed" + i).show();
    $("#editfed" + i).hide();
    $("#savefed" + i).show();
    $("#fechaed" + i).datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
    }).datepicker("setDate");
    $("#fechaed" + i).datepicker("refresh");
    $("#fechaed" + i).focus();
    //alert(i);
}


function savefeced(id,aux_i){
    swal({
        title: '¿ Seguro desea actualizar el registro ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                id : id,
                i  : aux_i,
                aux_fechaestdesp : $("#fechaed" + aux_i).val(),
                updated_at : $("#savefed" + aux_i).attr('updated_at'),
                _token : $('input[name=_token]').val()
            };
            var ruta = '/despachosol/guardarfechaed'; //Guardar Fecha estimada de despacho
            ajaxRequest(data,ruta,'guardarfechaed');
        }else{
            restbotoneditfeced(aux_i);
        }
    });
}

function restbotoneditfeced(i){
    $("#fechaestdesp" + i).show();
    $("#fechaed" + i).hide();
    $("#editfed" + i).show();
    $("#savefed" + i).hide();
    $(".datepicker").datepicker("refresh");
}

function anularguiafact(nfila,id){
	$("#idanul").val(id);
	$("#guiadespachoanul").val('');
	$("#nfilaanul").val(nfila);
	var data = {
		id    : id,
		nfila : nfila,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultaranularguiafact');
}

$("#btnGuardarGanul").click(function(event)
{
	event.preventDefault();
	if(verificarAnulGuia())
	{

		var data = {
			id         : $("#idanul").val(),
			nfila      : $("#nfilaanul").val(),
			tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
			_token: $('input[name=_token]').val()
		};
		var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
		respuesta = ajaxRequest(data,ruta,'buscarTipoBodegaOrdDesp');
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

function verificarAnulGuia()
{
	var v1=0;
	var v2=0;
	v2=validacion('statusM','combobox');
	v1=validacion('observacionanul','texto');
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}
