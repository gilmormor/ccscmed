<?php

use App\Models\Admin\Menu;
use App\Models\Admin\Permiso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

if (!function_exists('getMenuActivo')) {
    function getMenuActivo($ruta)
    {
        if (request()->is($ruta) || request()->is($ruta . '/*')) {
            return 'active menu-open';
        } else {
            return '';
        }
    }
}
if (!function_exists('canUser')) {
    function can($permiso, $redirect = true)
    {
        if (session()->get('rol_nombre') == 'administrador') {
            return true;
        } else {
            $rolId = session()->get('rol_id');
            /*
            $permisos = cache()->tags('Permiso')->rememberForever("Permiso.rolid.$rolId", function () {
                return Permiso::whereHas('roles', function ($query) {
                    $query->where('rol_id', session()->get('rol_id'));
                })->get()->pluck('slug')->toArray();
            });
            */
            $permisos = Permiso::whereHas('roles', function ($query) {
                $query->where('rol_id', session()->get('rol_id'));
                })->get()->pluck('slug')->toArray();
            if (!in_array($permiso, $permisos)) {
                if ($redirect) {
                    if (!request()->ajax())
                        $permisoT = Permiso::where("slug",$permiso)->get();
                        $nombre_permiso = "";
                        if(count($permisoT) > 0){
                            $nombre_permiso = ": " . $permisoT[0]->nombre;
                        }
                        return redirect()->route('inicio')->with('mensaje', 'No tienes permisos para entrar en este módulo' . $nombre_permiso)->send();
                    abort(403, 'No tiene permiso');
                } else {
                    return false;
                }
            }
            return true;
        }
    }
}

if (!function_exists('urlActual')) {
    function urlActual(){
        $ruta = url()->current();
        //$ruta = url()->current();
        $pos = 0;
        $cont = 0;
        for( $i=0 ; $i < strlen($ruta) ; $i++){
            if((substr($ruta,$i,1) == '/') and $cont < 3){
                $cont++;
                $pos = $i;
            }
        }
        return substr($ruta,$pos+1,(strlen($ruta)-1));

    }
}

if (!function_exists('urlPrevio')) {
    function urlPrevio(){
        $ruta = url()->previous();
        //$ruta = url()->current();
        $pos = 0;
        $cont = 0;
        for( $i=0 ; $i < strlen($ruta) ; $i++){
            if((substr($ruta,$i,1) == '/') and $cont < 3){
                $cont++;
                $pos = $i;
            }
        }
        return substr($ruta,$pos+1,(strlen($ruta)-1));

    }
}

function autoVer($url)
{
  $path = pathinfo($url);
  //dd($url);
  //$ver = '?v=' . filemtime($_SERVER['DOCUMENT_ROOT'].$url);
  $ver = '?v=' . filemtime($url);
  //return $path['dirname'].'/'.$path['basename'].$ver;
  //dd(asset($url).$ver);
  return (asset($url).$ver);
}

function getIniciales($nombre){
    $name = '';
    $explode = explode(' ',$nombre);
    foreach($explode as $x){
        $name .=  $x[0];
    }
    return $name;    
}

//FUNCION REEMPLAZA CARACTERES ESPECIALES
if (!function_exists('sanear_string')) {
    function sanear_string($string){
        $string = trim($string);
 
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "¨", "º", "~",
                "#", "@", "|", "!",
                "·", "$", "%", "&",
                "(", ")", "?", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "<"
            ),
            ' ',
            $string
        );

        /*
        $string = str_replace(
            array(
                "\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                "."
            ),
            '',
            $string
        );
        */
     
     
        return $string;
    }
}

if (!function_exists('cadVacia')) { //VALIDAR CADENA VACIA O NULL
    function cadVacia($cad)
    {
        if($cad == "" OR is_null($cad)){
            return true;
        }
        return false;
    }
}

if (!function_exists('urlRaiz')) {
    function urlRaiz(){
        $ruta = url()->current();
        $currentUrl = url()->current();

        $parsedUrl = parse_url($currentUrl);
        // Obtener el dominio
        $domain = $parsedUrl['host'];
        // Construir la URL deseada
        $desiredUrl = 'https://' . $domain;
        return $desiredUrl;
    }
}

