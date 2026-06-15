<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "cargo";
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
    //RELACION UNO A MUCHOS PERSONA
    public function personals()
    {
        return $this->hasMany(Persona::class);
    }
}
