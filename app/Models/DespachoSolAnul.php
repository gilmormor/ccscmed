<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSolAnul extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "despachosolanul";
    protected $fillable = [
        'despachosol_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
}