if (!function_exists('usuariosConAccesoMenuURL')) {
    function usuariosConAccesoMenuURL($tabla,$url){
        $menu = Menu::where("url","=",$url)->get(); //BUSCO EN LA TABLA MENU EL REGISTRO QUE CONTIENE EL URL notaventaaprobar
        $menu = Menu::findOrFail($menu[0]->id); //LUEGO BUSCO EL ID EN MENU PARA TENER EL OBJETO COMPLETO CON SUS TABLAS HIJAS
        $arrayUsuarios = []; //ARRAY PARA ALMACENAR LOS USUARIOS
        //LUEGO RECORRO Y BUSCO TODOS LOS USUARIO QUE TIENEN ACCESO A ESTE URL PARA ENVIARLES EL CORREO
        //PERO SOLO LOS USUARIOS TIENEN ACCESO A LA MISMA SUCURSAL DE LA NOTA DE VENTA, PUEDEN SER VARIOS USUARIOS UE TENGAN ACCESO A APROBAR NOTAS DE VENTA
        foreach ($menu->menuroles as $menurol) {
            if($menurol->rol_id != 1){
                foreach ($menurol->usuarioroles as $usuariorol){
                    foreach ($usuariorol->usuario->sucursales as $sucursal) {
                        if($tabla->sucursal_id == $sucursal->id){
                            $arrayUsuarios[] = [
                                "usuario_id" =>$usuariorol->usuario->id,
                                "nombre" => $usuariorol->usuario->nombre,
                                "email" => $usuariorol->usuario->email
                            ];
                        }
                    }
                }
            }
        }
        return $arrayUsuarios;
    }
}
if (!function_exists('formatearRUT')) {
    function formatearRUT($rut){
        $rut = preg_replace('/[^0-9Kk]/', '', $rut); // Eliminar caracteres no numéricos ni K/k

        if (strlen($rut) < 2) {
            return $rut; // No se puede formatear si el RUT es demasiado corto
        }
    
        $digitoVerificador = strtoupper(substr($rut, -1)); // Último dígito (puede ser K/k)
        $restoRUT = substr($rut, 0, -1); // Resto del RUT
    
        $rutFormateado = $restoRUT . '-' . $digitoVerificador;
    
        return $rutFormateado;
    }
}
//Sucursales fisicas por Usuario. Sucursales a las que pertenese el Usuario a traves de tabla persona
if (!function_exists('sucFisXUsu')) {
    function sucFisXUsu($persona){
        $arraySuc = [];
        foreach ($persona->jefaturasucursalareas as $jefaturasucursalarea) {
            $arraySuc[] = $jefaturasucursalarea->sucursal_area->sucursal_id;
        }            
        return $arraySuc;
    }
}

//Sucursales fisicas por Usuario. Sucursales a las que pertenese el Usuario a traves de tabla persona
if (!function_exists('CedularErrorEmail')) {
    function CedularErrorEmail($status_envlistado){
        $aux_nm_movnomtrab_mov_stasendemailCond = " ISNULL(nm_movnomtrab.mov_stasendemail)";
        if($status_envlistado == 1){
            $aux_nm_controlCond = " ISNULL(nm_control.cot_stasendemail)";
            $aux_nm_movnomtrab_mov_stasendemailCond = " ISNULL(nm_movnomtrab.mov_stasendemail)";
        }
        if($status_envlistado == 2){
            $aux_nm_controlCond = " true";
            $aux_nm_movnomtrab_mov_stasendemailCond = " ISNULL(nm_movnomtrab.mov_stasendemail)";
        }

        $sql = "SELECT nm_movhist.emp_ced,nm_movhist.mov_nummon,nm_empleados.emp_ced,TRIM(nm_empleados.emp_email) as emp_email,
        concat(TRIM(nm_empleados.emp_nom), ' ' ,TRIM(nm_empleados.emp_ape)) as empleado_nombre
        FROM nm_movhist INNER JOIN nm_empleados
        ON nm_movhist.emp_ced = nm_empleados.emp_ced
        INNER JOIN nm_control
        ON nm_movhist.mov_nummon = nm_control.cot_numnom
        INNER JOIN nm_movnomtrab
        ON nm_movnomtrab.mov_numnom = nm_movhist.mov_nummon AND nm_movnomtrab.mov_ced = nm_movhist.emp_ced
        where $aux_nm_movnomtrab_mov_stasendemailCond
        AND $aux_nm_controlCond
        GROUP BY nm_movhist.emp_ced;";
        //dd($sql);
        /* $sql = "SELECT emp_ced,emp_email
        FROM nm_empleados
        ORDER BY emp_ced;"; */
        $cedulas = DB::select($sql);

        //dd($cedulas);
        $array_erroremail = [];
        foreach ($cedulas as $cedula) {
            $email = $cedula->emp_email;
            // Crear un validador manual
            $validator = Validator::make(
                ['emp_email' => $email],
                ['emp_email' => 'required|max:50|email:rfc,dns']
            );
            // Verificar si la validación falló
            if ($validator->fails()) {
                $array_erroremail[$cedula->emp_ced] = [
                    "emp_ced" => $cedula->emp_ced,
                    "empleado_nombre" => $cedula->empleado_nombre,
                    "email" => $email,
                    "error" => $validator->errors()
                ];
                // El correo no es válido, continuar con el siguiente registro
                continue;
            }

        }
        //dd($array_erroremail);
        return $array_erroremail;

    }
}

