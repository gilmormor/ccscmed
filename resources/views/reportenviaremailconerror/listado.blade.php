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
					<span class="h3">LISTADO CORREOS NO VALIDOS</span>
					<p><strong>Fecha:</strong> {{date("d/m/Y h:i:s A")}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round" style="padding-bottom: 0px;">
		<table id="factura_detalle" style="table-layout:fixed;width: 100%;">
			<thead>
				<tr>
					<th style='text-align:left;width: 30% !important;'>Cedula</th>
					<th style='text-align:right;width: 7.7% !important;'>Nombre</th>
					<th style='text-align:right;width: 7.7% !important;'>email</th>
					{{-- <th style='text-align:right;width: 7.7% !important;'>Error</th> --}}
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($datos as $dato)
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:left;width: 30.7% !important;'>{{$dato->emp_ced}}</td>
						<td style='text-align:right;width: 7.7% !important;'>{{$dato->empleado_nombre}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7.7% !important;'>{{$dato->email}}&nbsp;&nbsp;</td>
						{{-- <td style='text-align:right;width: 7.7% !important;'>{{$dato->emp_ced}}&nbsp;&nbsp;</td> --}}
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>	
</div>