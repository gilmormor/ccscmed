<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Médicos que más generaron ingresos</title>
    @php
        $color = (isset($empresa->color) && $empresa->color != '') ? $empresa->color : 'rgb(13,126,110)';
        $logo  = (!empty($empresa->logo) && file_exists(public_path('storage/imagenes/logos/' . $empresa->logo)))
                 ? public_path('storage/imagenes/logos/' . $empresa->logo) : null;
        $fmt   = fn($v) => number_format((float) $v, 2, ',', '.');
    @endphp
    <style>
        @page { margin: 34px 30px 46px 30px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px; color: #222; margin: 0;
        }

        /* ── Encabezado ── */
        .cab { width: 100%; border-bottom: 2.5px solid {{ $color }}; padding-bottom: 7px; margin-bottom: 12px; }
        .cab td { vertical-align: middle; }
        .cab-logo { width: 22%; }
        .cab-logo img { width: 108px; }
        .cab-tit { text-align: right; }
        .cab-tit h1 { margin: 0 0 2px 0; font-size: 14px; color: {{ $color }}; }
        .cab-tit p  { margin: 0; font-size: 8.5px; color: #555; }

        /* ── Resumen ── */
        .resumen { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .resumen td {
            width: 20%; padding: 7px 9px;
            border: 1px solid #e2e7ee; background: #f7f9fb;
        }
        .resumen .lbl {
            font-size: 7px; text-transform: uppercase; letter-spacing: .05em;
            color: #7b8798; padding-bottom: 2px;
        }
        .resumen .val { font-size: 12px; font-weight: bold; color: #1a3a5c; }

        /* ── Tabla ── */
        table.datos { width: 100%; border-collapse: collapse; }
        table.datos thead th {
            background: {{ $color }}; color: #fff;
            padding: 5px 6px; font-size: 8px; text-align: left;
            text-transform: uppercase; letter-spacing: .03em;
        }
        table.datos tbody td {
            padding: 4px 6px; border-bottom: 1px solid #eceff3; font-size: 8.5px;
        }
        table.datos tbody tr:nth-child(even) td { background: #f8fafc; }
        .r { text-align: right; }
        .c { text-align: center; }
        .pos {
            display: inline-block; min-width: 15px; padding: 1px 4px;
            background: #eef2f6; color: #55627a; border-radius: 7px;
            font-size: 7.5px; font-weight: bold; text-align: center;
        }
        .pos-top { background: {{ $color }}; color: #fff; }

        tfoot td {
            padding: 6px; font-weight: bold; font-size: 8.5px;
            border-top: 2px solid {{ $color }}; background: #f2f6f9;
        }

        /* ── Pie ── */
        .pie {
            position: fixed; bottom: -28px; left: 0; right: 0;
            text-align: center; font-size: 7px; color: #8b96a9;
            border-top: 1px solid #e2e7ee; padding-top: 4px;
        }
        .pie .pag:after { content: counter(page); }
    </style>
</head>
<body>

<div class="pie">
    {{ isset($empresa->nombre) ? trim($empresa->nombre) : '' }}
    &nbsp;|&nbsp; Médicos que más generaron ingresos
    &nbsp;|&nbsp; Generado el {{ date('d/m/Y H:i') }}
    &nbsp;|&nbsp; Página <span class="pag"></span>
</div>

<table class="cab">
    <tr>
        <td class="cab-logo">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo">
            @endif
        </td>
        <td class="cab-tit">
            <h1>Médicos que más generaron ingresos</h1>
            <p><strong>Período:</strong> {{ $filtros['periodo'] }}</p>
            @foreach($filtros['extras'] as $extra)
                <p>{{ $extra }}</p>
            @endforeach
            <p>Tasa promedio BCV: Bs {{ $fmt($tasa_bcv) }}</p>
        </td>
    </tr>
</table>

<table class="resumen">
    <tr>
        <td>
            <div class="lbl">Médicos</div>
            <div class="val">{{ number_format($totales['medicos'], 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="lbl">Honorarios Profesionales</div>
            <div class="val">Bs {{ $fmt($totales['asig_bs']) }}</div>
        </td>
        <td>
            <div class="lbl">Deducciones</div>
            <div class="val">Bs {{ $fmt($totales['ded_bs']) }}</div>
        </td>
        <td>
            <div class="lbl">Neto a pagar</div>
            <div class="val">Bs {{ $fmt($totales['neto_bs']) }}</div>
        </td>
        <td>
            <div class="lbl">Total ME</div>
            <div class="val">$ {{ $fmt($totales['asig_usd']) }}</div>
        </td>
    </tr>
</table>

@if(count($filas) === 0)
    <p style="text-align:center;color:#8b96a9;padding:28px 0;">
        No hay honorarios registrados para los filtros seleccionados.
    </p>
@else
<table class="datos">
    <thead>
        <tr>
            <th class="c" style="width:4%;">#</th>
            <th style="width:9%;">Cédula</th>
            <th style="width:27%;">Médico</th>
            <th class="c" style="width:6%;">Nóm.</th>
            <th class="r" style="width:12%;">Honorarios Bs</th>
            <th class="r" style="width:11%;">Otra Mon. Bs</th>
            <th class="r" style="width:11%;">Deducciones Bs</th>
            <th class="r" style="width:12%;">Neto Bs</th>
            <th class="r" style="width:8%;">ME $</th>
            <th class="r" style="width:6%;">%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($filas as $f)
        <tr>
            <td class="c"><span class="pos {{ $f->posicion <= 3 ? 'pos-top' : '' }}">{{ $f->posicion }}</span></td>
            <td>{{ number_format($f->emp_ced, 0, ',', '.') }}</td>
            <td>{{ $f->medico }}</td>
            <td class="c">{{ $f->nominas }}</td>
            <td class="r">{{ $fmt($f->asig_bs) }}</td>
            <td class="r">{{ $fmt($f->otramon_bs) }}</td>
            <td class="r">{{ $fmt($f->ded_bs) }}</td>
            <td class="r"><strong>{{ $fmt($f->neto_bs) }}</strong></td>
            <td class="r">{{ $fmt($f->asig_usd) }}</td>
            <td class="r">{{ $fmt($f->participacion) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="c">TOTAL · {{ number_format($totales['medicos'], 0, ',', '.') }} médicos</td>
            <td class="r">{{ $fmt($totales['asig_bs']) }}</td>
            <td class="r">{{ $fmt($totales['otramon_bs']) }}</td>
            <td class="r">{{ $fmt($totales['ded_bs']) }}</td>
            <td class="r">{{ $fmt($totales['neto_bs']) }}</td>
            <td class="r">{{ $fmt($totales['asig_usd']) }}</td>
            <td class="r">100,00</td>
        </tr>
    </tfoot>
</table>
@endif

</body>
</html>
