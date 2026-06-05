<link rel="stylesheet" href="{{ asset("assets/css/factura.css") }}">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    #page_pdf { padding: 20px 30px; }
    .header-constancia { text-align: center; margin-bottom: 28px; }
    .header-constancia img { max-height: 80px; }
    .header-constancia h2 { font-size: 17px; font-weight: bold; letter-spacing: 1px; margin: 10px 0 4px; text-transform: uppercase; color: #1a3a5c; }
    .header-constancia p  { margin: 0; font-size: 11px; color: #555; }
    .cuerpo { text-align: justify; line-height: 1.9; margin: 24px 0; font-size: 12.5px; }
    .datos-empleado { width: 100%; border-collapse: collapse; margin: 22px 0; }
    .datos-empleado td { padding: 6px 10px; font-size: 11.5px; border-bottom: 1px solid #eee; }
    .datos-empleado td:first-child { font-weight: bold; width: 35%; color: #1a3a5c; }
    .firma { margin-top: 60px; text-align: center; }
    .firma .linea { border-top: 1px solid #333; width: 260px; margin: 0 auto 6px; }
    .firma p { font-size: 11px; color: #555; margin: 2px 0; }
    .sello { text-align: right; font-size: 10px; color: #aaa; margin-top: 40px; }
    .destacado { font-weight: bold; color: #1a3a5c; }
</style>

<div id="page_pdf">

    {{-- Encabezado --}}
    <div class="header-constancia">
        <img src="{{ asset("assets/$theme/dist/img/logo_large.png") }}" alt="Logo">
        @if($empresa)
            <h2>{{ strtoupper($empresa->nombre) }}</h2>
            <p>RIF: {{ $empresa->rut }}</p>
        @endif
        <h2 style="margin-top:16px; font-size:15px; color:#0d7e6e">CONSTANCIA DE TRABAJO</h2>
    </div>

    {{-- Cuerpo --}}
    <div class="cuerpo">
        <p>
            Por medio de la presente, la empresa <span class="destacado">{{ strtoupper($empresa->nombre ?? 'la empresa') }}</span>,
            hace constar que el ciudadano(a)
            <span class="destacado">{{ strtoupper(trim($nm_empleado->emp_nom . ' ' . $nm_empleado->emp_ape)) }}</span>,
            titular de la Cédula de Identidad N° <span class="destacado">V-{{ number_format($nm_empleado->emp_ced, 0, ',', '.') }}</span>,
            presta sus servicios en esta institución
            @if($nm_empleado->emp_fecing)
                desde el día <span class="destacado">{{ date('d/m/Y', strtotime($nm_empleado->emp_fecing)) }}</span>,
            @endif
            desempeñando el cargo de <span class="destacado">{{ strtoupper($nm_empleado->car_desc ?? 'PROFESIONAL') }}</span>
            @if(isset($nm_empleado->ubi_desc) && $nm_empleado->ubi_desc)
                en el departamento / área de <span class="destacado">{{ strtoupper($nm_empleado->ubi_desc) }}</span>
            @endif
            con una relación laboral de tipo <span class="destacado">{{ strtoupper($nm_empleado->tmo_desc ?? 'NÓMINA') }}</span>.
        </p>

        <p>
            La presente constancia se expide a solicitud del interesado(a), a los
            <span class="destacado">{{ date('d') }}</span> días del mes de
            <span class="destacado">{{ strftime('%B', mktime(0,0,0, date('m'), 1, date('Y'))) ?? date('F') }}</span>
            del año <span class="destacado">{{ date('Y') }}</span>,
            en la ciudad de Caracas, República Bolivariana de Venezuela.
        </p>
    </div>

    {{-- Tabla de datos del empleado --}}
    <table class="datos-empleado">
        <tr>
            <td>Nombre completo:</td>
            <td>{{ strtoupper(trim($nm_empleado->emp_nom . ' ' . $nm_empleado->emp_ape)) }}</td>
        </tr>
        <tr>
            <td>Cédula de identidad:</td>
            <td>V-{{ number_format($nm_empleado->emp_ced, 0, ',', '.') }}</td>
        </tr>
        @if($nm_empleado->car_desc)
        <tr>
            <td>Cargo:</td>
            <td>{{ strtoupper($nm_empleado->car_desc) }}</td>
        </tr>
        @endif
        @if(isset($nm_empleado->ubi_desc) && $nm_empleado->ubi_desc)
        <tr>
            <td>Departamento / Área:</td>
            <td>{{ strtoupper($nm_empleado->ubi_desc) }}</td>
        </tr>
        @endif
        @if($nm_empleado->emp_fecing)
        <tr>
            <td>Fecha de ingreso:</td>
            <td>{{ date('d/m/Y', strtotime($nm_empleado->emp_fecing)) }}</td>
        </tr>
        @endif
        <tr>
            <td>Estatus:</td>
            <td>{{ $nm_empleado->emp_staact == 1 ? 'ACTIVO' : 'INACTIVO' }}</td>
        </tr>
        <tr>
            <td>Fecha de emisión:</td>
            <td>{{ $fecha_emision }}</td>
        </tr>
    </table>

    {{-- Firma --}}
    <div class="firma">
        <div class="linea"></div>
        <p><strong>Recursos Humanos</strong></p>
        @if($empresa)
            <p>{{ strtoupper($empresa->nombre) }}</p>
        @endif
    </div>

    {{-- Sello --}}
    <div class="sello">
        Generado digitalmente por el Portal Laboral ccscmed &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</div>
