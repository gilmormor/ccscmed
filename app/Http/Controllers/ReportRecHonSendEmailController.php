<?php

namespace App\Http\Controllers;

use App\Events\EnviarRecHon;
use App\Models\Empresa;
use App\Models\Nm_MovHist;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportRecHonSendEmailController extends Controller
{
    public function index()
    {
        can('listar-recibo-honorarios-send-mail');

        $nominaPeriodos = Nm_MovHist::periodos();
        //dd($nominaPeriodos);

        //return view('reportrechonsendemail.index');
        return view('reportrechonsendemail.index', compact('nominaPeriodos'));
    }

    public function sendemailx(Request $request)
    {
        //dd($request);
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $sql = "SELECT nm_movhist.emp_ced,nm_movhist.mov_nummon
        FROM nm_movhist INNER JOIN nm_empleados
        ON nm_movhist.emp_ced = nm_empleados.emp_ced
        where nm_movhist.mov_nummon=$request->mov_nummon
        AND nm_empleados.emp_email != '' AND !ISNULL(nm_empleados.emp_email)
        GROUP BY nm_movhist.emp_ced;";
        $cedulas = DB::select($sql);

        foreach ($cedulas as $cedula) {
            Event(new EnviarRecHon($cedula));
        }
        /*
        return response()->json([
            'id' => 0,
            'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
            'tipo_alert' => 'error'
        ]);
        */
       return response()->json([
            'id' => 1,
            'title'=>'Correos enviados.',
            'mensaje'=>'Proceso finalizo con exito.',
            'tipo_alert' => 'success'
        ]);

    }

        //ENVIAR CORREOS DE FORMA AUTOMATICA
    //20 CORREO EN PERIODOS DE 1 HORA
    public function sendemail(Request $request)
    {
        //dd($request);
        $empresa = Empresa::orderBy('id')->get();
        $usuario = Usuario::findOrFail(auth()->id());
        $sql = "SELECT nm_movhist.emp_ced,nm_movhist.mov_nummon
        FROM nm_movhist INNER JOIN nm_empleados
        ON nm_movhist.emp_ced = nm_empleados.emp_ced
        INNER JOIN nm_control
        ON nm_movhist.mov_nummon = nm_control.cot_numnom
        INNER JOIN nm_movnomtrab
        ON nm_movnomtrab.mov_numnom = nm_movhist.mov_nummon AND nm_movnomtrab.mov_ced = nm_movhist.emp_ced
        where nm_empleados.emp_email != '' 
        AND !ISNULL(nm_empleados.emp_email)
        AND ISNULL(nm_control.cot_stasendemail)
        AND ISNULL(nm_movnomtrab.mov_stafecsendemail)
        GROUP BY nm_movhist.emp_ced,nm_movhist.mov_nummon LIMIT 1;";
        $cedulas = DB::select($sql);

        // nm_movhist.mov_nummon=$request->mov_nummon AND 

        //dd($cedulas);

        foreach ($cedulas as $cedula) {
            //Event(new EnviarRecHon($cedula));
            $sql = "UPDATE nm_movnomtrab set mov_stafecsendemail = NOW() where mov_ced = $cedula->emp_ced and mov_numnom = $cedula->mov_nummon;";
            $actualizar = DB::select($sql);
        }
        /*
        return response()->json([
            'id' => 0,
            'mensaje'=>'Registro no puede editado, fué modificado por otro usuario.',
            'tipo_alert' => 'error'
        ]);
        */
       return response()->json([
            'id' => 1,
            'title'=>'Correos enviados.',
            'mensaje'=>'Proceso finalizo con exito.',
            'tipo_alert' => 'success'
        ]);

    }
    
}
