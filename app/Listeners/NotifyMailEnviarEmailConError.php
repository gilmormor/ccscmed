<?php

namespace App\Listeners;

use App\Mail\MailEnviarEmailConError;
use App\Mail\MailEnviarRecHon;
use App\Models\Empresa;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;


class NotifyMailEnviarEmailConError
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
    public function handle($event)
    {
        //dd($event);
        $rutaPantalla = urlPrevio();
        $rutaOrigen = urlActual();
        $cedula = $event;
        //dd($cedula->datos[1551262]);
        $datos = $cedula->datos;
        /* $aux_ced = $cedula->datos->emp_ced;
        $aux_numnom = $cedula->datos->mov_nummon; */

        $empresa = Empresa::orderBy('id')->get();
        if(count($datos) > 0){
            if(env('APP_DEBUG')){
                //return view('reportenviaremailconerror.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request'));
            }
            //dd($datos);
            
            $pdf = PDF::loadView('reportenviaremailconerror.listado', compact('datos','empresa'));
            // Guarda el PDF en una ubicación temporal
            $pdfPath = storage_path("app/temp/enviaremailconerror.pdf");
            $pdf->save($pdfPath);
            $empresa = Empresa::findOrFail(1);
            /* $notificaciones["nm_empleado"] = $nm_empleado;
            $notificaciones["nm_movnomtrab"] = $nm_movnomtrab;
            $notificaciones["nm_control"] = $nm_control;
            $notificaciones["nm_movhists"] = $nm_control; */
            $notificaciones = "";
            $nm_empleado = "";
            
            
            //$aux_email = "honorariosmedicos@ccsc.com.ve"; // trim($nm_empleado->emp_email);
            $aux_email = "gilmormor@gmail.com"; // trim($nm_empleado->emp_email);
            $cc_email = "gilmormorm@gmail.com";
            //$aux_email = strtolower(trim($nm_empleado->emp_email));
            $cuerpo = '👨‍⚕ Honorarios Medicos - 📩🚩😱Listado correos NO VALIDOS😱📩🚩';
            $asunto = $empresa->nombre . " " . $cuerpo;
    
            //dd("entrro");
            Mail::to($aux_email)
                        ->cc($cc_email)
                        ->send(new MailEnviarEmailConError($notificaciones,$asunto,$cuerpo,$nm_empleado,$pdfPath));

            // Elimina el archivo temporal después de enviarlo por correo electrónico
            unlink($pdfPath);

            //return $pdf->stream("ReciboHonorarios.pdf");
        /* }else{
            dd('Ningún dato disponible en esta consulta.'); */
        }
    }
}
