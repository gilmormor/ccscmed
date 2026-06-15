<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class InvMovDet_BodOrdDesp extends Model
{
    use SerializaFechasLegacy;
    protected $table = "invmovdet_bodorddesp";
    protected $fillable = [
        'invmovdet_id',
        'despachoorddet_invbodegaproducto_id',
    ];

    //RELACION INVERSA InvMov
    public function invmovdet()
    {
        return $this->belongsTo(InvMovDet::class);
    }

    public function despachoorddet_invbodegaproducto()
    {
        return $this->belongsTo(DespachoOrdDet_InvBodegaProducto::class);
    }

}
