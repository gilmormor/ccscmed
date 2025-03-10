$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    
});



$("#btnpdf2").click(function()
{
    aux_titulo = 'Pendientes Solicitud Despacho';
    //data = datosRecHon();
    $('#contpdf').attr('src', '/enviaremailconerrorgen/exportPdf/'); //+ data.data2);
    $("#myModalpdf").modal('show'); 
});
