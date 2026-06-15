<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "area";
    protected $fillable = [
                            'nombre',
                            'abrev',
                            'descripcion',
                            'usuariodel_id'
                        ];
                        
    //RELACION DE UNO A MUCHOS sucursal_area
    public function sucursalareas()
    {
        return $this->hasMany(SucursalArea::class);
    }
}
