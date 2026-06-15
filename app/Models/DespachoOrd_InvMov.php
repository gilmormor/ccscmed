<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrd_InvMov extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "despachoord_invmov";
    protected $fillable = [
        'despachoord_id',
        'invmov_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function despachoor()
    {
        return $this->belongsTo(DespachoOrd::class);
    }
    //RELACION INVERSA InvMov
    public function invmov()
    {
        return $this->belongsTo(InvMov::class);
    }
}
