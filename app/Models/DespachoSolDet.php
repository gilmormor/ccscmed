<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSolDet extends Model
{
    use SoftDeletes;
    protected $table = "despachosoldet";
    protected $fillable = [
        'despachosol_id',
        'notaventadetalle_id',
        'cantsoldesp',
        'cantsoldespdev',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class,'despachosoldet_id');
    }
    //RELACION INVERSA DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
    //RELACION DE UNO A MUCHOS despachosoldet_invbodegaproducto
    public function despachosoldet_invbodegaproductos()
    {
        return $this->hasMany(DespachoSolDet_InvBodegaProducto::class,'despachosoldet_id');
    }
    //Relacion uno a uno con despachosoldet_invbodegaproducto
    public function despachosoldet_invbodegaproducto()
    {
        return $this->hasOne(DespachoSolDet_InvBodegaProducto::class,"despachosoldet_id");
    }
    
}
