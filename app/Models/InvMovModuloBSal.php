<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvMovModuloBSal extends Model
{
    protected $table = "invmovmodulobsal";
    protected $fillable = [
        'invmovmodulo_id',
        'invbodega_id'
    ];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function invmovmodulo()
    {
        return $this->belongsTo(CategoriaProd::class,'invmovmodulo_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function invbodega()
    {
        return $this->belongsTo(InvBodega::class,'invbodega_id');
    }
}
