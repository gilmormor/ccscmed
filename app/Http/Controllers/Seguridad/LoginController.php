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
use Illuminate\Support\Facades\DB;
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
        $emailSolicitado = trim($request->email);

        // Buscar en tabla usuario
        $usuario = Usuario::where("email", $emailSolicitado)->first();

        if (!$usuario) {
            // Buscar en nm_empleados
            $nm_empleado = DB::table('nm_empleados')
                ->whereRaw('TRIM(emp_email) = ?', [$emailSolicitado])
                ->whereNotNull('emp_email')
                ->where('emp_email', '!=', '')
                ->first();

            if (!$nm_empleado) {
                return redirect('seguridad/reset')->with([
                    'mensaje'    => "Correo electrónico $emailSolicitado no existe.",
                    'tipo_alert' => 'alert-error'
                ]);
            }

            // Validar que la cédula no exista ya en usuario
            $cedulaExiste = Usuario::where('usuario', $nm_empleado->emp_ced)->first();
            if ($cedulaExiste) {
                return redirect('seguridad/reset')->with([
                    'mensaje'    => 'El usuario con cédula ' . $nm_empleado->emp_ced . ' ya existe en el sistema.',
                    'tipo_alert' => 'alert-error'
                ]);
            }

            // Crear usuario a partir de nm_empleados
            $now = now();
            $usuario = Usuario::create([
                'usuario'    => $nm_empleado->emp_ced,
                'password'   => bcrypt($nm_empleado->emp_contra),
                'pass'       => $nm_empleado->emp_contra,
                'nombre'     => trim($nm_empleado->emp_nom) . ' ' . trim($nm_empleado->emp_ape),
                'email'      => trim($nm_empleado->emp_email),
                'foto'       => 'hombre.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Asignar sucursal_id=1 si existe en sucursal y no tiene ya registro
            $sucursalExiste = DB::table('sucursal')->where('id', 1)->exists();
            if ($sucursalExiste) {
                $sucursalUsuarioExiste = DB::table('sucursal_usuario')
                    ->where('sucursal_id', 1)
                    ->where('usuario_id', $usuario->id)
                    ->exists();
                if (!$sucursalUsuarioExiste) {
                    DB::table('sucursal_usuario')->insert([
                        'sucursal_id' => 1,
                        'usuario_id'  => $usuario->id,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }
            }
        }

        $user = $usuario;

        $validacion = Validator::make(['email' => trim($user->email)], [
            'email' => 'required|email',
        ]);

        if (!$validacion->passes()) {
            return redirect('seguridad/reset')->with([
                'mensaje'    => 'El correo electrónico no es válido.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $request->request->add(['pass' => $user->pass]);
        $request->request->add(['password' => $user->pass]);
        $user->update(array_filter($request->all()));

        $usuariorol = UsuarioRol::where("rol_id", 2)
                        ->where("usuario_id", $user->id)->first();
        if (!$usuariorol) {
            UsuarioRol::create([
                "rol_id"     => 2,
                "usuario_id" => $user->id
            ]);
        }

        Event(new SolicitarSendPassUsuario($user));

        return redirect('seguridad/login')->with([
            'mensaje'    => 'Contraseña enviada, revise su Correo.',
            'tipo_alert' => 'alert-success'
        ]);
    }

}
