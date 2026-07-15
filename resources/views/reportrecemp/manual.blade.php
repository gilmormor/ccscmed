<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manual de Usuario - Sistema Consulta de Recibos de Pago Online</title>
    <style>
        @page {
            margin-left: 70px;
            margin-right: 70px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.7;
            color: #222;
        }

        /* ── ENCABEZADO ── */
        .header-table {
            width: 100%;
            border-bottom: 3px solid rgb(100, 190, 180);
            margin-bottom: 18px;
            padding-bottom: 10px;
        }
        .header-logo { width: 50%; vertical-align: middle; }
        .header-logo img { width: 200px; }
        .header-title {
            width: 75%;
            vertical-align: middle;
            text-align: right;
        }
        .header-title h1 {
            font-size: 16px;
            color: rgb(0, 120, 110);
            margin: 0 0 3px 0;
        }
        .header-title p {
            font-size: 10px;
            color: #555;
            margin: 0;
        }

        /* ── TÍTULOS DE SECCIÓN ── */
        .section-title {
            background: rgb(100, 190, 180);
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 10px;
            margin-top: 20px;
            margin-bottom: 8px;
        }
        .step-row {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }
        .step-num {
            width: 22px;
            min-width: 22px;
            vertical-align: top;
            padding-top: 1px;
            padding-right: 6px;
            text-align: center;
        }
        .step-num span {
            display: block;
            background: rgb(0, 150, 136);
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            width: 18px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            border-radius: 9px;
        }
        .step-text {
            vertical-align: top;
        }

        /* ── CAJAS ── */
        .box-info {
            border-left: 4px solid rgb(100, 190, 180);
            background: #f0faf9;
            padding: 7px 10px;
            margin: 8px 0;
            font-size: 10.5px;
        }
        .box-warn {
            border-left: 4px solid #e07b39;
            background: #fff6f0;
            padding: 7px 10px;
            margin: 8px 0;
            font-size: 10.5px;
        }
        .box-error {
            border-left: 4px solid #c0392b;
            background: #fff0f0;
            padding: 7px 10px;
            margin: 8px 0;
            font-size: 10.5px;
        }

        /* ── ETIQUETAS ── */
        .badge {
            background: rgb(0, 150, 136);
            color: #fff;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 3px;
        }
        .badge-gray {
            background: #888;
            color: #fff;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 3px;
        }
        .badge-orange {
            background: #e07b39;
            color: #fff;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 3px;
        }
        .path {
            font-family: Courier New, monospace;
            background: #eee;
            padding: 1px 5px;
            font-size: 10px;
            border-radius: 2px;
        }

        /* ── TABLA DE CAMPOS ── */
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
            font-size: 10.5px;
        }
        .field-table th {
            background: rgb(100, 190, 180);
            color: #fff;
            padding: 4px 8px;
            text-align: left;
        }
        .field-table td {
            border-bottom: 1px solid #ddd;
            padding: 5px 8px;
            vertical-align: top;
        }
        .field-table tr:nth-child(even) td { background: #f5fafa; }

        /* ── PIE ── */
        .footer {
            border-top: 2px solid rgb(100, 190, 180);
            margin-top: 30px;
            padding-top: 6px;
            text-align: center;
            font-size: 9px;
            color: #777;
        }

        p { margin: 4px 0; }
        ul { margin: 4px 0 6px 20px; }
        li { margin-bottom: 3px; }
        a { color: rgb(0, 120, 110); }
    </style>
</head>
@if(isset($empresa->color) and $empresa->color!="")
	<style>
		.section-title{
			background: {{ $empresa->color }} !important;
		}
		.field-table th{
			background: {{ $empresa->color }} !important;
		}
        .header-title h1{
			color: {{ $empresa->color }} !important;
		}
        .step-num span {
            background: {{ $empresa->color }} !important;
        }
        .badge {
            background: {{ $empresa->color }} !important;
        }
        a { color: {{ $empresa->color }} !important; }
    </style>
@endif

<body>

<!-- ══════════════════════════════════════════════
     ENCABEZADO
═══════════════════════════════════════════════ -->
<table class="header-table">
    <tr>
        <td class="header-logo">
            @if(!empty($empresa->logo) && file_exists(public_path('storage/imagenes/logos/' . $empresa->logo)))
                <img src="{{ public_path('storage/imagenes/logos/' . $empresa->logo) }}" width="100" alt="Logo Empresa">
            @endif
        </td>
        <td class="header-title">
            <h1>Manual de Usuario</h1>
            <p>Sistema de Consulta de Recibos de Pago Online</p>
            <p>Rol: Trabajador &nbsp;|&nbsp; Versión 1.0 &nbsp;|&nbsp; {{ date('d/m/Y') }}</p>
        </td>
    </tr>
</table>


<!-- ══════════════════════════════════════════════
     1. INTRODUCCIÓN
═══════════════════════════════════════════════ -->
<div class="section-title">1. Introducción</div>
<p>
    El <strong>Sistema de Consulta de Recibos de Pago Online</strong> del {{ trim($empresa->nombre) }} es una plataforma digital diseñada para facilitar a los trabajadores el acceso a su información de nómina de manera rápida, segura y desde cualquier lugar. A través de este sistema, cada empleado pu
    le permite consultar y descargar en formato PDF sus recibos de pago correspondientes a cada período
    de nómina, así como generar su <strong>Constancia de Trabajo</strong> cuando la necesite,
    todo desde cualquier dispositivo con acceso a Internet.
</p>
<div class="box-info">
    <strong>Perfil de acceso:</strong> Su cuenta tiene asignado el rol <span class="badge">Trabajador</span>,
    que le permite consultar únicamente su propia información de nómina.
</div>


<!-- ══════════════════════════════════════════════
     2. INGRESO AL SISTEMA (LOGIN)
═══════════════════════════════════════════════ -->
<div class="section-title">2. Ingreso al Sistema</div>

<table class="step-row"><tr><td class="step-num"><span>1</span></td><td class="step-text">Abra su navegador e ingrese a la dirección del sistema haciendo clic en el siguiente enlace o copiándolo en su navegador:<br>&nbsp;&nbsp;&nbsp;<a href="{{ url('/') }}">{{ url('/') }}</a></td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>2</span></td><td class="step-text">El sistema lo llevará automáticamente a la pantalla de inicio de sesión.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>3</span></td><td class="step-text">Complete los campos de acceso:</td></tr></table>

<table class="field-table">
    <tr>
        <th style="width:30%;">Campo</th>
        <th>Descripción</th>
    </tr>
    <tr>
        <td><strong>Usuario</strong></td>
        <td>
            Ingrese su número de cédula de identidad <strong>solo con dígitos numéricos</strong>,
            sin la letra V, sin puntos ni espacios.<br>
            <em>Ejemplo correcto:</em> <span class="path">12345678</span>&nbsp;
            <em>Ejemplo incorrecto:</em> <span class="path">V-12.345.678</span>
        </td>
    </tr>
    <tr>
        <td><strong>Contraseña</strong></td>
        <td>Ingrese la contraseña asignada. La contraseña distingue mayúsculas de minúsculas.</td>
    </tr>
</table>

<table class="step-row"><tr><td class="step-num"><span>4</span></td><td class="step-text">Haga clic en el botón <span class="badge">Ingresar</span>.</td></tr></table>

<div class="box-error">
    <strong>Credenciales incorrectas:</strong> Si el usuario o la contraseña no son válidos, el sistema
    mostrará el mensaje en rojo:<br><br>
    &nbsp;&nbsp;&nbsp;<em>"Las credenciales introducidas son incorrectas."</em><br><br>
    Verifique que su número de cédula esté escrito correctamente (solo números) y que no tenga
    activado el bloqueo de mayúsculas al escribir la contraseña. Si el problema persiste,
    utilice la opción de recuperación de contraseña descrita en la sección 3.
</div>


<!-- ══════════════════════════════════════════════
     3. RECUPERACIÓN DE CONTRASEÑA
═══════════════════════════════════════════════ -->
<div class="section-title">3. Recuperación de Contraseña</div>

<p>Si olvidó su contraseña, puede recuperarla desde la pantalla de inicio de sesión:</p>

<table class="step-row"><tr><td class="step-num"><span>1</span></td><td class="step-text">En la pantalla de inicio de sesión haga clic en el enlace <a href="{{ url('seguridad/reset') }}"><strong>"Recuperar la contraseña!"</strong></a>.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>2</span></td><td class="step-text">Ingrese el <strong>correo electrónico</strong> registrado en el sistema.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>3</span></td><td class="step-text">Haga clic en <span class="badge">Enviar</span>.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>4</span></td><td class="step-text">El sistema le enviará un correo electrónico con su nueva contraseña para que pueda ingresar al sistema.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>5</span></td><td class="step-text">Revise su bandeja de entrada (y la carpeta de spam si no lo encuentra).</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>6</span></td><td class="step-text">Use esa contraseña para iniciar sesión y luego cámbiela por una de su preferencia (ver sección 5).</td></tr></table>

<div class="box-error">
    <strong>Correo no encontrado:</strong> Si al intentar recuperar su contraseña el sistema muestra el mensaje:<br><br>
    &nbsp;&nbsp;&nbsp;<em>"Correo electrónico [su correo] no existe."</em><br><br>
    Significa que el correo ingresado no está registrado en el sistema. En ese caso diríjase al
    <strong>Departamento de Talento Humano</strong> para que actualicen su dirección de correo
    electrónico en el sistema.
</div>


<!-- ══════════════════════════════════════════════
     4. CONSULTA DE RECIBOS DE PAGO
═══════════════════════════════════════════════ -->
<div class="section-title">4. Consulta de Recibos de Pago</div>

<p>
    Al ingresar al sistema, en el menú principal encontrará la opción
    <strong>Reportes</strong>. Siga estos pasos en orden:
</p>

<table class="step-row"><tr><td class="step-num"><span>1</span></td><td class="step-text">Haga clic en el menú <strong>Reporte</strong>.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>2</span></td><td class="step-text">Seleccione la opción <strong>Recibo Emp</strong>.<br><span style="font-size:10px;">Ruta de acceso: <strong>Reporte &rarr; Recibo Emp</strong></span></td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>3</span></td><td class="step-text">Seleccione el <strong>Cono Monetario</strong> correspondiente al período que desea consultar (por defecto aparece <strong>BsD</strong>).</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>4</span></td><td class="step-text">
    Verifique el campo <strong>Empresa</strong>.<br>
    <span style="font-size:10px;color:#555;">Si su nómina pertenece a <strong>una sola empresa</strong>, el sistema la seleccionará automáticamente.
    Si pertenece a más de una empresa, seleccione la que corresponda al recibo que desea consultar.</span>
</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>5</span></td><td class="step-text">
    Haga clic en el botón azul <span class="badge">&#128269; Filtrar períodos</span>.<br>
    <span style="font-size:10px;color:#555;">Este paso es <strong>obligatorio</strong>: carga la lista de períodos disponibles según la empresa seleccionada.
    Al completar, el sistema seleccionará automáticamente el período más reciente.</span>
</td></tr></table>

<div class="box-warn">
    <strong>Paso obligatorio — Filtrar períodos:</strong><br>
    Después de confirmar la Empresa, <strong>siempre</strong> debe hacer clic en el botón
    <strong>"Filtrar períodos"</strong> para cargar los períodos disponibles.
    Si omite este paso, la lista de períodos aparecerá vacía y el sistema no podrá generar el recibo.
    <br><br>
    Opcionalmente puede indicar un rango de fechas (Desde / Hasta) antes de filtrar,
    para reducir la cantidad de períodos que se muestran en la lista.
</div>

<table class="step-row"><tr><td class="step-num"><span>6</span></td><td class="step-text">
    Verifique o cambie el <strong>Período</strong> en la lista desplegable <em>Periodo</em>.<br>
    <span style="font-size:10px;color:#555;">El sistema selecciona automáticamente el primer período de la lista. Si necesita consultar uno distinto, selecciónelo manualmente.</span>
</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>7</span></td><td class="step-text">Haga clic en el botón <span class="badge">Recibo</span> para visualizar y descargar su recibo de pago en formato PDF.</td></tr></table>

<table class="field-table">
    <tr>
        <th style="width:5%;">#</th>
        <th style="width:30%;">Campo</th>
        <th>Descripción</th>
    </tr>
    <tr>
        <td>1</td>
        <td><strong>Cono Monetario</strong></td>
        <td>Tipo de moneda del período (BsD, BsS o BsF). Normalmente se deja en <strong>BsD</strong>.</td>
    </tr>
    <tr>
        <td>2</td>
        <td><strong>Empresa</strong></td>
        <td>Empresa a la que pertenece su nómina. Si solo tiene una empresa registrada, el sistema la selecciona automáticamente.</td>
    </tr>
    <tr>
        <td>3</td>
        <td><strong>Filtrar períodos</strong> <span class="badge-orange">Botón obligatorio</span></td>
        <td>Carga la lista de períodos según la empresa. <strong>Debe hacer clic aquí antes de poder generar el recibo.</strong> Al completar, selecciona automáticamente el período más reciente.</td>
    </tr>
    <tr>
        <td>4</td>
        <td><strong>Período</strong></td>
        <td>Período de nómina a consultar. Se selecciona automáticamente el primero de la lista; cámbielo si necesita uno distinto.</td>
    </tr>
</table>

<div class="box-info">
    <strong>Resumen del flujo correcto:</strong><br>
    Cono Monetario &rarr; Empresa (auto-seleccionada si es única) &rarr; <strong>Filtrar períodos</strong> &rarr; Período (auto-seleccionado) &rarr; <span class="badge">Recibo</span>
</div>


<!-- ══════════════════════════════════════════════
     5. CONSTANCIA DE TRABAJO
═══════════════════════════════════════════════ -->
<div class="section-title">5. Constancia de Trabajo</div>

<p>
    Desde la misma pantalla de consulta de recibos (<strong>Reporte &rarr; Recibo Emp</strong>)
    también puede generar su Constancia de Trabajo:
</p>

<table class="step-row"><tr><td class="step-num"><span>1</span></td><td class="step-text">Complete los filtros (Cono Monetario, Empresa y Período) tal como se describe en la sección 4. Recuerde hacer clic en <strong>Filtrar períodos</strong> antes de seleccionar el período.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>2</span></td><td class="step-text">Haga clic en el botón <span class="badge">Constancia</span>.</td></tr></table>
<table class="step-row"><tr><td class="step-num"><span>3</span></td><td class="step-text">El sistema generará su constancia de trabajo en formato PDF.</td></tr></table>

<div class="box-warn">
    <strong>Condición para generar la Constancia:</strong> La constancia de trabajo
    solo puede generarse si usted se encuentra registrado como <strong>empleado activo</strong>
    de la empresa seleccionada. Si el sistema no genera el documento, verifique con el
    <strong>Departamento de Talento Humano</strong> el estatus de su relación laboral en el sistema.
</div>


<!-- ══════════════════════════════════════════════
     6. CAMBIO DE CONTRASEÑA Y CIERRE DE SESIÓN
═══════════════════════════════════════════════ -->
<div class="section-title">6. Cambio de Contraseña y Cierre de Sesión</div>

<p>
    En la parte superior derecha de la pantalla encontrará el ícono de usuario (imagen de una persona
    con corbata) junto al texto <strong>"Hola [su nombre]"</strong>, por ejemplo:
    <em>Hola María</em>. Al hacer clic sobre ese texto se despliega un menú con las siguientes opciones:
</p>

<table class="field-table">
    <tr>
        <th style="width:35%;">Opción</th>
        <th>Descripción</th>
    </tr>
    <tr>
        <td><strong>Cambiar contraseña</strong></td>
        <td>
            Permite actualizar su contraseña de acceso. Se recomienda usar una contraseña
            segura de al menos 8 caracteres combinando letras y números.
        </td>
    </tr>
    <tr>
        <td><strong>Salir</strong></td>
        <td>
            Cierra su sesión de forma segura. <strong>Siempre utilice esta opción</strong>
            antes de cerrar el navegador, especialmente si usa un equipo compartido.
        </td>
    </tr>
</table>


<!-- ══════════════════════════════════════════════
     7. RECOMENDACIONES GENERALES
═══════════════════════════════════════════════ -->
<div class="section-title">7. Recomendaciones Generales</div>

<ul>
    <li>No comparta su usuario ni contraseña con otras personas.</li>
    <li>Si sospecha que alguien más conoce su contraseña, cámbiela de inmediato desde el menú
        <strong>Hola [su nombre] &rarr; Cambiar contraseña</strong>.</li>
    <li>Siempre cierre su sesión al terminar de usar el sistema.</li>
    <li>Si tiene algún inconveniente con el acceso al sistema, contacte al
        <strong>Departamento de Talento Humano</strong>.</li>
</ul>


<!-- ── PIE DE PÁGINA ── -->
<div class="footer">
    {{ trim($empresa->nombre) }} &nbsp;|&nbsp; Sistema de Consulta de Recibos de Pago Online
    &nbsp;|&nbsp; Manual de Usuario Rol Trabajador &nbsp;|&nbsp; {{ date('Y') }}
</div>

</body>
</html>
