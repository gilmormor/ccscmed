<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormaDeteccionNC extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "formadeteccionnc";
    protected $fillable = [
        'descripcion',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS noconformidad
    public function noconformidades()
    {
        return $this->hasMany(NoConformidad::class);
    }
}
