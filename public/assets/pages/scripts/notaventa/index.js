$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
/*
    function generateBarcode(codbar,i){
        var value = codbar; //"7626513231424"; //$("#barcodeValue").val();
        var btype = "ean13" //$("input[name=btype]:checked").val();
        var renderer = "css"; //$("input[name=renderer]:checked").val();

        var settings = {
            output:renderer,
            bgColor: "#FFFFFF", // $("#bgColor").val(),
            color: "#000000", // $("#color").val(),
            barWidth: "1", //$("#barWidth").val(),
            barHeight: "50", //$("#barHeight").val(),
            moduleSize: "3", //$("#moduleSize").val(),
            posX: "5", //$("#posX").val(),
            posY: "5", //$("#posY").val(),
            addQuietZone: "1" //$("#quietZoneSize").val()
        };
7        if (renderer == 'canvas'){
            clearCanvas();
            $("#barcodeTarget").hide();
            $("#canvasTarget").show().barcode(value, btype, settings);
        } else {
            $("#canvasTarget").hide();
            $("#barcodeTarget" + i).html("").show().barcode(value, btype, settings);
        }
    }
*/


	$('#tabla-data-notaventas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		'processing'  : true,
		'serverSide'  : true,
		'ajax'        : "notaventapage",
		"order": [[ 0, "desc" ]],
		'columns'     : [
			{data: 'id'},
			{data: 'cotizacion_id'},
			{data: 'fechahora'},
			{data: 'razonsocial'},
			{data: 'pdfnv'},
			{data: 'oc_id'},
			{data: 'btnguardar',className:"ocultar"},
			{data: 'btnanular',className:"ocultar"},
			{data: 'aprobstatus',className:"ocultar"},
			{data: 'aprobobs',className:"ocultar"},
			{data: 'contador',className:"ocultar"},
			{data: 'oc_file',className:"ocultar"},
			//El boton eliminar esta en comentario Gilmer 23/02/2021
			{defaultContent : 
				"<a href='notaventa' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
					"<i class='fa fa-fw fa-pencil'></i>"+
				"</a>"}
		],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		},
		"createdRow": function ( row, data, index ) {
			$(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
			//$(row).addClass('todo-list1');
			colorFila = "";
			aprobstatus = 1;
			aux_data_toggle = "";
			aux_title = "";
			colorinfo = '';
			if(data.contador>0){
				colorFila = 'background-color: #87CEEB;';
				colorinfo = 'text-aqua';
				aprobstatus = 2;
				aux_data_toggle = "tooltip";
				aux_title = "Precio menor al valor en tabla";
			}
			if(data.aprobstatus==4){
				colorFila = 'background-color: #FFC6C6;';  //" style=background-color: #FFC6C6;  title=Rechazo por: $data->aprobobs data-toggle=tooltip"; //'background-color: #FFC6C6;'; 
				colorinfo = 'text-red';
				aux_data_toggle = "tooltip";
				aux_title = "Rechazado por: " + data.aprobobs;
			}
			aux_text = 
			"<a href='#'  class='tooltipsC' title='Nota de Venta' " +
				"onclick='genpdfNV(" + data.id + ",1)'>" + data.id + 
			"</a>";

			$('td', row).eq(0).html(aux_text);

			codigo = data.cotizacion_id;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
				aux_text = "";
			}else{
				aux_text = 
				"<a href='#'  class='tooltipsC' title='Cotizacion' " +
				"onclick='genpdfCOT(\"" + data.cotizacion_id + "\",1)'>" + data.cotizacion_id + 
				"</a>";
			}
			$('td', row).eq(1).html(aux_text);

			$('td', row).eq(2).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

			aux_text = 
				"<a class='btn-accion-tabla btn-sm btngenpdfNV1 tooltipsC action-buttons' title='Nota de venta: " + data.id + "'>" +
					"<i class='fa fa-fw fa-file-pdf-o'></i>" +
				"</a>"+
				"<a class='btn-accion-tabla btn-sm btngenpdfNV2 tooltipsC action-buttons' title='Precio x Kg: " + data.id + "'>" +
					"<i class='fa fa-fw fa-file-pdf-o'></i>" +
				"</a>";
			if(colorinfo != ""){
				aux_text +=
				"<a class='btn-sm tooltipsC action-buttons' title='" + aux_title + "'>" +
					"<i class='fa fa-fw fa-question-circle " + colorinfo + "'></i>" + 
				"</a>";
			}
	
			$('td', row).eq(4).html(aux_text);

			codigo = data.oc_file;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
				aux_text = "";
			}else{
				aux_text = 
				"<a href='#' class='tooltipsC' title='Orden de Compra' " +
				"onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + 
				"</a>";
			}
			$('td', row).eq(5).html(aux_text);

			aux_text = 
			"<div class='tools1'>" +
				"<a id='bntaprobnv" + data.id + "' name='bntaprobnv" + data.id + "' class='btn-accion-tabla btn-sm tooltipsC action-buttons' onclick='aprobarnv(" + data.id + "," + data.id + "," + aprobstatus + ")' title='Aprobar'>" +
					"<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>" + 
				"</a>"
			"</div>";
			$('td', row).eq(6).html(aux_text);

			aux_text = 
			"<div class='tools1'>" +
				"<a id='btnanularnv" + data.id + "' name='btnanularnv" + data.id + "' class='btn-accion-tabla btn-sm tooltipsC action-buttons' onclick='anularnv(" + data.id + "," + data.id + ")' title='Anular'>" +
					"<span class='glyphicon glyphicon-remove' style='bottom: 0px;top: 2px;'></span>" + 
				"</a>" +
			"</div>";
			$('td', row).eq(7).html(aux_text);
