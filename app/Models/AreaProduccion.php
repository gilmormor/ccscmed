<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaProduccion extends Model
{
    use SoftDeletes;
    protected $table = "areaproduccion";
    protected $fillable = [
        'nombre',
        'descripcion',
        'stapromkg',
        'usuariodel_id'
    ];
    //RELACION UNO A MUCHOS CATEGORIAPROD
    public function categoriaprods()
    {
        return $this->hasMany(CategoriaProd::class);
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'areaproduccionsuc','areaproduccion_id')->withTimestamps();
    }
    //RELACION UNO A MUCHOS AreaProduccionSuc
    public function areaproduccionsucs()
    {
        return $this->hasMany(AreaProduccionSuc::class,'areaproduccion_id');
    }

}