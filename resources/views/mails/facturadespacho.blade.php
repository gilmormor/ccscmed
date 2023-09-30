<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Despacho entregado</title>
</head>
<body>

    <p><b>Fecha:</b> {{ date("d-m-Y h:i:s A", strtotime($msg->updated_at)) }}.</p>
    <p>{{ $cuerpo }}</p>
    

    <p>Datos usuario que generó el correo:</p>
    <ul>
        <li><b>Nombre:</b> {{session()->get('nombre_usuario') }}</li>
        <li><b>Email:</b> {{Auth::user()->email}}</li>
    </ul>
    <p><b>Datos:</b></p>
    <ul>
        <li><b>Nro. Nota Venta:</b> {{ $tabla->notaventa_id }}</li>
        <li><b>nro. Orden Despacho:</b> {{ $tabla->id }}</li>
        <li><b>Fecha:</b> {{date("d-m-Y h:i:s A", strtotime($tabla->guiadespachofec))}}</li>
        <li><b>Nro Guia: </b> {{ $tabla->guiadespacho }}</li>
        <li><b>Nro Factura: </b> {{ $tabla->numfactura }}</li>
        <li><b>Fecha Factura: </b> {{ date("d-m-Y", strtotime($tabla->fechafactura)) }}</li>
        <li><b>RUT:</b> {{ $tabla->notaventa->cliente->rut }}</li>
        <li><b>Razon Social:</b> {{ $tabla->notaventa->cliente->razonsocial }}</li>
        <li><b>Vendedor:</b> {{ $tabla->notaventa->vendedor->persona->nombre . " " . $tabla->notaventa->vendedor->persona->apellido}}</li>
    </ul>
    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="https://www.pl.plastiservi.cl">
            https://www.pl.plastiservi.cl
        </a>
    </p>
</body>
</html>