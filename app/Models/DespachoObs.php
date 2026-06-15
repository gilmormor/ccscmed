<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoObs extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "despachoobs";
    protected $fillable = [
        'nombre',
        'descripcion',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class);
    }
}
