/* ================================================================
   SPG KEY — replica del calculo del sistema local (Visual FoxPro 8)
   ================================================================
   aux_resul = producto de los codigos ASCII de cada caracter del Serial
   aux_clave = ROUND(aux_resul / (dia * mes * Codigo1 * Codigo2), 0)

   Se usa aritmetica de punto flotante normal (JS Number), NO BigInt:
   tanto VFP como JS usan double de 8 bytes (IEEE 754) para numeros, asi
   que replicar la operacion tal cual, sin forzar precision entera exacta,
   es lo que da el mismo resultado que el sistema local para cualquier
   longitud de Serial.
   ================================================================ */
$(document).ready(function () {

    $('#spg_generar').on('click', generarClave);

    $('#spg_salir').on('click', function () {
        window.location.href = '/';
    });

});

function generarClave()
{
    var serial = $.trim($('#spg_serial').val());
    var dia    = parseInt($('#spg_dia').val(), 10);
    var mes    = parseInt($('#spg_mes').val(), 10);
    var cod1   = parseFloat($('#spg_codigo1').val());
    var cod2   = parseFloat($('#spg_codigo2').val());

    if (!serial) {
        swal({ title: 'Indique el Serial', text: '', icon: 'warning', buttons: { confirm: 'Aceptar' } });
        $('#spg_serial').focus();
        return;
    }
    if (!cod1 || !cod2) {
        swal({
            title: 'Faltan datos',
            text: 'Código1 y Código2 son obligatorios.',
            icon: 'warning',
            buttons: { confirm: 'Aceptar' }
        });
        return;
    }

    // aux_resul: producto del codigo ASCII de cada caracter (asc()).
    // iif(aux_d<>0, aux_d, 1): un codigo 0 (caracter nulo) se toma como 1
    // para no anular todo el producto; en la practica no ocurre al
    // escribir texto normal, pero se respeta la regla tal cual esta en VFP.
    var resul = 1;
    for (var i = 0; i < serial.length; i++) {
        var d = serial.charCodeAt(i);
        if (d === 0) d = 1;
        resul *= d;
    }

    var divisor = dia * mes * cod1 * cod2;
    var clave = Math.round(resul / divisor);

    $('#spg_clave').val(clave);
}
