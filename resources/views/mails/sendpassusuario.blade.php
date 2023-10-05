<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recuperacion Contraseña</title>
</head>
<body>

    <p>{{ $cuerpo }}</p>
    
    <p>Buen dia estimado {{$tabla->nombre}}</p>
    <ul>
        <li><b>Nombre:</b> {{$tabla->nombre}}</li>
        <li><b>Email:</b> {{$tabla->email}}</li>
    </ul>
    <p><b>Credenciales de acceso:</b></p>
    <ul>
        <li><b>Usuario:</b> {{ $tabla->usuario }}</li>
        <li><b>Contraseña:</b> {{ $tabla->pass }}</li>
    </ul>
    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="{{urlRaiz()}}">
            {{urlRaiz()}}
        </a>
    </p>
</body>
</html>