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
        dd($cedula->datos);
        /* $datos = $cedula->datos;
        $aux_ced = $cedula->datos->emp_ced;
        $aux_numnom = $cedula->datos->mov_nummon; */

        $empresa = Empresa::orderBy('id')->get();
        if(count($datos) > 0){
            if(env('APP_DEBUG')){
                //return view('reportenviaremailconerror.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request'));
            }
            
            //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
            
            //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
            $pdf = PDF::loadView('reportenviaremailconerror.listado', compact('datos','empresa'));
            //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
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
            //$aux_email = strtolower(trim($nm_empleado->emp_email));
            $cuerpo = 'Listado correos con error';
            $asunto = $empresa->nombre . " Recibo Honorarios " . $cuerpo;
    

            Mail::to($aux_email)->send(new MailEnviarEmailConError($notificaciones,$asunto,$cuerpo,$nm_empleado,$pdfPath));

            // Elimina el archivo temporal después de enviarlo por correo electrónico
            unlink($pdfPath);

            //return $pdf->stream("ReciboHonorarios.pdf");
        }else{
            dd('Ningún dato disponible en esta consulta.');
        }
    }
}
