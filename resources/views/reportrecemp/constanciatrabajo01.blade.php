<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">
<?php
	$cedula_empleado=$nm_movnomtrab->emp_rif;
	$nombres_empleado=trim($nm_movnomtrab->emp_nom);
	$fecha_ingreso=$nm_movnomtrab->mov_fecing;
	$cargo=trim($nm_movnomtrab->car_desc);
	$sueldo=$nm_movnomtrab->mov_sueldo;
	$jefe_RRHH=$nm_empresa->emp_nombrefirma;
	$sueldo_letras=num2letras($nm_movnomtrab->mov_sueldo,false);
	$sueldo_numeros=$nm_movnomtrab->mov_sueldo;
	$sueldo_formato=number_format($sueldo_numeros, 2, ",", ".");
	$empresa=trim($nm_empresa->emp_nombre);
	$fecha_actualletra=fechaEnTexto(date("Y-m-d"));
	//$porcentaje_cestaticket=$_REQUEST['porcentaje_cestaticket'];
	$cod_empresa=$nm_empresa->emp_codh;
	switch($cod_empresa)
	{
		case 1: //centro clinico
		{	$ruta_logo='logos/ccsc2.png';
		//	$membrete='Centro Clinico San Cristóbal</br></br>"hospital privado c.a.</br></br>CAPITAL PAGADO Y SUSCRITO: 3.500.000,00 BsF</br> Dirección: Av. Las Pilas Edif. Centro Clínico San Cristóbal</br>Piso 3 Of. 3Urb. Santa Inés</br>San Cristóbal - Estado Táchira - Venezuela</br>Telfs: (0276)340.61.99 / 340.61.00</br>E-mail: ccsc@telcel.net.ve</br>RIF: J-09008017-1</br>';
			$membrete="<p align='center'>Centro Clinico San Crist&oacute;bal Hospital Privado C.A.</p>          
		<p align='center'>Direcci&oacute;n: Av. Las Pilas Edif. Centro Cl&iacute;nico San Crist&oacute;bal Piso 3 Of. 3 Urb. Santa In&eacute;s</p>      
		<p align='center'>San Crist&oacute;bal - Estado T&aacute;chira   - Venezuela</p>
		<p align='center'>Telfs: (0276)340.61.99 / 340.61.00</p>     
		<p align='center'>RIF: J-09008017-1 </p>";
			break;
		}
		case 4: //servicios nutricionales
		{
			$ruta_logo='logos/nutricion.png';
			$membrete="<p align='center'>RIF J-309114077</p>                      
			<p align='center'>Telfs: (0276)340.61.48 / 340.63.09</p>     
			<p align='center'>San Crist&oacute;bal - Estado T&aacute;chira   - Venezuela</p>";
			break;
		}
		case 5:
		{
			$ruta_logo='logos/farmaclinico.png';
			$membrete="<p align='center'>RIF J-317003659</p>                      
			<p align='center'>Telfs: (0276)340.64.54 / 340.63.09</p>     
			<p align='center'>San Crist&oacute;bal - Estado T&aacute;chira   - Venezuela</p>";
			break;
		}
	}
?>
<div id='apDiv1'>
<table width='90%' border='0' align='center'>
  <tr>
    <td><p align='center'><div id='logo'><img src='{{ asset($ruta_logo) }}' width='120' height='100' /></div></p></td>
    <td align='center' style='line-height:14px; font-family: Times New Roman; font-size:12px; line-height: 2.0;' width='80%' >{!! $membrete !!}</td>
    <td style='line-height:6px;' width='0%'></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
   <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4'></td>
  </tr>
  <tr>
    <td align='center' colspan='4'><div id='tit_constancia'>CONSTANCIA</div></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>  
	<td colspan='4'></td>
  </tr>
  <tr>
    <td colspan='4' style='text-align:justify; font-family: Times New Roman; line-height: 2.0; text-indent: 5em; font-size:16px;' >
		<div id='cuerpo_constancia3'>
			<p>Quien suscribe, Jefe  del Departamento de Talento Humano de la Sociedad Mercantil <strong>{{$empresa}}</strong>,<strong> </strong> hace constar que el (la) ciudadano(a) <strong>{{$nombres_empleado}}</strong>, titular de la cédula de identidad No. <strong>{{$cedula_empleado}}</strong>, trabaja en esta empresa desde el <strong>{{$fecha_ingreso}}</strong>,<strong> </strong>desempeña actualmente el cargo de <strong>{{$cargo}}</strong>,<strong> </strong>devenga un salario integral mensual de <strong>{{$sueldo_letras}} CÉNTIMOS (Bs.{{$sueldo_formato}})</strong>, y recibe el  beneficio de alimentación diario.
      		{{-- <p><strong>&nbsp;</strong></p> --}}
      		<br><br>Constancia que se expide a petición de parte interesada  para fines legales consiguientes en la ciudad de San Cristóbal, a los {{$fecha_actualletra}}.</p>
	  	</div>	  
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan='2'>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan='2'>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan='2'>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan='2'>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan='2'>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td colspan='4' style='text-align:center; font-family: Times New Roman; line-height: 2.0; text-indent: 5em; font-size:16px;'>
	<p align='center'>______________________________________________________________</p>
	<p align='center'>    	<div id='cuerpo_constancia'>
	  <div align='center'><strong>Lcda. {{$jefe_RRHH}}</strong>
	      </p>
	    </div>
	  <p align='center'><strong>JEFE DE DPTO. TALENTO HUMANO.</strong></p>
	  </div></td>
    </tr>
</table>
</div>