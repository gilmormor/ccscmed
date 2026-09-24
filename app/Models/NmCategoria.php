<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Catálogo de categorías de empleados. Relación 1:N con NmEmpleado a través
 * de nm_empleados.categoria_id.
 */
class NmCategoria extends Model
{
    use SoftDeletes;

    protected $table = 'nm_categoria';

    protected $fillable = ['id', 'cat_descrip'];

    /** El id lo trae VFP8; no lo genera MySQL. */
    public $incrementing = false;

    /** Empleados que pertenecen a esta categoría. */
    public function empleados()
    {
        return $this->hasMany(NmEmpleado::class, 'categoria_id', 'id');
    }
}
