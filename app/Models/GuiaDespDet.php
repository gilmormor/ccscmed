<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDespDet extends Model
{
    use SoftDeletes;
    protected $table = "guiadespdet";
    protected $fillable = [
        'guiadesp_id',
        'despachoorddet_id',
        'notaventadetalle_id',
        'producto_id',
        'nrolindet',
        'vlrcodigo',
        'nmbitem',
        'dscitem',
        'qtyitem',
        'unmditem',
        'unidadmedida_id',
        'prcitem',
        'montoitem',
        'obsdet',
        'itemkg',
        'usuariodel_id'
    ];

    //RELACION INVERSA DespachoOrd
    public function guiadesp()
    {
        return $this->belongsTo(GuiaDesp::class);
    }

    //RELACION INVERSA DespachoOrdDet
    public function despachoorddet()
    {
        return $this->belongsTo(DespachoOrdDet::class);
    }
  
    //RELACION INVERSA NotaVentaDetalle
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }
/*
    //RELACION DE UNO A MUCHOS DespachoOrdRecDet
    public function despachoordrecdets()
    {
        return $this->hasMany(DespachoOrdRecDet::class,'despachoorddet_id');
    }
*/
}
