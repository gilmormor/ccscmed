<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoCategoria extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "grupocategoria";
    protected $fillable = ['gruc_nombre','gruc_descripcion','usuariodel_id'];

    //RELACION DE UNO A VARIOS
    public function categoriaprods()
    {
        return $this->hasMany(CategoriaProd::class);
    }

}
