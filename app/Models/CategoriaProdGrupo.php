<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProdGrupo extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "categoriaprodgrupo";
    protected $fillable = [
        'nombre',
        'desc',
        'comisionventas'
    ];

    //RELACION UNO A MUCHOS CategoriaProd
    public function categoriaprods()
    {
        return $this->hasMany(CategoriaProd::class);
    }
}
