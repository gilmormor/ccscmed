<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ManualController extends Controller
{
    public function reciboPago()
    {
        $empresa = DB::table('empresa')
            ->first();

        $pdf = PDF::loadView('reportrecemp.manual', compact('empresa'))->setPaper('a4', 'portrait');
        return $pdf->stream('Manual_Usuario_ReciboPago.pdf');
    }
}
