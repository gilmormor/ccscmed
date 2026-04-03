<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;

class ManualController extends Controller
{
    public function reciboPago()
    {
        $pdf = PDF::loadView('reportrecemp.manual')->setPaper('a4', 'portrait');
        return $pdf->stream('Manual_Usuario_ReciboPago.pdf');
    }
}
