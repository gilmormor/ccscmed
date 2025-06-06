<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">
<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="{{asset("assets/$theme/dist/img/logo_large.png")}}" style="max-width:1200%;width:auto;height:auto;">
					<p>{{$empresa[0]['nombre']}}</p>					
					<p>RIF: {{$empresa[0]['rut']}}</p>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h4">RELACION PACIENTES PAGADOS</span>
					<p><strong>Fecha:</strong> {{date("d/m/Y h:i:s A")}}</p>
					<p><strong>Relación #:</strong> {{implode(',', $nm_control->nmcontrolnomclss->pluck('ccl_nronomciclos')->toArray())}}</p>
					<p><strong>Periodo:</strong> {{date('d/m/Y', strtotime($nm_control->cot_fdesde))}} al {{date('d/m/Y', strtotime($nm_control->cot_fhasta))}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h4">MEDICO</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:10%"><strong>Cédula:</strong> </td><td style="width:50%">{{number_format($nm_empleado->emp_ced, 0, ",", ".")}}</td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><strong>Nombre:</strong> </td><td style="width:50%">{{$nm_empleado->emp_nom}} {{$nm_empleado->emp_ape}}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>

	<div class="round" style="padding-bottom: 0px;">
		<table id="factura_detalleccsc" style="table-layout:fixed;width: 100%;">
			<thead>
				{{-- <tr>
					<th style='text-align:left;width: 10% !important;'>Fecha/<br>Factura</th>
					<th style='text-align:left;width: 30% !important;'>Concepto/<br>Paciente</th>
					<th style='text-align:right;width: 7.7% !important;'>Honorario (Bs.)</th>
					<th style='text-align:right;width: 7.7% !important;'>Pagado/<br>Dscto</th>
					<th style='text-align:right;width: 7.7% !important;'>Pago Actual (Bs.)</th>
					<th style='text-align:right;width: 7.7% !important;'>Moneda Nac</th>
					<th style='text-align:right;width: 7.7% !important;'>Otra Moneda</th>
					<th style='text-align:right;width: 7.7% !important;'>Tasa Cambio</th>
					<th style='text-align:right;width: 7.7% !important;'>Monto en moneda</th>
				</tr> --}}
				<tr>
					<th style='text-align:left;width: 10% !important;' rowspan="2">Fecha / Factura</th>
					<th style='text-align:left;width: 30% !important;' rowspan="2">Concepto / Paciente</th>
					<th style='text-align:right;width: 7.7% !important;' rowspan="2">Honorario<br>(Bs.)</th>
					<th style='text-align:right;width: 7.7% !important;' rowspan="2">Pagado / Dscto</th>
					<th style='text-align:right;width: 7.7% !important;' rowspan="2">Pago Actual<br>(Bs.)</th>
					<th style='text-align:center;width: 15.4% !important;' colspan="2">División del Pago Actual (Bs.)</th>
					<th style='text-align:right;width: 7.7% !important;' rowspan="2">Tasa Cambio</th>
					<th style='text-align:right;width: 7.7% !important;' rowspan="2">Monto en Moneda</th>
				</tr>
				<tr>
					<th>Moneda Nac.</th>
					<th>Otra moneda</th>
				</tr>
			</thead>
			<?php
				$Tpago_actual = 0;
				$Tmoneda_nac = 0;
				$Totra_moneda_bs = 0;
				$Tmonto_otra_moneda = 0;
				$TTpago_actual = 0;
				$TTmoneda_nac = 0;
				$TTotra_moneda_bs = 0;
				$TTmonto_otra_moneda = 0;

				$aux_tipdoc_desc = $pacdets[0]->tipdoc_desc;
			?>
			<tbody id="detalle_productos">
				@foreach($pacdets as $pacdet)
					@if ($aux_tipdoc_desc != $pacdet->tipdoc_desc)
						<tr class='btn-accion-tabla total-row fondo-blanco'>
							<td style='text-align:left;width: 10% !important;'></span></td>
							<td style='text-align:right;width: 30% !important;'>Total {{$aux_tipdoc_desc}}</span></td>
							<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
							<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
							<td style='text-align:right;width: 7.7% !important;'>{{number_format($Tpago_actual, 2, ",", ".")}}&nbsp;&nbsp;</td>
							<td style='text-align:right;width: 7.7% !important;'>{{number_format($Tmoneda_nac, 2, ",", ".")}}&nbsp;&nbsp;</td>
							<td style='text-align:right;width: 7.7% !important;'>{{number_format($Totra_moneda_bs, 2, ",", ".")}}&nbsp;&nbsp;</td>
							<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
							<td style='text-align:right;width: 7.7% !important;'><span class="blue small">{{number_format($Tmonto_otra_moneda, 2, ",", ".")}}</span>&nbsp;&nbsp;</td>
						</tr>
						<?php 
							$aux_tipdoc_desc = $pacdet->tipdoc_desc;
							$Tpago_actual = 0;
							$Tmoneda_nac = 0;
							$Totra_moneda_bs = 0;
							$Tmonto_otra_moneda = 0;
						?>
					@endif
					<?php
						$aux_pagadoDscto = "0,00";
						if($pacdet->pagado > 0 or $pacdet->dscto > 0){
							$aux_pagadoDscto = number_format($pacdet->pagado, 2, ",", ".") . " / " . number_format($pacdet->dscto, 2, ",", ".");
						}
					?>
					<tr class='btn-accion-tabla fondo-blanco'>
						<td style='text-align:right;width: 10% !important;'>{{date("d/m/Y", strtotime($pacdet->fecha_fact))}}&nbsp;&nbsp;&nbsp;&nbsp;<br><i class="purple small">{{$pacdet->factura}}</i>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td style='text-align:left;width: 30% !important;'>{{$pacdet->concepto}}<br><i class="purple small">{{$pacdet->nom_paciente}}</i></td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format($pacdet->honorario, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{$aux_pagadoDscto}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format($pacdet->pago_actual, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format($pacdet->moneda_nac, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format($pacdet->otra_moneda_bs, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'><span class="blue small">{{number_format($pacdet->tasa_cambio, 2, ",", ".")}}</span>&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'><span class="blue small">{{number_format($pacdet->monto_otra_moneda, 2, ",", ".")}}</span>&nbsp;&nbsp;</td>
					</tr>

					<?php 
						$Tpago_actual += $pacdet->pago_actual;
						$Tmoneda_nac += $pacdet->moneda_nac;
						$Totra_moneda_bs += $pacdet->otra_moneda_bs;
						$Tmonto_otra_moneda += $pacdet->monto_otra_moneda;
						$TTpago_actual += $pacdet->pago_actual;
						$TTmoneda_nac += $pacdet->moneda_nac;
						$TTotra_moneda_bs += $pacdet->otra_moneda_bs;
						$TTmonto_otra_moneda += $pacdet->monto_otra_moneda;

					?>
				@endforeach
				<tr class='btn-accion-tabla total-row fondo-blanco'>
					<td style='text-align:left;width: 10% !important;'></span></td>
					<td style='text-align:right;width: 30% !important;'>Total {{$aux_tipdoc_desc}}</span></td>
					<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
					<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
					<td style='text-align:right;width: 7.7% !important;'>{{number_format($Tpago_actual, 2, ",", ".")}}&nbsp;&nbsp;</td>
					<td style='text-align:right;width: 7.7% !important;'>{{number_format($Tmoneda_nac, 2, ",", ".")}}&nbsp;&nbsp;</td>
					<td style='text-align:right;width: 7.7% !important;'>{{number_format($Totra_moneda_bs, 2, ",", ".")}}&nbsp;&nbsp;</td>
					<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
					<td style='text-align:right;width: 7.7% !important;'><span class="blue small">{{number_format($Tmonto_otra_moneda, 2, ",", ".")}}</span>&nbsp;&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr class='total-row'>
				<td style='text-align:left;width: 10% !important;'></span></td>
				<td style='text-align:right;width: 30% !important;'>Total Bruto Honorarios Medico</span></td>
				<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($TTpago_actual, 2, ",", ".")}}&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($TTmoneda_nac, 2, ",", ".")}}&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($TTotra_moneda_bs, 2, ",", ".")}}&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'><span class="blue small">{{number_format($TTmonto_otra_moneda, 2, ",", ".")}}&nbsp;&nbsp;</td>
			</tr>
		</table>
	</div>
	
</div>