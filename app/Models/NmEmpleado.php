<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NmEmpleado extends Model
{
    use SerializaFechasLegacy;
    protected $table = "nm_empleados";
    protected $fillable = [
        'emp_nac',
        'emp_ced',
        'emp_cod',
        'emp_ape',
        'emp_nom',
        'emp_sexo',
        'emp_rif',
        'emp_email',
        'emp_usu',
        'emp_contra',
        'emp_stasit',
        'emp_codh',
        'gru_cod',
        'emp_staact',
        'emp_horaconex',
        'emp_fecing',
        'emp_fecegre',
        'emp_carcod',
        'emp_sueldo',
        'emp_salint'
    ];

    /**
     * El id no es autoincremental: lo trae VFP8 desde nm_empleado.emp_id del
     * sistema local. Sin esto Eloquent intentaría dejar que MySQL lo genere.
     */
    public $incrementing = false;

    /** Especialidades médicas asignadas (para la constancia de honorarios). */
    public function especialidades()
    {
        return $this->belongsToMany(
            NmEspecialidad::class,
            'nm_empleadoespecialidad',
            'emp_id',
            'esp_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public static function consultaempleado($vendedor_id = '0',$sucursal_id = false){
        $users = Usuario::findOrFail(auth()->id());
        if($sucursal_id){
            $sucurArray = [$sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);
        $vendedor_idCond = "true";

        $sql = "SELECT emp_ced,concat(emp_nom, ' ' ,emp_ape)
                FROM nm_empleados
                ORDER BY emp_ape,emp_nom;";

        $datas = DB::select($sql);
        return $datas;
    }
}
