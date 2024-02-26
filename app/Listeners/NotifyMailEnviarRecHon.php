<?php

namespace App\Listeners;

use App\Mail\MailEnviarRecHon;
use App\Models\Empresa;
use App\Models\Nm_MovHist;
use App\Models\Notificaciones;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;

class NotifyMailEnviarRecHon
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
        $aux_ced = $cedula->datos->emp_ced;
        $aux_numnom = $cedula->datos->mov_nummon;

        $empresa = Empresa::orderBy('id')->get();
        $sql = "SELECT *
            FROM nm_empleados 
            WHERE emp_ced = $aux_ced;";
        $nm_empleado = DB::select($sql);
            $sql = "SELECT *
            FROM nm_movnomtrab 
            WHERE mov_ced = $aux_ced
            AND mov_numnom = $aux_numnom;";
        $nm_movnomtrab = DB::select($sql);
        $sql = "SELECT *
            FROM nm_control 
            WHERE cot_numnom = $aux_numnom;";
        $nm_control = DB::select($sql);
        if(count($nm_empleado) > 0 and count($nm_movnomtrab) > 0){
            $nm_empleado = $nm_empleado[0];
            $nm_movnomtrab = $nm_movnomtrab[0];
            $nm_control = $nm_control[0];
            $nm_movhists = Nm_MovHist::consultarecibolote($aux_ced,$aux_numnom);
            $tasacamb = 0;
            foreach($nm_movhists as $nm_movhist){
                if($nm_movhist->mme_tasacambiorig > 0){
                    $tasacamb = $nm_movhist->mme_tasacambiorig;
                    break;
                }
            }
            if($nm_empleado){
                if(env('APP_DEBUG')){
                    //return view('reportrechon.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request'));
                }
                
                //return view('notaventaconsulta.listado', compact('notaventas','empresa','usuario','aux_fdesde','aux_fhasta','nomvendedor','nombreAreaproduccion','nombreGiro','nombreTipoEntrega'));
                
                //$pdf = PDF::loadView('reportinvstockvend.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                $pdf = PDF::loadView('reportrechon.listado', compact('nm_control','nm_empleado','empresa','nm_movhists','nm_movnomtrab','usuario','request','tasacamb'));
                //$pdf = PDF::loadView('reportdtefac.listado', compact('datas','empresa','usuario','request'))->setPaper('a4', 'landscape');
                // Guarda el PDF en una ubicación temporal
                $pdfPath = storage_path("app/temp/$nm_movnomtrab->mov_numrec.pdf");
                $pdf->save($pdfPath);
                $empresa = Empresa::findOrFail(1);
                $notificaciones["nm_empleado"] = $nm_empleado;
                $notificaciones["nm_movnomtrab"] = $nm_movnomtrab;
                $notificaciones["nm_control"] = $nm_control;
                $notificaciones["nm_movhists"] = $nm_control;
                
                
                //$aux_email = "honorariosmedicos@ccsc.com.ve"; // trim($nm_empleado->emp_email);
                $aux_email = "gilmormor@gmail.com"; // trim($nm_empleado->emp_email);
                //$aux_email = trim($nm_empleado->emp_email);
                $cuerpo = 'Periodo: ' . date("d/m/Y", strtotime($nm_control->cot_fdesde)) . ' al ' . date("d/m/Y", strtotime($nm_control->cot_fhasta));
                $asunto = $empresa->nombre . " Recibo Honorarios " . $cuerpo;
        

                Mail::to($aux_email)->send(new MailEnviarRecHon($notificaciones,$asunto,$cuerpo,$nm_empleado,$pdfPath));

                // Elimina el archivo temporal después de enviarlo por correo electrónico
                unlink($pdfPath);

                //return $pdf->stream("ReciboHonorarios.pdf");
            }else{
                dd('Ningún dato disponible en esta consulta.');
            } 
        }




/*
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

        Mail::to($aux_email)->send(new MailEnviarRecHon($notificaciones,$asunto,$cuerpo,$usuario));
        */
    }
}
