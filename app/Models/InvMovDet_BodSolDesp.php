<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class InvMovDet_BodSolDesp extends Model
{
    use SerializaFechasLegacy;
    protected $table = "invmovdet_bodsoldesp";
    protected $fillable = [
        'invmovdet_id',
        'despachosoldet_invbodegaproducto_id',
    ];

    //RELACION INVERSA InvMov
    public function invmovdet()
    {
        return $this->belongsTo(InvMovDet::class);
    }

    public function despachosoldet_invbodegaproducto()
    {
        return $this->belongsTo(DespachoSolDet_InvBodegaProducto::class);
    }

}
