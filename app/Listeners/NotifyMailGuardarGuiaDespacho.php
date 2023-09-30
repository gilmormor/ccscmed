<?php

namespace App\Listeners;

use App\Events\GuardarGuiaDespacho;
use App\Mail\MailInicioDespacho;
use App\Mail\MailNotaVentaDevuelta;
use App\Models\DespachoOrd;
use App\Models\NotaVenta;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyMailGuardarGuiaDespacho
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
    public function handle(GuardarGuiaDespacho $event)
    {
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $despachoord = $event->despachoord;
        $notificaciones = new Notificaciones();
        $notificaciones->usuarioorigen_id = auth()->id();
        $aux_email = $despachoord->notaventa->vendedor->persona->email;
        if($despachoord->notaventa->vendedor->persona->usuario){
            $notificaciones->usuariodestino_id = $despachoord->notaventa->vendedor->persona->usuario->id;
            $aux_email = $despachoord->notaventa->vendedor->persona->usuario->email;
        }
        $notificaciones->vendedor_id = $despachoord->notaventa->vendedor_id;
        $notificaciones->status = 1;                    
        $notificaciones->nombretabla = 'despachoord';
        $notificaciones->mensaje = 'Inicio Despacho OD:'.$despachoord->id.' NV:'.$despachoord->notaventa_id;
        $notificaciones->nombrepantalla = $rutaPantalla; 'despachoord.indexguiafact';
        $notificaciones->rutaorigen = $rutaOrigen;
        $notificaciones->rutadestino = 'notaventaconsulta';
        $notificaciones->tabla_id = $despachoord->id;
        $notificaciones->accion = 'Despacho Iniciado.';
        $notificaciones->mensajetitle = 'Nro. Guia despacho: '.$despachoord->guiadespacho;
        $notificaciones->icono = 'fa fa-fw fa-truck text-yellow ';
        $notificaciones->save();
        //$usuario = Usuario::findOrFail(auth()->id());
        $asunto = $notificaciones->mensaje;
        $cuerpo = $notificaciones->mensaje;

        Mail::to($aux_email)->send(new MailInicioDespacho($notificaciones,$asunto,$cuerpo,$despachoord));

    }
}
