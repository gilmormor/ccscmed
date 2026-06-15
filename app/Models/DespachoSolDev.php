<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class DespachoSolDev extends Model
{
    use SerializaFechasLegacy;
    protected $table = "despachosoldev";
    protected $fillable = 
    [
        'despachosol_id',
        'usuario_id',
        'obs',
        'status'
    ];

    //Relacion inversa a DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
}
