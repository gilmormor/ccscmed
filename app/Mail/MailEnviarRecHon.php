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
    public $pdfPathRelPac;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg,$asunto,$cuerpo,$tabla,$pdfPath,$pdfPathRelPac)
    {
        $this->subject = $asunto;
        $this->cuerpo = $cuerpo;
        $this->msg = $msg;
        $this->tabla = $tabla;
        $this->pdfPath = $pdfPath;
        $this->pdfPathRelPac = $pdfPathRelPac;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $nm_movnomtrab = $this->msg["nm_movnomtrab"];
        $fromAddress   = config('mail.from.address');
        $fromName      = config('mail.from.name');

        if($this->pdfPathRelPac != ""){
            return $this->from($fromAddress, $fromName)
                        ->view('mails.enviarrechon')
                        ->attach($this->pdfPath, [
                            'as' => "$nm_movnomtrab->mov_numrec.pdf",
                            'mime' => 'application/pdf',
                        ])
                        ->attach($this->pdfPathRelPac, [
                            'as' => "$nm_movnomtrab->mov_numrec" . "_relpac.pdf",
                            'mime' => 'application/pdf',
                        ]);
        }else{
            return $this->from($fromAddress, $fromName)
                        ->view('mails.enviarrechon')
                        ->attach($this->pdfPath, [
                            'as' => "$nm_movnomtrab->mov_numrec.pdf",
                            'mime' => 'application/pdf',
                        ]);
        }
    }
}