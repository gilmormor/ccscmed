<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">
<?php 
	use App\Models\dtedte;
	//dd($datas);
?>
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
					<span class="h3">RECIBO HONORARIOS</span>
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
					<span class="h3">{{$nm_movnomtrab->car_desc}}</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:10%"><strong>Cédula:</strong> </td><td style="width:50%">{{number_format($nm_empleado->emp_ced, 0, ",", ".")}}</td>
							<td style="width:15%"><strong>Tasa Liberacion:</strong> </td><td style="width:10%">{{number_format($tasacamb, 2, ",", ".")}}</td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><strong>Nombre:</strong> </td><td style="width:50%">{{$nm_empleado->emp_nom}} {{$nm_empleado->emp_ape}}</td>
							<td style="width:15%"><strong>Tasa Promedio:</strong> </td><td style="width:10%">{{number_format($nm_control->cot_valordolar, 2, ",", ".")}}</td>
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
					<th style='text-align:right;width: 7.7% !important;'>Bs General</th>
					<th style='text-align:right;width: 7.7% !important;'>Otra Mon.</th>
					<th style='text-align:right;width: 7.7% !important;'>Bs. Netos</th>
					<th style='text-align:right;width: 7.7% !important;'>Deduc Bs.</th>
					<th style='text-align:right;width: 7.7% !important;'>Otra Mon</th>
					<th style='text-align:right;width: 7.7% !important;'>Bs. Netos</th>
					<th style='text-align:right;width: 7.7% !important;'>ME</th>
					<th style='text-align:right;width: 7.7% !important;'>Tasa</th>
					<th style='text-align:right;width: 7.7% !important;'>Saldo Bs</th>
				</tr>
			</thead>
			<?php
				$totalAsigBsGeneral = 0;
				$totalAsigOtraMon = 0;
				$totalAsigBsNetos = 0;
				$totalDeduBsGeneral = 0;
				$totalDeduOtraMon = 0;
				$totalDeduBsNetos = 0;
				$totalME = 0;
				$totaldll = 0;
				$totalacum = 0;
				$totalHonxPagarBs = 0;
				$totalHonxPagarME = 0;
				$NetoAPagarRefBs = 0;
				$aux_anticipobs = 0;
				$aux_anticipodll = 0;

			?>
			<tbody id="detalle_productos">
				@foreach($nm_movhists as $nm_movhist)
				<?php 
					$signo = $nm_movhist->con_asided == "A" ? 1 : -1;
					$totalAsigBsGeneral += (strpos("AOF", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0;
					$totalAsigOtraMon += (strpos("AOF", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mme_montomone : 0;
					$totalAsigBsNetos += (strpos("AOF", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mov_monto - $nm_movhist->mme_montomone : 0;
					$totalDeduBsGeneral += (strpos("D", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0;
					$totalDeduOtraMon += (strpos("D", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mme_montomone : 0;
					$totalDeduBsNetos += (strpos("D", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto - $nm_movhist->mme_montomone : 0;
					$totalME += (($nm_movhist->mov_codcon == 307 and $nm_movhist->mme_montodl > 0) ? $nm_movhist->mme_montodll : $nm_movhist->mme_montodl)  * $signo;

					$totalacum += ($nm_movhist->mov_monto*$signo)-($nm_movhist->mme_montomone * $signo);

					$totalHonxPagarBs += ($nm_movhist->mov_monto * $signo)-($nm_movhist->mme_montomone * $signo);
					$totalHonxPagarME += $nm_movhist->mme_montomone * $signo;

					$aux_anticipobs += ($nm_movhist->mov_codcon == 307 ? $nm_movhist->mov_monto : 0);
					$aux_anticipodll += ($nm_movhist->mov_codcon == 307 ? $nm_movhist->mme_montodll : 0);
				?>
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:left;width: 30.7% !important;'>{{$nm_movhist->con_desc . $nm_movhist->mov_ref}}</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("AOF", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("AOF", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mme_montomone : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("AOF", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mov_monto - $nm_movhist->mme_montomone : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("D", $nm_movhist->mov_tipocon) !== false) ? $nm_movhist->mov_monto : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("D", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mme_montomone : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format((strpos("D", $nm_movhist->mov_tipocon) !== false and $nm_movhist->mme_montomone > 0) ? $nm_movhist->mov_monto - $nm_movhist->mme_montomone : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format(($nm_movhist->mov_codcon == 307 and $nm_movhist->mme_montodl > 0) ? $nm_movhist->mme_montodll : $nm_movhist->mme_montodl, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format(($nm_movhist->mov_codcon == 307 or $nm_movhist->mme_montodl > 0) ? $nm_movhist->mme_tasacambi : 0, 2, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{number_format($totalacum, 2, ",", ".")}}&nbsp;&nbsp;</td>
					</tr>
					<?php 

					?>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
				<td style='text-align:center;width: 30.7% !important;'><strong>Total</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalAsigBsGeneral, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalAsigOtraMon, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalAsigBsNetos, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalDeduBsGeneral, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalDeduOtraMon, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalDeduBsNetos, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalME, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>{{number_format($totalacum, 2, ",", ".")}}&nbsp;&nbsp;</strong></td>
			</tr>
		</table>
	</div>
	<?php 
		$NetoPagarRefBs = $totalHonxPagarBs / (($totalHonxPagarBs > 0) ? (($tasacamb > 0) ? $tasacamb : $nm_control->cot_valordolar) : 1);
		$NetoPagarRefME = $totalHonxPagarME / (($totalHonxPagarME > 0) ? (($tasacamb > 0) ? $tasacamb : $nm_control->cot_valordolar) : 1);
	?>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
				<td style='text-align:center;width: 30.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td colspan="3" style='text-align:right;'></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>Bs.&nbsp;&nbsp;</strong></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'><strong>ME&nbsp;&nbsp;</strong></td>
			</tr>
			<tr>
				<td style='text-align:center;width: 30.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td colspan="3" style='text-align:right;'><strong>Total Honorarios por pagar:</strong></td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($totalHonxPagarBs, 2, ",", ".")}}&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($totalHonxPagarME, 2, ",", ".")}}&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td style='text-align:center;width: 30.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td colspan="3" style='text-align:right;'><strong>Neto a pagar Ref ME:</strong></td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($NetoPagarRefBs, 2, ",", ".")}}&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($NetoPagarRefME, 2, ",", ".")}}&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td style='text-align:center;width: 30.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td colspan="3" style='text-align:right;'><strong>Total Moneda Extranjera:</strong></td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($aux_anticipobs, 2, ",", ".")}}&nbsp;&nbsp;</td>
				<td style='text-align:right;width: 7.7% !important;'></td>
				<td style='text-align:right;width: 7.7% !important;'>{{number_format($aux_anticipodll, 2, ",", ".")}}&nbsp;&nbsp;</td>
			</tr>

		</table>
	</div>
	
</div>