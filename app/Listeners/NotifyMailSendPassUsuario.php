<?php

namespace App\Listeners;

use App\Events\SolicitarSendPassUsuario;
use App\Mail\MailSendPassUsuario;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailSendPassUsuario
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SolicitarSendPassUsuario $event)
    {
        //dd($event->usuario);
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $usuario = $event->usuario;
        $notificaciones = new Notificaciones();
        $notificaciones->usuarioorigen_id = $usuario->id;
        $aux_email = trim($usuario->email);
        $notificaciones->usuariodestino_id = $usuario->id;
        $notificaciones->status = 1;                    
        $notificaciones->nombretabla = 'usuario';
        $notificaciones->mensaje = 'Recuperacion contraseña. ' . urlRaiz();
        $notificaciones->nombrepantalla = $rutaPantalla; 'auth.passwords.email';
        $notificaciones->rutaorigen = $rutaOrigen;
        $notificaciones->rutadestino = 'login';
        $notificaciones->tabla_id = $usuario->id;
        $notificaciones->accion = 'Recuperar Contraseña.';
        $notificaciones->mensajetitle = 'Recuperar Cantraseña';
        $notificaciones->icono = 'fa fa-fw fa-truck text-yellow ';
        $notificaciones->save();
        //$usuario = Usuario::findOrFail(auth()->id());
        $asunto = $notificaciones->mensaje;
        $cuerpo = $notificaciones->mensaje;

        Mail::to($aux_email)->send(new MailSendPassUsuario($notificaciones,$asunto,$cuerpo,$usuario));

    }
}