/*
			aux_text = 
			"<a class='btn-accion-tabla btn-sm' onclick='genpdfNV(" + data.id + ",1)' title='Nota de venta' data-toggle='tooltip'>" +
				"<i class='fa fa-fw fa-file-pdf-o'></i>" +
			"</a>" +
			"<a class='btn-accion-tabla btn-sm' onclick='genpdfNV(" + data.id + ",2)' title='Precio x Kg' data-toggle='tooltip'>" +
				"<i class='fa fa-fw fa-file-pdf-o'></i>" +
			"</a>";
			$('td', row).eq(7).html(aux_text);
*/
			if ( data.contador * 1 > 0 ) {
				//console.log(row);
				///$('tr').addClass('preciomenor');
				//$('td', row).parent().addClass('preciomenor tooltipsC');
				/*
				$(row).attr('style',colorFila);
				$(row).attr('data-toggle',"tooltip");
				$(row).attr('data-original-title',aux_title);
				*/
				//$(row).attr('class',"tooltip");
			}
			//clase tools1: hace el efecto de mostrar o no los botones al pasar el cursor sobre la fila 
			//Clase acciones: hace el efecto de atenuar los botones y poner un circulo al rededor del boton
			aux_text = 
				"<div class='tools11'>" +
					"<a id='bntaprobnv" + data.id + "' name='bntaprobnv" + data.id + "' class='btn-accion-tabla btn-sm tooltipsC action-buttons' onclick='aprobarnv(" + data.id + "," + data.id + "," + aprobstatus + ")' title='Aprobar'>" +
						"<!--<span class='glyphicon glyphicon-floppy-save sombra' style='bottom: 0px;top: 2px;'></span>-->" + 
						"<i class='fa fa-fw fa-save acciones1 fa-lg'></i>" +
					"</a>  " +
					"<a href='notaventa' class='btn-accion-tabla tooltipsC btnEditar action-buttons' title='Editar'>" +
						"<i class='fa fa-fw fa-pencil acciones1 fa-lg'></i>" +
					"</a>" +
					"<a id='btnanularnv" + data.id + "' name='btnanularnv" + data.id + "' class='btn-accion-tabla btn-sm tooltipsC action-buttons' onclick='anularnv(" + data.id + "," + data.id + ")' title='Anular'>" +
						"<!--<span class='glyphicon glyphicon-remove sombra' style='bottom: 0px;top: 2px;'></span>-->" + 
						"<i class='fa fa-fw fa-close acciones1 fa-lg text-danger'></i>" +
					"</a>  " +
				"</div>";
			$('td', row).eq(12).attr('style','padding-top: 0px;padding-bottom: 0px;');
			$('td', row).eq(12).html(aux_text);

		}
	});

	$('#tabla-data-productos1').DataTable({
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


	$("#btnGuardarM").click(function()
    {
        //generateBarcode();
    });

    aux_nfila=parseInt($("#tabla-data >tbody >tr").length);
    for(i=1; i<=aux_nfila; i++){
        //codbar = $("#barcodeTarget" + i).html();
        //generateBarcode(codbar,i);
    }
    //alert(aux_nfila);

    $("#cotizacion_idM").numeric();
    $('.form-group').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px'});
    $( "#myModalnumcot" ).draggable({opacity: 0.80, handle: ".modal-header"});
	$( "#myModalBusquedaCot" ).draggable({opacity: 0.80, handle: ".modal-header"});

	$('#myModalpdf').on('show.bs.modal', function () {
		$('.modal-body').css('height',$( window ).height()*0.75);
		});
	
});

