$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    
});


function ajaxRequest(data,url,funcion) {
    aux_data = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='reportrechonsendemail'){
            }
		},
		error: function () {
		}
	});
}

function datosRecHon(){
    var data1 = {
        mov_nummon        : $("#mov_nummon").val(),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?mov_nummon="+data1.mov_nummon +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
}

$("#sendemail").click(function()
{
    data = datosRecHon();
    var ruta = '/reportrechonsendemail/sendemail';
    ajaxRequest(data.data1,ruta,'reportrechonsendemail');
});