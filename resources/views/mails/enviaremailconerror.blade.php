<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recibo Honorarios</title>
</head>
<body>
   
    <p>Buen dia {{ucwords(strtolower($tabla->emp_nom))}}</p>
    <p>Adjunto se envia recibo honorarios profesionales</p>   
    <p><b>Datos:</b></p>
    <ul>
        <li><b>CI:</b> {{$tabla->emp_ced}}</li>
        <li><b>Nombre:</b> {{ucwords(strtolower($tabla->emp_nom . " " . $tabla->emp_ape))}}</li>
        <li><b>Periodo:</b> {{date("d/m/Y", strtotime($msg["nm_control"]->cot_fdesde)) . ' al ' . date("d/m/Y", strtotime($msg["nm_control"]->cot_fhasta))}}</li>
        <li><b>Recibo Nro:</b> {{$msg["nm_movnomtrab"]->mov_numrec}}</li>
    </ul>
    <br><br>
    <p>
        <b>Recuerde que puede consultar todos sus Recibos a traves del Siguiente enlace:</b> 
        <a href="{{urlRaiz()}}">
            {{urlRaiz()}}
        </a>
    </p>
</body>
</html>