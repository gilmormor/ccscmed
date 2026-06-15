<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class DteAnul extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "dteanul";
    protected $fillable = [
        'dte_id',
        'obs',
        'motanul_id',
        'moddevgiadesp_id',
        'usuario_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
}
