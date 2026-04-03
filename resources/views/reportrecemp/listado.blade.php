<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">
<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					{{-- <img src="{{asset("assets/$theme/dist/img/logo_large.png")}}" style="max-width:1200%;width:auto;height:auto;"> --}}
					<p>{{$nm_empresa->emp_nombre}}</p>
					<p>RIF: {{$nm_empresa->emp_rif}}</p>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">RECIBO PAGO</span>
					<p><strong>Fecha:</strong> {{date("d/m/Y h:i:s A")}}</p>
					<p><strong>Nro. Recibo:</strong> {{$nm_movnomtrab->mov_numrec}}</p>
					<p><strong>Periodo:</strong> {{date('d/m/Y', strtotime($nm_control->cot_fdesde))}} al {{date('d/m/Y', strtotime($nm_control->cot_fhasta))}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Nomina: {{$nm_tiponomina->tmo_desc}}</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:7%"><strong>Cédula:</strong> </td><td style="width:50%">{{$nm_empleado->emp_rif}}</td>
						</tr>
						<tr class="headt">
							<td style="width:7%"><strong>Nombre:</strong> </td><td style="width:50%">{{$nm_empleado->emp_nom}} {{$nm_empleado->emp_ape}}</td>
						</tr>
						<tr class="headt">
							<td style="width:7%"><strong>Depto:</strong> </td><td style="width:50%">{{$nm_ubicacion->ubi_desc}}</td>
						</tr>
						<tr class="headt">
							<td style="width:7%"><strong>Cargo:</strong> </td><td style="width:50%">{{$nm_cargos->car_desc}}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>

	<div class="round" style="padding-bottom: 0px;">
		<table id="factura_detalle" style="table-layout:fixed;width: 100%;">
			<thead>
				<tr>
					<th style='text-align:left;width: 30% !important;'>Concepto</th>
					<th style='text-align:left;width: 7.7% !important;'>Cantidad</th>
					<th style='text-align:right;width: 7.7% !important;'>Saldo</th>
					<th style='text-align:right;width: 7.7% !important;'>Asignaciones</th>
					<th style='text-align:right;width: 7.7% !important;'>Deduciones</th>
				</tr>
			</thead>
			<?php
				$totalAsig = 0;
				$totalDedu = 0;
			?>
			<tbody id="detalle_productos">
				@foreach($nm_movhists as $nm_movhist)
					<?php 
						$signo = $nm_movhist->mov_tipocon == "A" ? 1 : -1;
						$totalAsig += (strpos("AOF", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0;
						$totalDedu += (strpos("D", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0;
						//$totalacum += $signo * $nm_movhist->mov_monto;
					?>
					<tr>
						<td style='text-align:left;width: 30.7% !important;'>&nbsp;&nbsp;{{$nm_movhist->con_desc . " " . trim($nm_movhist->mov_ref)}}</td>
						<td style='text-align:left;width: 7.7% !important;'>{{$nm_movhist->mov_unid}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format($nm_movhist->mov_saldo, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("A", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("D", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
				<td style='text-align:left;width: 30.7% !important;'></td>
				<td style='text-align:left;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>Total</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalAsig, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalDedu, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
			</tr>
			<tr>
				<td colspan='3' style='text-align:right;width: 30.7% !important;'><strong>Neto Recibido</strong></td>
				<td colspan='2' style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalAsig - $totalDedu, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width: 100%; text-align: left; margin-top: 40px;">
		<div style="display: inline-block; width: 300px;">
			<p><strong>Recibido Conforme:</strong></p>
			<div style="border-top: 1px solid #000; margin-top: 60px;"></div>
			<p style="margin-top: 5px;">
				{{$nm_empleado->emp_nom}} {{$nm_empleado->emp_ape}}
			</p>
		</div>
	</div>
</div>
