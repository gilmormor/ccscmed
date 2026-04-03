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

    periodos();    
    $('#cono_monetario').on('change', function() {
        periodos();
    });
    $('#emp_codh').on('change', function() {
        periodos();
    });


});

function periodos(){
    $('#mov_nummon').empty(); 
    $(".selectpicker").selectpicker('refresh');
    var data = {
        emp_codh       : $('#emp_codh').val(),
        cono_monetario : $("#cono_monetario").val(),
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
        mov_codcar        : $("#mov_nummon option:selected").attr("mov_codcar"),
        cot_tipo          : $("#mov_nummon option:selected").attr("cot_tipo"),
        mov_codubica      : $("#mov_nummon option:selected").attr("mov_codubica"),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?mov_nummon="+data1.mov_nummon +
    "&cono_monetario="+data1.cono_monetario +
    "&cono_monetarioletra="+data1.cono_monetarioletra +
    "&emp_codh="+data1.emp_codh +
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


$("#btnpdf2").click(function()
{
    aux_titulo = 'Recibo de pago';
    data = datosRecEmp();
    //console.log(data);
    if(data.data1.cono_monetario != "" && data.data1.emp_codh != "" && data.data1.mov_nummon != ""){
        $('#contpdf').attr('src', '/reportrecemp/exportPdf/' + data.data2);
        $("#myModalpdf").modal('show'); 
    }else{
        swal({
        title: 'Debe seleccionar el Cono Monetario, Empresa y Periodo.',
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

    return 0;
    $('#contpdf').attr('src', '/reportrecemp/exportPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
});

$("#constancia").click(function()
{
    aux_titulo = 'Recibo de pago';
    data = datosRecEmp();
    //console.log(data);
    if(data.data1.cono_monetario != "" && data.data1.emp_codh != "" && data.data1.mov_nummon != ""){
        $('#contpdf').attr('src', '/reportrecemp/constanciaTrabajo/' + data.data2);
        $("#myModalpdf").modal('show'); 
    }else{
        swal({
        title: 'Debe seleccionar el Cono Monetario, Empresa y Periodo.',
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

    return 0;
    $('#contpdf').attr('src', '/reportrecemp/exportPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
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