<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "empresa";
    protected $fillable = [
        'rut',
        'nombre',
        'razonsocial',
        'giro',
        'iva',
        'sucursal_id',
        'acteco',
        'moneda_id',
        'usuariodel_id'
    ];

    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a Moneda
    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }
    
}
