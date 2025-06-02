<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class nmHonPacienteDet extends Model
{
    protected $table = "nm_honpacientedet";
    protected $fillable = [
        'nm_controlnomcls_id',
        'tipo_documento',
        'factura',
        'fecha_fact',
        'tipo_acree',
        'cod_acreed',
        'emp_ced',
        'nom_paciente',
        'concepto',
        'honorario',
        'pagado',
        'dscto',
        'pago_actual',
        'moneda_nac',
        'otra_moneda_bs',
        'tasa_cambio',
        'monto_otra_moneda'
    ];
    
    //RELACION INVERSA nm_control
    public function nmcontrol()
    {
        return $this->belongsTo(nmControl::class);
    }
}
