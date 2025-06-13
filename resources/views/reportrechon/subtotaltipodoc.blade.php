
<tr class='btn-accion-tabla total-row fondo-blanco'>
	<td style='text-align:left;width: 10% !important;'></td>
	<td style='text-align:right;width: 30% !important;'>TOTAL {{strtoupper($aux_tipdoc_desc)}}</td>
	<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
	<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
	<td style='text-align:right;width: 7.7% !important;'>{{number_format($Tpago_actual, 2, ",", ".")}}&nbsp;&nbsp;</td>
	<td style='text-align:right;width: 7.7% !important;'>{{number_format($Tmoneda_nac, 2, ",", ".")}}&nbsp;&nbsp;</td>
	<td style='text-align:right;width: 7.7% !important;'>{{number_format($Totra_moneda_bs, 2, ",", ".")}}&nbsp;&nbsp;</td>
	<td style='text-align:right;width: 7.7% !important;'>&nbsp;&nbsp;</td>
	<td style='text-align:right;width: 7.7% !important;'><span class="blue small">{{number_format($Tmonto_otra_moneda, 2, ",", ".")}}</span>&nbsp;&nbsp;</td>
</tr>