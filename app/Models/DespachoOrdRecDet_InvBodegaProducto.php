<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdRecDet_InvBodegaProducto extends Model
{
    use SoftDeletes;
    protected $table = "despachoordrecdet_invbodegaproducto";
    protected $fillable = [
        'despachoordrecdet_id',
        'invbodegaproducto_id',
        'cant',
        'cantkg'
    ];

    //RELACION INVERSA DespachoOrdDet
    public function despachoordrecdet()
    {
        return $this->belongsTo(DespachoOrdRecDet::class);
    }

    //RELACION INVERSA InvBodegaProducto
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }
    /*
    //RELACION DE UNO A MUCHOS invmovdet_bodsoldesps
    public function invmovdet_bodsoldesps()
    {
        return $this->hasMany(InvMovDet_BodSolDesp::class,'despachosoldet_invbodegaproducto_id');
    }*/
    
}
