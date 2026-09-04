<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Constancia de Honorarios Profesionales</title>
    @php
        /** num2letras siempre agrega "Bolivares 00/100"; el modelo aprobado
         *  cierra con "EXACTOS CON CERO CÉNTIMOS". */
        $enLetras = function ($n) {
            return trim(preg_replace('/\s*Bol[ií]vares.*$/iu', '', num2letras(number_format($n, 2, '.', ''), false, false)));
        };

        $logo = (!empty($empresa->logo) && file_exists(public_path('storage/imagenes/logos/' . $empresa->logo)))
                ? public_path('storage/imagenes/logos/' . $empresa->logo) : null;

        /* El tamaño se calcula aquí y no en CSS porque el logo configurado puede
           ser ancho (membrete, ~3.4:1) o cuadrado (solo el símbolo, ~1:1). Fijar
           únicamente el ancho hacía que el cuadrado saliera de 400x378 px y
           empujara el texto a una segunda página. Se limitan ambos lados y se
           conserva la proporción. */
        $logoW = $logoH = null;
        if ($logo && ($dim = @getimagesize($logo))) {
            $escala = min(400 / $dim[0], 95 / $dim[1], 1);
            $logoW  = (int) round($dim[0] * $escala);
            $logoH  = (int) round($dim[1] * $escala);
        }

        $tratamiento = ((int) $medico->emp_sexo === 2) ? 'DRA.' : 'DR.';
        $nombreMedico = trim($medico->emp_ape) . ' ' . trim($medico->emp_nom);
        $cedulaFmt = trim($medico->emp_nac) . '- ' . number_format((int) $medico->emp_ced, 0, '', '.');

        $especialidadTexto = count($especialidades)
            ? strtoupper(implode(' y ', $especialidades))
            : null;

        // "a los veinte y dos días del mes de Julio de dos mil veintiséis"
        $mesesEs = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio',
                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $hoy = time();
        $diaLetras  = mb_strtolower($enLetras((int) date('j', $hoy)), 'UTF-8');
        $anioLetras = mb_strtolower($enLetras((int) date('Y', $hoy)), 'UTF-8');
        $mesHoy     = $mesesEs[(int) date('n', $hoy)];
    @endphp
    <style>
        @page { margin: 40px 70px 50px 70px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.55;
            color: #000;
        }
        /* Membrete: logo ancho arriba y el domicilio fiscal centrado debajo,
           como el modelo aprobado por la clínica. */
        .cab { text-align: center; margin-bottom: 14px; }
        .cab-datos {
            font-size: 8px;
            line-height: 1.4;
            margin-top: 4px;
        }
        .cab-datos div { margin: 0; }
        .cab-datos .etiqueta { font-weight: bold; text-decoration: underline; }
        .cab-datos .rif { font-weight: bold; }
        .titulo {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 6px 0 26px 0;
        }
        .cuerpo { text-align: justify; }
        .cuerpo p { margin: 0 0 18px 0; }
        .b { font-weight: bold; }
        .i { font-style: italic; }
        .firma { margin-top: 92px; text-align: center; line-height: 1.5; }
        .firma .nombre { margin-bottom: 2px; }
        .firma .cargo { font-weight: bold; }
    </style>
</head>
<body>

<div class="cab">
    @if($logo)
        <img src="{{ $logo }}" width="{{ $logoW }}" height="{{ $logoH }}" alt="Logo">
    @endif

    <div class="cab-datos">
        @php
            $ubicacion = trim(($empresa->ciudad ?? '') . ' - ESTADO ' . ($empresa->estado ?? ''), ' -');
        @endphp
        @if(!empty($empresa->direccion))
            <div>
                <span class="etiqueta">DOMICILIO FISCAL:</span>
                {{ mb_strtoupper(trim($empresa->direccion), 'UTF-8') }}@if($ubicacion),@endif
            </div>
        @endif
        @if($ubicacion)
            <div>{{ mb_strtoupper($ubicacion, 'UTF-8') }}.</div>
        @endif
        @if(!empty($empresa->telefono))
            <div>Telfs. {{ trim($empresa->telefono) }}@if(!empty($empresa->website)) - {{ trim($empresa->website) }}@endif</div>
        @endif
        @if(!empty($empresa->rut))
            <div class="rif">RIF.: {{ trim($empresa->rut) }}</div>
        @endif
    </div>
</div>

<div class="titulo">CONSTANCIA</div>

<div class="cuerpo">
    <p>
        Quien suscribe, Administrador del
        <span class="b">{{ strtoupper(trim($nm_empresa->emp_nombrelegal ?: $nm_empresa->emp_nombre)) }}</span>,
        por medio de la presente hace constar que el
        <span class="b">{{ $tratamiento }} {{ strtoupper($nombreMedico) }}</span>,
        titular de la cédula de identidad No.
        <span class="b">{{ $cedulaFmt }}</span>,
        es socio accionista de la sociedad mercantil
        <span class="i">{{ trim($nm_empresa->emp_sociedad ?? '') }}</span>
        <span class="b i">RIF {{ trim($nm_empresa->emp_sociedadrif ?? '') }}</span>
        desde el <span class="b">{{ fechaEnLetras($medico->emp_fecing) }}</span>,
        y este a su vez presta en su nombre y por cuenta propia Servicios Profesionales como
        Médico <span class="b">{{ $especialidadTexto ?? '________________' }}</span>,
        para lo cual, por aplicación de lo dispuesto en el artículo 32 de la Providencia
        Administrativa Nro. SNAT/2011/00071 sobre las Normas Generales de Emisión de Facturas
        y otros Documentos publicada en la Gaceta Oficial Nro. 39.795 de fecha 8 de noviembre
        de 2011, son emitidas en su nombre las facturas correspondientes a sus honorarios
        profesionales; en ese sentido, al verificar el reporte de facturación del período
        <span class="b">{{ $periodo_texto }}</span>
        se deja constancia que el promedio mensual facturado es de
        <span class="b">{{ mb_strtoupper($enLetras($promedio), 'UTF-8') }} EXACTOS CON CERO CÉNTIMOS.
        (Bs. {{ number_format($promedio, 2, ',', '.') }})</span>
        generados por la atención de pacientes en consulta externa, hospitalización y/o
        emergencia de esta Institución.
    </p>

    <p>
        Constancia que se expide a petición de la parte interesada a los
        {{ $diaLetras }} días del mes de {{ $mesHoy }} de {{ $anioLetras }}.
    </p>
</div>

<div class="firma">
    <div class="nombre">
        {{ trim(trim($nm_empresa->emp_titulofirma ?? '') . ' ' . trim($nm_empresa->emp_nombrefirma ?? '')) }}
    </div>
    <div class="cargo">{{ strtoupper(trim($nm_empresa->emp_cargofirma ?? '')) }}</div>
    <div class="cargo">{{ strtoupper(trim($nm_empresa->emp_grupofirma ?? '')) }}</div>
</div>

</body>
</html>
