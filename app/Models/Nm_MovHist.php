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

    public static function empresas($request){
        if(!empty($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $user = Usuario::findOrFail(auth()->id());
            $aux_cedula = $user->usuario;
            //$aux_cedula = "2450604";
        }
        //$aux_cedula = "6510971";
        $sql = "SELECT nm_empresa.emp_codh,nm_empresa.emp_nombre
                    FROM nm_empleados inner join nm_empresa
                    ON nm_empleados.emp_codh = nm_empresa.emp_codh
                    WHERE nm_empleados.emp_ced = $aux_cedula
                    group by nm_empresa.emp_codh;";

        $datas = DB::select($sql);
        return $datas;

    }

    public static function periodosnompersona($request){
        //dd($request);
        $aux_condfecha=" and cot_fdesde<='2018-08-15'  ";
		if ($request->cono_monetario=="0")
			$aux_condfecha=" and cot_fdesde>='2018-08-16' and cot_fdesde<='2021-09-30' ";
		if ($request->cono_monetario=="2")
			$aux_condfecha=" and cot_fdesde>='2021-10-01'  ";

        if(!empty($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $user = Usuario::findOrFail(auth()->id());
            $aux_cedula = $user->usuario;
            //$aux_cedula = "2450604";
        }
        //dd($aux_cedula);
        //$aux_cedula = "6510971";
        $sql = "SELECT nm_control.cot_tipo,nm_control.cot_numnom,mov_codcar,mov_codubica,
            nm_tiponomina.tmo_desc,
            DATE_FORMAT(cot_fdesde, '%d/%m/%Y') AS fdesde,
            DATE_FORMAT(cot_fhasta, '%d/%m/%Y') AS fhasta
            FROM nm_movnomtrab INNER JOIN nm_control
            ON nm_movnomtrab.mov_numnom = nm_control.cot_numnom
            INNER JOIN nm_movhist
            ON nm_movhist.mov_nummon = nm_movnomtrab.mov_numnom
            AND nm_movhist.emp_ced = nm_movnomtrab.mov_ced
            AND nm_movhist.emp_codh = nm_movnomtrab.emp_codh
            INNER JOIN nm_tiponomina
            ON nm_tiponomina.tmo_cod = nm_control.cot_tipo AND nm_tiponomina.gru_cod = nm_movnomtrab.gru_cod and nm_tiponomina.emp_codh = nm_movnomtrab.emp_codh
            where nm_movnomtrab.mov_ced = $aux_cedula 
            AND nm_movnomtrab.emp_codh = $request->emp_codh
            AND nm_control.emp_codh = $request->emp_codh
            $aux_condfecha
            group by nm_movnomtrab.mov_numnom 
            order by nm_control.cot_fdesde desc;";

        //dd($sql);
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
        //dd($sql);
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
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;

    }

}
