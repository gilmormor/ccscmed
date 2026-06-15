<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ManualController extends Controller
{
    public function reciboPago()
    {
        ini_set('memory_limit', '256M');

        $empresa = DB::table('empresa')->first();

        if (!$empresa) {
            abort(404, 'No se encontró configuración de empresa.');
        }

        $pdf = PDF::loadView('reportrecemp.manual', compact('empresa'))->setPaper('a4', 'portrait');
        return $pdf->stream('Manual_Usuario_ReciboPago.pdf');
    }
}
