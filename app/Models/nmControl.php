<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
