<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\DB;

class nmControl extends Model
{
    protected $table = "nm_control";
    protected $fillable = [
        'cot_tipo',
        'cot_fdesde',
        'cot_fhasta',
        'cot_numnom',
        'emp_codh',
        'gru_cod',
        'cot_fecing',
        'cot_valordolar',
        'cot_stasendemail'
    ];

    //RELACION DE UNO A MUCHOS nmControlNomCls
    public function nmcontrolnomclss()
    {
        return $this->hasMany(nmControlNomCls::class);
    }

    //RELACION DE UNO A MUCHOS nmHonPacienteDet
    public function nmhonpacientedets()
    {
        return $this->hasMany(nmHonPacienteDet::class);
    }

    public static function generarPDFRelHon($request){
        if(isset($request->emp_ced)){
            $aux_cedula = $request->emp_ced;
        }else{
            $usuario = Usuario::findOrFail(auth()->id());
            $aux_cedula = $usuario->usuario;
            //$aux_cedula = "2450604";
        }
        $nm_control = nmControl::findOrFail($request->nmcontrol_id);
        $empresa = Empresa::orderBy('id')->get();
        $sql = "SELECT *
            FROM nm_empleados 
            WHERE emp_ced = $aux_cedula;";
        $datas = DB::select($sql);

        $sql = "SELECT nm_honpacientedet.*,tipodocumento.desc as tipdoc_desc,
                    nm_controlnomcls.ccl_nronomciclos
                    FROM nm_control INNER JOIN nm_controlnomcls
                    ON nm_control.id = nm_controlnomcls.nm_control_id
                    INNER JOIN nm_honpacientedet
                    ON nm_controlnomcls.id = nm_honpacientedet.nm_controlnomcls_id
                    LEFT JOIN tipodocumento
                    ON nm_honpacientedet.tipo_documento = tipodocumento.tipodoc
                    WHERE nm_control.id = $request->nmcontrol_id
                    AND nm_honpacientedet.emp_ced = $aux_cedula
                    ORDER BY tipodocumento.orden,factura;";
        $pacdets = DB::select($sql);        
        if(count($datas) > 0){
            $nm_empleado = $datas[0];
        }
        if($pacdets){
            $sql = "SELECT nm_controlnomcls.ccl_nronomciclos
                        FROM nm_control INNER JOIN nm_controlnomcls
                        ON nm_control.id = nm_controlnomcls.nm_control_id
                        INNER JOIN nm_honpacientedet
                        ON nm_controlnomcls.id = nm_honpacientedet.nm_controlnomcls_id
                        LEFT JOIN tipodocumento
                        ON nm_honpacientedet.tipo_documento = tipodocumento.tipodoc
                        WHERE nm_control.id = $request->nmcontrol_id
                        AND nm_honpacientedet.emp_ced = $aux_cedula
                        GROUP BY nm_controlnomcls.ccl_nronomciclos;";
            $nronomciclos = DB::select($sql);
            // Convertir a array de números
            $valores = array_map(function($item) {
                return $item->ccl_nronomciclos;
            }, $nronomciclos);

            // Convertir a cadena separada por comas
            $nroNominaCiclos = implode(',', $valores);


            // Convertimos todo a mayúsculas y eliminamos espacios extra
            $apellido = strtoupper(trim($nm_empleado->emp_ape));
            $nombre = strtoupper(trim($nm_empleado->emp_nom));

            $primer_apellido = explode(' ', $apellido)[0]; // GARCIA
            $primer_nombre = explode(' ', $nombre)[0];     // ANNA

            $primer_apellido = ucwords(strtolower($primer_apellido));
            $primer_nombre = ucwords(strtolower($primer_nombre));

            // Cedula con ceros a la izquierda (8 dígitos)
            $cedula_formateada = str_pad($nm_empleado->emp_ced, 8, '0', STR_PAD_LEFT); // 00504431

            // Concatenamos todo
            $nomach = 'RelPacHon_' . implode('_', $nm_control->nmcontrolnomclss->pluck('ccl_nronomciclos')->toArray()) . '_' . $cedula_formateada . '_' . $primer_apellido . $primer_nombre;

            $pdf = SnappyPdf::loadView('reportrechon.relhon', compact(
                'nm_control', 'nm_empleado', 'empresa', 'usuario', 'request', 'pacdets', 'nroNominaCiclos'
            ));
            $pdf->setPaper('a4', 'landscape');
            return [
                'pdf' => $pdf,
                'nomach' => $nomach
            ];
            return $pdf;
        }else{
            return [
                'id' => 0,
                'title'=>'Error',
                'mensaje'=>'Ningún dato disponible en esta consulta.',
                'tipo_alert' => 'error'
            ];
            //dd('Ningún dato disponible en esta consulta.');
        } 

    }

}
