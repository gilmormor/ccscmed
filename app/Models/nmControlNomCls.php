<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class nmControlNomCls extends Model
{
    use SerializaFechasLegacy;
    protected $table = "nm_controlnomcls";
    protected $fillable = [
        'nm_control_id',
        'ccl_nronomciclos',
    ];
    
    //RELACION INVERSA nm_control
    public function nmcontrol()
    {
        return $this->belongsTo(nmControl::class);
    }

}
