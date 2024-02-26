<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nm_MovHist extends Model
{
    protected $table = "nm_movhist";
    protected $fillable = [
        'mov_id',
        'emp_ced',
        'mov_codcon',
        'mov_tipocon',
        'mov_monto',
        'mov_factor',
        'mov_unid',
        'mov_saldo',
        'mov_ref',
        'emp_cod',
        'mov_nummon',
        'emp_codh',
        'gru_cod'
    ];

    public static function periodosnompersona($request){
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $user = Usuario::findOrFail(auth()->id());
            $aux_cedula = $user->usuario;
            //$aux_cedula = "2450604";
        }

        $sql = "SELECT nm_control.*,
        DATE_FORMAT(cot_fdesde, '%d/%m/%Y') AS fdesde,
        DATE_FORMAT(cot_fhasta, '%d/%m/%Y') AS fhasta
        FROM nm_movhist INNER JOIN nm_control
        ON nm_movhist.mov_nummon = nm_control.cot_numnom
        where nm_movhist.emp_ced = $aux_cedula 
        group by nm_movhist.mov_nummon 
        order by nm_control.cot_fdesde desc;";

        $datas = DB::select($sql);
        return $datas;

    }

    public static function periodos(){

        $sql = "SELECT nm_control.*
        FROM nm_movhist INNER JOIN nm_control
        ON nm_movhist.mov_nummon = nm_control.cot_numnom
        group by nm_movhist.mov_nummon 
        order by nm_control.cot_fdesde desc;";

        $datas = DB::select($sql);
        return $datas;

    }

    public static function consultarecibo($request){
        $user = Usuario::findOrFail(auth()->id());
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $user = Usuario::findOrFail(auth()->id());
            $aux_cedula = $user->usuario;
            //$aux_cedula = "2450604";
        }

        $sql = "SELECT nm_conceptos.*, nm_movhist.*,nm_movhismonext.*
        FROM nm_empleados INNER JOIN nm_movhist 
        ON nm_empleados.emp_ced = nm_movhist.emp_ced 
        AND nm_movhist.emp_codh=nm_empleados.emp_codh AND nm_movhist.gru_cod = nm_empleados.gru_cod
        inner join nm_conceptos 
        ON nm_conceptos.con_cod=nm_movhist.mov_codcon 
        and nm_conceptos.gru_cod=nm_movhist.gru_cod
        LEFT JOIN nm_movhismonext
        ON nm_movhismonext.mov_id = nm_movhist.mov_id
        where nm_empleados.emp_ced=$aux_cedula
        and nm_movhist.mov_nummon=$request->mov_nummon
        ORDER BY nm_conceptos.con_asided,nm_conceptos.con_cod;";

        $datas = DB::select($sql);
        //dd($datas);
        return $datas;

    }

    public static function consultarecibolote($aux_ced,$aux_numnom){
        //$user = Usuario::findOrFail(auth()->id());
        $sql = "SELECT nm_conceptos.*, nm_movhist.*,nm_movhismonext.*
        FROM nm_empleados INNER JOIN nm_movhist 
        ON nm_empleados.emp_ced = nm_movhist.emp_ced 
        AND nm_movhist.emp_codh=nm_empleados.emp_codh AND nm_movhist.gru_cod = nm_empleados.gru_cod
        inner join nm_conceptos 
        ON nm_conceptos.con_cod=nm_movhist.mov_codcon 
        and nm_conceptos.gru_cod=nm_movhist.gru_cod
        LEFT JOIN nm_movhismonext
        ON nm_movhismonext.mov_id = nm_movhist.mov_id
        where nm_empleados.emp_ced= $aux_ced
        and nm_movhist.mov_nummon= $aux_numnom
        ORDER BY nm_conceptos.con_asided,nm_conceptos.con_cod;";

        $datas = DB::select($sql);
        return $datas;

    }

}
