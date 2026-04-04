<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Constancia de Trabajo</title>
    <style>
        @page {
            margin-left: 80px;
            margin-right: 80px;
            margin-top: 60px;
            margin-bottom: 60px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        .center {
            text-align: center;
        }

        .justify {
            text-align: justify;
        }

        .bold {
            font-weight: bold;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mt-40 {
            margin-top: 40px;
        }

        .firma {
            margin-top: 80px;
            text-align: center;
        }

        .linea {
            border-top: 1px solid #000;
            width: 60%;
            margin: auto;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    {{-- <div class="center">
        <div class="bold">{{ trim($nm_empresa->emp_nombre) }}</div>
        <div>{{ trim($nm_empresa->emp_direc) }}</div>
        <div>{{ trim($nm_empresa->emp_telf) }}</div>
        <div>RIF: {{ trim($nm_empresa->emp_rif) }}</div>
    </div> --}}
    <br><br>
	<table width="100%">
		<tr>
			<td width="20%">
				{{-- <img src="{{ Storage::url("imagenes/logos/" . $nm_empresa->logo) }}" width="100"> --}}
				<img src="{{ public_path('storage/imagenes/logos/' . $nm_empresa->logo) }}" width="100">
			</td>
			<td width="80%" class="center" style="line-height:10px; font-family: Times New Roman; font-size:16px; line-height: 1.5;">
				<div class="bold">{{ $nm_empresa->emp_nombre }}</div>
				<div>{{ $nm_empresa->emp_direc }}</div>
				<div>{{ $nm_empresa->ciudad . " - " . $nm_empresa->region . " - " . $nm_empresa->pais }}</div>
				<div>{{ $nm_empresa->emp_telf }}</div>
				<div>RIF: {{ $nm_empresa->emp_rif }}</div>
				{{-- <div>RIF: {{ Storage::url("imagenes/logos/" . $nm_empresa->logo) }}</div> --}}
			</td>
		</tr>
	</table>
	<br><br>
    <div class="center mt-20 bold" style="text-align: center;
	font-size:20px;
	font-family: 'Times New Roman';
	text-decoration: underline;
	font-weight: bold;">
        CONSTANCIA
    </div>
	<br>
    {{-- CUERPO --}}
    <div class="justify mt-20" style="height: auto;
	width: 98%;
	text-align: justify;
	font-family: 'arial';
	font-size:18px;
	line-height: 1.5;">
        Quien suscribe, Jefe del Departamento de Talento Humano de la Sociedad Mercantil
        <span class="bold">{{ strtoupper(trim($nm_empresa->emp_nombre)) }}</span>, hace constar que
        el (la) ciudadano(a)
        <span class="bold">{{ trim($nm_movnomtrab->emp_nom) }} {{ trim($nm_movnomtrab->emp_ape) }}</span>,
        titular de la cédula de identidad No.
        <span class="bold">{{ substr($nm_movnomtrab->emp_rif, 0, 1) . '-' . number_format((int)$nm_movnomtrab->emp_ced, 0, '', '.') }}</span>,
        trabaja en esta empresa desde el {{fechaEnLetras($nm_movnomtrab->mov_fecing)}},
        desempeña actualmente el cargo de
        <span class="bold">{{ trim($nm_movnomtrab->car_desc) }}</span>,
        devenga un salario integral mensual de
        <span class="bold">{{ strtoupper(num2letras($nm_movnomtrab->mov_sueldo,false)) }}</span>
        (<span class="bold">Bs.{{ number_format($nm_movnomtrab->mov_sueldo, 2, ',', '.') }}</span>),
        y recibe el beneficio de alimentación diario.
    </div>

    <div class="justify mt-20" style="height: auto;
	width: 98%;
	text-align: justify;
	font-family: 'arial';
	font-size:18px;
	line-height: 1.5;">
        Constancia que se expide a petición de parte interesada para fines legales
        consiguientes en la ciudad de {{$nm_empresa->ciudad}},
        a los {{ fechaEnTexto(date("Y-m-d")) }}.
    </div>
	<br><br><br>
    {{-- FIRMA --}}
    <div class="firma" style="height: auto;
	width: 98%;
	text-align: center;
	font-family: 'arial';
	font-size:16px;
	line-height: 1.5;">
        <div class="linea"></div>
        <div class="bold mt-20">
            {{ $nm_empresa->emp_nombrefirma }}
        </div>
        <div class="bold">
            JEFE DE DPTO. TALENTO HUMANO
        </div>
    </div>

</body>