<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrdDet extends Model
{
    use SoftDeletes;
    protected $table = "despachoorddet";
    protected $fillable = [
        'despachoord_id',
        'despachosoldet_id',
        'notaventadetalle_id',
        'obs',
        'cantdesp',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
    }

    //RELACION INVERSA DespachoSolDet
    public function despachosoldet()
    {
        return $this->belongsTo(DespachoSolDet::class);
    }
  
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }

    //RELACION DE UNO A MUCHOS DespachoOrdRecDet
    public function despachoordrecdets()
    {
        return $this->hasMany(DespachoOrdRecDet::class,'despachoorddet_id');
    }

    //RELACION DE UNO A MUCHOS despachoorddet_invbodegaproducto
    public function despachoorddet_invbodegaproductos()
    {
        return $this->hasMany(DespachoOrdDet_InvBodegaProducto::class,'despachoorddet_id');
    }

    //Relacion uno a uno con guiadespdet
    public function guiadespdet()
    {
        return $this->hasOne(GuiaDespDet::class,"despachoorddet_id");
    }
}
