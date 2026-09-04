<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Catálogo de especialidades médicas.
 *
 * Alimenta la constancia de honorarios profesionales, donde va la especialidad
 * concreta ("ANESTESIOLOGO") y no el cargo genérico de nm_cargos ("MEDICO").
 */
class NmEspecialidad extends Model
{
    use SoftDeletes;

    protected $table = 'nm_especialidad';

    protected $fillable = ['id', 'nombre', 'emp_codh', 'gru_cod'];

    /** El id lo trae VFP8 junto con el nombre; no lo genera MySQL. */
    public $incrementing = false;

    /** Médicos que tienen esta especialidad. */
    public function empleados()
    {
        return $this->belongsToMany(
            NmEmpleado::class,
            'nm_empleadoespecialidad',
            'esp_id',
            'emp_id',
            'id',
            'id'
        )->withTimestamps();
    }
}
