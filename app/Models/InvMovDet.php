<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMovDet extends Model
{
    use SoftDeletes;
    protected $table = "invmovdet";
    protected $fillable = [
        'invmov_id',
        'invbodegaproducto_id',
        'cant',
        'cantgrupo',
        'cantxgrupo',
        'peso',
        'cantkg',
        'unidadmedida_id',
        'producto_id',
        'invbodega_id',
        'sucursal_id',
        'invmovtipo_id',
        'usuariodel_id'
    ];

    //RELACION INVERSA InvMov
    public function invmov()
    {
        return $this->belongsTo(InvMov::class);
    }

    //RELACION INVERSA InvMovTipo
    public function invmovtipo()
    {
        return $this->belongsTo(InvMovTipo::class);
    }
    //RELACION INVERSA invbodegaproducto
    public function invbodegaproducto()
    {
        return $this->belongsTo(InvBodegaProducto::class);
    }
    
    //RELACION INVERSA InvModulo
    public function invmovmodulo()
    {
        return $this->belongsTo(InvMovModulo::class);
    }
    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    //Relacion uno a uno con InvMovDet_BodSolDesp
    public function invmovdet_bodsoldesp()
    {
        return $this->hasOne(InvMovDet_BodSolDesp::class);
    }

    //Relacion uno a uno con InvMovDet_BodOrdDesp
    public function invmovdet_bodorddesp()
    {
        return $this->hasOne(InvMovDet_BodOrdDesp::class);
    }
    
}
