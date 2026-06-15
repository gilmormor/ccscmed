<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentroEconomico extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "centroeconomico";
    protected $fillable = [
        'sucursal_id',
        'nombre',
        'desc',
        'usuario_id',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    //RELACION UNO A MUCHOS PERSONA
    public function guiadesps()
    {
        return $this->hasMany(GuiaDesp::class);
    }
    
    
}