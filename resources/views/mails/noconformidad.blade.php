<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>No Conformidad</title>
</head>
<body>

    <p>Hola! Has recibido una nueva no conformidad a las {{ $msg->created_at }}.</p>
    <p>Estos son los datos del usuario que ha realizado la no conformidad:</p>
    <ul>
        <li>Nombre: {{session()->get('nombre_usuario') }}</li>
        <li>Email: {{Auth::user()->email}}</li>
    </ul>
    <p>Datos No Conformidad:</p>
    <ul>
        <li>Id: {{ $msg->id }}</li>
        <li>Fecha: {{ $msg->fechahora }}</li>
        <li>
            <a href="https://www.pl.plastiservi.cl">
                Ingresa a la pagina
            </a>
        </li>
    </ul>
</body>
</html>