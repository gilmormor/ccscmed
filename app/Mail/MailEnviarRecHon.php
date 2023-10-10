<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailEnviarRecHon extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $msg;
    public $cuerpo;
    public $tabla;
    public $pdfPath;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg,$asunto,$cuerpo,$tabla,$pdfPath)
    {
        $this->subject = $asunto;
        $this->cuerpo = $cuerpo;
        $this->msg = $msg;
        $this->tabla = $tabla;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $nm_movnomtrab = $this->msg["nm_movnomtrab"];
        return $this->view('mails.enviarrechon')
                    ->attach($this->pdfPath, [
                        'as' => "$nm_movnomtrab->mov_numrec.pdf", // Nombre del archivo adjunto
                        'mime' => 'application/pdf', // Tipo MIME del archivo PDF
                    ]);
    }
}
