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

    public function sendemail(Request $request)
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



    }
    
}
