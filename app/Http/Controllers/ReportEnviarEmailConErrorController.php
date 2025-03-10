<?php

namespace App\Http\Controllers;

use App\Events\EnviarEmailConError;
use Illuminate\Http\Request;

class ReportEnviarEmailConErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    
    public function sendemail()
    {
        $status_nm_control = true;
        $array_erroremail = CedularErrorEmail($status_nm_control,false);
        //dd($array_erroremail);

        Event(new EnviarEmailConError($array_erroremail));

        return response()->json([
            'id' => 1,
            'title'=>'Correos enviados.',
            'mensaje'=>'Proceso finalizo con exito.',
            'tipo_alert' => 'success'
        ]);

    }
}
