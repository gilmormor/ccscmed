<?php

namespace App\Http\Controllers;

use App\Events\EnviarEmailConError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $array_erroremail = CedularErrorEmail(1);
        //dd($array_erroremail);

        Event(new EnviarEmailConError($array_erroremail));
        
        $sql = "UPDATE nm_control SET nm_control.cot_stasendemail = '" . date("Y-m-d H:i:s") . "' WHERE ISNULL(nm_control.cot_stasendemail);";
        //dd($sql);
        $actualizar = DB::select($sql);

        return response()->json([
            'id' => 1,
            'title'=>'Correos enviados.',
            'mensaje'=>'Proceso finalizo con exito.',
            'tipo_alert' => 'success'
        ]);

    }
}