function aprobarnv(i,id,aprobstatus){
	event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        aprobstatus : aprobstatus,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/notaventa/aprobarnotaventa/'+i;
	swal({
		title: '¿ Aprobar y enviar al siguiente paso ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'accionnotaventa');
		}
	});
}
function anularnv(i,id){
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/notaventa/anularnotaventa/'+i;
	swal({
		title: '¿ Está seguro que desea anular nota de venta ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'accionnotaventa');
		}
	});
}
function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='accionnotaventa'){
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
		},
		error: function () {
		}
	});
}


$("#btnnuevaNV").click(function(event){
    swal({
		title: '¿ Hacer nota de venta con Nro. Cotización ?',
		text: "",
		icon: 'success',
		buttons: {
			si: {
				text: "Si",
				value: "Si",
			},

			no: {
				text: "No",
				value: "No",
			},
			cancel: "Cancelar"
		},
	}).then((value) => {
		/*
		if (value) {
			limpiarCampos();
			$("#myModalnumcot .modal-body").removeAttr("style");
			$("#myModalnumcot").modal('show');
		}else{
			//alert('Sin Cotizacion');
			// *** REDIRECCIONA A UNA RUTA*** 
			var loc = window.location;
			window.location = loc.protocol+"//"+loc.hostname+"/notaventa/crear";
			// ******************************
        }
		*/
		switch (value) {
 
			case "Si":
				limpiarCampos();
				$("#myModalnumcot .modal-body").removeAttr("style");
				$("#myModalnumcot").modal('show');
				break;
		 
			case "No":
				//alert('Sin Cotizacion');
				// *** REDIRECCIONA A UNA RUTA*** 
				var loc = window.location;
				window.location = loc.protocol+"//"+loc.hostname+"/notaventa/crear";
				// ******************************
				break;
			default:
			  //swal("Got away safely!");
		}

	});
	
});

$("#cotizacion_idM").keyup(function(event){
    if(event.which==113){
        cargarpantallaBC();
    }
});
$("#btnbuscarcotizacion").click(function(event){
    cargarpantallaBC();
});

function cargarpantallaBC(){
    limpiarCampos();
	$("#myModalBusquedaCot .modal-body").removeAttr("style");
    $("#myModalBusquedaCot").modal('show');
}

function copiar_numcot(id){
	$("#myModalBusquedaCot").modal('hide');
	$("#cotizacion_idM").val(id);
	$("#cotizacion_idM").blur();
	verificar();
	//$("#cantM").focus();
}

$("#cotizacion_idM").blur(function(){
	codigo = $("#cotizacion_idM").val();
	//limpiarCampos();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		var data = {
			id: codigo,
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/cotizacion/buscarCotizacion',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log(respuesta);
				console.log(respuesta.cotizaciones)
				console.log(respuesta.cotizaciones["length"]);
				if(respuesta.cotizaciones["length"]>0){
					if(respuesta.cotizaciones[0]['descripbloqueo']==null){
						$("#razonsocialM").val(respuesta.cotizaciones[0]['razonsocial']);
					}else{
						swal({
							title: 'Cliente Bloqueado.',
							text: respuesta.cotizaciones[0]['descripbloqueo'],
							icon: 'error',
							buttons: {
								confirm: "Aceptar"
							},
						}).then((value) => {
							if (value) {
								//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
								$("#cotizacion_idM").val('');
								$("#cotizacion_idM").focus();
							}
						});
					}
				}else{
					$('#cotizacion_idM').val('');
					$('#razonsocialM').val('');
					swal({
						title: respuesta.mensaje,
						text: "", //"Presione F2 para buscar",
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
							$("#cotizacion_idM").focus();
						}
					});
				}
			}
		});
	}else{
		$('#cotizacion_idM').val('');
		$('#razonsocialM').val('');
	}
});

function limpiarCampos(){
	$("#cotizacion_idM").val('');
	$("#razonsocialM").val('');
	quitarVerificar();
};

