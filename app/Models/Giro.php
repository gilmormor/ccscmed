<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Giro extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "giro";
    protected $fillable = ['nombre','descripcion','usuariodel_id'];

    //RELACION DE UNO A VARIOS
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    //RELACION DE UNO A VARIOS
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }
}
