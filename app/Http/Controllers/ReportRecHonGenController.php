<?php

namespace App\Http\Controllers;

use App\Models\Nm_MovHist;
use Illuminate\Http\Request;

class ReportRecHonGenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-recibo-honorarios-general');
        return view('reportrechongen.index');
    }

    public function periodos(Request $request)
    {
        //dd($request);
        $nominaPeriodos = Nm_MovHist::periodosnompersona($request);
        return $nominaPeriodos;
    }
    
}