if (!function_exists('annomes')) {
    function annomes($mesanno){
        $arraymeanno = explode(" ", $mesanno);
        switch ($arraymeanno[0]) {
            case "Enero":
                $mes = "01";
                break;
            case "Febrero":
                $mes = "02";
                break;
            case "Marzo":
                $mes = "03";
                break;
            case "Abril":
                $mes = "04";
                break;
            case "Mayo":
                $mes = "05";
                break;
            case "Junio":
                $mes = "06";
                break;
            case "Julio":
                $mes = "07";
                break;
            case "Agosto":
                $mes = "08";
                break;
            case "Septiembre":
                $mes = "09";
                break;
            case "Octubre":
                $mes = "10";
                break;
            case "Noviembre":
                $mes = "11";
                break;
            case "Diciembre":
                $mes = "12";
                break;
        }
        $resultado = $arraymeanno[1].$mes;
        return $resultado;
    }
}

if (!function_exists('mesanno')) {
    function mesanno($annomes){
        $mes = substr($annomes,4,2);
        switch ($mes) {
            case "01":
                $mes = "Enero";
                break;
            case "02":
                $mes = "Febrero";
                break;
            case "03":
                $mes = "Marzo";
                break;
            case "04":
                $mes = "Abril";
                break;
            case "05":
                $mes = "Mayo";
                break;
            case "06":
                $mes = "Junio";
                break;
            case "07":
                $mes = "Julio";
                break;
            case "08":
                $mes = "Agosto";
                break;
            case "09":
                $mes = "Septiembre";
                break;
            case "10":
                $mes = "Octubre";
                break;
            case "11":
                $mes = "Noviembre";
                break;
            case "12":
                $mes = "Diciembre";
                break;
        }
        $resultado = $mes . " " . substr($annomes,0,4);
        return $resultado;
    }
}

