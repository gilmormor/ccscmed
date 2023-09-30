$(document).ready(function () {
    $( "#nombre" ).focus();
    const reglas = {
        re_password: {
            equalTo: "#password"
        }
    };
    const mensajes = {
        re_password:
        {
            equalTo: 'Las contraseñas no coinciden'
        }
    };
    Biblioteca.validacionGeneral('form-general', reglas, mensajes);
    $('#password').on('change', function(){
        const valor = $(this).val();
        if(valor != ''){
            $('#re_password').prop('required', true);
        }else{
            $('#re_password').prop('required', false);
        }
    });

    $('#foto').fileinput({
        language: 'es',
        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
        maxFileSize: 400,
        showUpload: false,
        showClose: false,
        initialPreviewAsData: true,
        dropZoneEnabled: false,
        maxFileCount: 5,
        theme: "fa",
    });
});