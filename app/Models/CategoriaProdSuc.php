<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProdSuc extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "categoriaprodsuc";
    protected $fillable = ['sucursal_id','categoriaprod_id','usuariodel_id'];

    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class,'categoriaprod_id');
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class,'sucursal_id');
    }

}