function num2letras($num, $fem = false, $dec = true) { 
   $matuni[2]  = "dos"; 
   $matuni[3]  = "tres"; 
   $matuni[4]  = "cuatro"; 
   $matuni[5]  = "cinco"; 
   $matuni[6]  = "seis"; 
   $matuni[7]  = "siete"; 
   $matuni[8]  = "ocho"; 
   $matuni[9]  = "nueve"; 
   $matuni[10] = "diez"; 
   $matuni[11] = "once"; 
   $matuni[12] = "doce"; 
   $matuni[13] = "trece"; 
   $matuni[14] = "catorce"; 
   $matuni[15] = "quince"; 
   $matuni[16] = "dieciseis"; 
   $matuni[17] = "diecisiete"; 
   $matuni[18] = "dieciocho"; 
   $matuni[19] = "diecinueve"; 
   $matuni[20] = "veinte"; 
   $matunisub[2] = "dos"; 
   $matunisub[3] = "tres"; 
   $matunisub[4] = "cuatro"; 
   $matunisub[5] = "quin"; 
   $matunisub[6] = "seis"; 
   $matunisub[7] = "sete"; 
   $matunisub[8] = "ocho"; 
   $matunisub[9] = "nove"; 

   $matdec[2] = "veint"; 
   $matdec[3] = "treinta"; 
   $matdec[4] = "cuarenta"; 
   $matdec[5] = "cincuenta"; 
   $matdec[6] = "sesenta"; 
   $matdec[7] = "setenta"; 
   $matdec[8] = "ochenta"; 
   $matdec[9] = "noventa"; 
   $matsub[3]  = 'mill'; 
   $matsub[5]  = 'bill'; 
   $matsub[7]  = 'mill'; 
   $matsub[9]  = 'trill'; 
   $matsub[11] = 'mill'; 
   $matsub[13] = 'bill'; 
   $matsub[15] = 'mill'; 
   $matmil[4]  = 'millones'; 
   $matmil[6]  = 'billones'; 
   $matmil[7]  = 'de billones'; 
   $matmil[8]  = 'millones de billones'; 
   $matmil[10] = 'trillones'; 
   $matmil[11] = 'de trillones'; 
   $matmil[12] = 'millones de trillones'; 
   $matmil[13] = 'de trillones'; 
   $matmil[14] = 'billones de trillones'; 
   $matmil[15] = 'de billones de trillones'; 
   $matmil[16] = 'millones de billones de trillones'; 
   
   //Zi hack
   $float=explode('.',$num);
   $num=$float[0];

   $num = trim((string)@$num); 
   if ($num[0] == '-') { 
      $neg = 'menos '; 
      $num = substr($num, 1); 
   }else 
      $neg = ''; 
   while ($num[0] == '0') $num = substr($num, 1); 
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
   $zeros = true; 
   $punt = false; 
   $ent = ''; 
   $fra = ''; 
   for ($c = 0; $c < strlen($num); $c++) { 
      $n = $num[$c]; 
      if (! (strpos(".,'''", $n) === false)) { 
         if ($punt) break; 
         else{ 
            $punt = true; 
            continue; 
         } 

      }elseif (! (strpos('0123456789', $n) === false)) { 
         if ($punt) { 
            if ($n != '0') $zeros = false; 
            $fra .= $n; 
         }else 

            $ent .= $n; 
      }else 

         break; 

   } 
   $ent = '     ' . $ent; 
   if ($dec and $fra and ! $zeros) { 
      $fin = ' coma'; 
      for ($n = 0; $n < strlen($fra); $n++) { 
         if (($s = $fra[$n]) == '0') 
            $fin .= ' cero'; 
         elseif ($s == '1') 
            $fin .= $fem ? ' una' : ' un'; 
         else 
            $fin .= ' ' . $matuni[$s]; 
      } 
   }else 
      $fin = ''; 
   if ((int)$ent === 0) return 'Cero ' . $fin; 
   $tex = ''; 
   $sub = 0; 
   $mils = 0; 
   $neutro = false; 
   while ( ($num = substr($ent, -3)) != '   ') { 
      $ent = substr($ent, 0, -3); 
      if (++$sub < 3 and $fem) { 
         $matuni[1] = 'una'; 
         $subcent = 'as'; 
      }else{ 
         $matuni[1] = $neutro ? 'un' : 'uno'; 
         $subcent = 'os'; 
      } 
      $t = ''; 
      $n2 = substr($num, 1); 
      if ($n2 == '00') { 
      }elseif ($n2 < 21) 
         $t = ' ' . $matuni[(int)$n2]; 
      elseif ($n2 < 30) { 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      }else{ 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      } 
      $n = (int)$num[0]; 
	 
	  
      if ($n == 1) { 
         $t = ' ciento' . $t; 
      }elseif ($n == 5){ 
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
      }elseif ($n != 0){ 
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
      } 
      if ($sub == 1) { 
      }elseif (! isset($matsub[$sub])) { 
         if ($num == 1) { 
            $t = ' mil'; 
         }elseif ($num > 1){ 
            $t .= ' mil'; 
         } 
      }elseif ($num == 1) { 
         $t .= ' ' . $matsub[$sub] . '?n'; 
      }elseif ($num > 1){ 
         $t .= ' ' . $matsub[$sub] . 'ones'; 
      }   
      if ($num == '000') $mils ++; 
      elseif ($mils != 0) { 
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
         $mils = 0; 
      } 
      $neutro = true; 
      $tex = $t . $tex; 
   } 
   $tex = $neg . substr($tex, 1) . $fin; 
   //Zi hack --> return ucfirst($tex);
   $end_num=ucfirst($tex).' Bolivares '.($float[1] ?? '00').'/100';
   return $end_num; 
} 
function fechaEnTexto($fecha)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $timestamp = strtotime($fecha);

    $dia = date('d', $timestamp);
    $mes = $meses[(int)date('m', $timestamp)];
    $anio = date('Y', $timestamp);

    return "$dia dias del mes de $mes de $anio";
}
function fechaEnLetras($fecha) {
    $meses = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
        '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
        '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
        '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    ];

    [$anio, $mes, $dia] = explode('-', $fecha);

    return "$dia de " . $meses[$mes] . " de $anio";
}
?>