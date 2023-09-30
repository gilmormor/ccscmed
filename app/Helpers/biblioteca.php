<?php

use App\Models\Admin\Menu;
use App\Models\Admin\Permiso;
use Illuminate\Database\Eloquent\Builder;

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
?>