$("#btnaceptar").click(function(){
	//alert('Prueba');
	if(verificar()==true){
		aux_numcot = $("#cotizacion_idM").val();
		$("#myModalnumcot").modal('hide');
		// *** REDIRECCIONA A UNA RUTA*** 
		var loc = window.location;
		window.location = loc.protocol+"//"+loc.hostname+"/notaventa/crearcot/" + aux_numcot;
		// ******************************
	}
    
    //route('notaventa.crear');
    /*
    punto = '';
    $.ajax({
        url: 'notaventa/crear',
        type: 'GET',
        data: {punto:punto},
        dataType: 'JSON',
        success: function(respuesta) {
          if (respuesta) {
            alertify.success("Exito...");return false;
          }else {
            alertify.error("Error...");return false;
          }
        }
    });*/

});

function verificar()
{
	aux_resp = true;
	$(".requeridos").each(function() {
		v1=validacion($(this).prop('name'),$(this).attr('tipoval'),$(this).parent().parent().attr('classorig'));
		if(v1==false){
			aux_resp = false;
		}
	});
	if(aux_resp){
		return true;
	}else{
		alertify.error("Falta incluir informacion");
		return false;
	}
		
	/*
	var v1=0,v2=0,v3=0,v4=0,v5=0,v6=0,v7=0,v8=0,v9=0,v10=0,v11=0,v12=0,v13,v14=0;
	
	v1=validacion('producto_idM','textootro','col-xs-12 col-sm-2');

	if (v1===false || v2===false || v3===false || v4===false || v5===false || v6===false || v7===false || v8===false || v9===false || v10===false || v11===false || v12===false || v13===false || v14===false)
	{
		//$("#exito").hide();
		//$("#error").show();
		return false;
	}else{
		//$("#error").hide();
		//$("#exito").show();
		return true;
	}
	*/

}

$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	//alert($(this).parent().parent().attr('classorig'));
	validacion($(this).prop('name'),$(this).attr('tipoval'),$(this).parent().parent().attr('classorig'));
});

function validacion(campo,tipo,columnas)
{
	var a=0;
	//columnas = $('#'+campo).parent().parent().attr("class");
	switch (tipo) 
	{ 
		case "texto": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().children('span').hide();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break
		case "textootro": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().parent().children('span').hide();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break
		case "numerico": 
			codigo = document.getElementById(campo).value;
			cajatexto = document.getElementById(campo).value;
			var caract = new RegExp(/^[0-9]+$/);

			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback' style='left: 90px;'></span>");
				//$('#'+campo).focus();
				return false;
			}
			else
			{
				if(caract.test(cajatexto) == false)
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().parent().children('span').text("Solo permite valores numericos").show();
					$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback' style='left: 90px;'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().parent().children('span').hide();
					$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'  style='left: 90px;'></span>");
					return true;				
				}
			}

		break 
		case "combobox": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().parent().children('span').hide();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break
		case "comboboxmult": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().parent().children('span').hide();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break 
		case "email": 
			cajatexto = document.getElementById(campo).value;
			var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
			if( cajatexto == null || cajatexto.length == 0 || /^\s+$/.test(cajatexto) )
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				if(caract.test(cajatexto) == false)
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().children('span').text("Correo no valido").show();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
					return true;				
				}
			} 
		break 
		default: 
		
	}
}

function quitarValidacion(campo,tipo,columnas)
{
	var a=0;
	//columnas = $('#'+campo).parent().parent().attr("class");
	switch (tipo) 
	{ 
		case "texto": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().attr("class", columnas);
			$('#'+campo).parent().children('span').hide();
			$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;
		break 
		case "textootro": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().parent().children('span').hide();
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;
		break 
		case "numerico": 
			codigo = document.getElementById(campo).value;
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().parent().children('span').hide();
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;
		break 
		case "combobox": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().parent().children('span').hide();
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;

		break 
		case "email": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().children('span').hide();
			$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
		break 
		default: 
		
	}
}

function quitarVerificar()
{
	aux_resp = true;
	$(".requeridos").each(function() {
		quitarValidacion($(this).prop('name'),$(this).attr('tipoval'),$(this).parent().parent().attr('classorig'));
	});
}


function genpdfNV(id,stareport){ //GENERAR PDF NOTA DE VENTA
	$("#myModalpdf").modal('show')
	$('#contpdf').attr('src', 'notaventa/'+id+'/'+stareport+'/exportPdf');
}
