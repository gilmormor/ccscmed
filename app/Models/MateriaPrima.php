<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class MateriaPrima extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "materiaprima";
    protected $fillable = [
        'nombre',
        'desc',
        'descfact',
        'pe',
        'usuariodel_id'
    ];

    public function acuerdotecnicotemps()
    {
        return $this->hasMany(AcuerdoTecnicoTemp::class);
    }
    public function acuerdotecnicos()
    {
        return $this->hasMany(AcuerdoTecnico::class);
    }

}
