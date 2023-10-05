<?php

namespace App\Http\Controllers\Seguridad;

use App\Events\InicioSesionUsuario;
use App\Events\SolicitarSendPassUsuario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidarUsuario;
use App\Models\Seguridad\Usuario;
use App\Models\Seguridad\UsuarioRol;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('seguridad.index');
    }

    protected function authenticated(Request $request, $user)
    {
        //$roles = $user->roles()->where('estado', 1)->get();
        $roles = $user->roles()->get();
        if ($roles->isNotEmpty()) {
            $user->setSession($roles->toArray());
            event(new InicioSesionUsuario());
        } else {
            $this->guard()->logout();
            $request->session()->invalidate();
            return redirect('seguridad/login')->withErrors(['error' => 'Este usuario no tiene un rol activo']);
        }
    }

    public function username()
    {
        return 'usuario';
    }

    public function sendpass(Request $request)
    {
        //dd($request);
        $usuario = Usuario::where("email",$request->email)->get();
        //dd($usuario[0]->id);
        if(count($usuario) > 0){
            $user = Usuario::findOrFail($usuario[0]->id);

            $validacion = Validator::make(['email' => trim($user->email)], [
                'email' => 'required|email',
            ]);

            if ($validacion->passes()) {
                // El correo electrónico es válido
                $request->request->add(['pass' => $user->pass]);
                $request->request->add(['password' => $user->pass]);
                $user->update(array_filter($request->all()));
                $usuariorol = UsuarioRol::where("rol_id",2)
                            ->where("usuario_id",$user->id)->get();
                if($usuariorol->count() == 0){
                    UsuarioRol::create([
                        "rol_id" => 2,
                        "usuario_id" => $user->id
                    ]);
                }
                //$user->update(array_filter(['password' => $user->pass]));
                //$user->update(['password' => $claveencriptada]);
                Event(new SolicitarSendPassUsuario($user));
                return redirect('seguridad/login')->with([
                    'mensaje'=>'Clave enviada, revise su Correo.',
                    'tipo_alert' => 'alert-success'
                ]);

                return 'El correo electrónico es válido.';
            } else {
                // El correo electrónico no es válido
                return redirect('seguridad/reset')->with([
                    'mensaje'=>'El correo electrónico no es válido.',
                    'tipo_alert' => 'alert-error'
                ]);
                return 'El correo electrónico no es válido.';
            }
            //dd($user);
        }else{
            return redirect('seguridad/reset')->with([
                'mensaje'=>"Correo electrónico $request->email no existe.",
                'tipo_alert' => 'alert-error'
            ]);
        }
    }

}
