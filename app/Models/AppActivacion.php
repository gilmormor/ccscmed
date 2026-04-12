<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AppActivacion extends Model
{
    protected $table = 'app_activaciones';

    protected $fillable = [
        'emp_ced',
        'codigo',
        'dispositivo_id',
        'activado_en',
        'activo',
    ];

    protected $casts = [
        'activado_en' => 'datetime',
        'activo'      => 'boolean',
    ];

    /**
     * Genera un código alfanumérico único de 8 caracteres para una cédula.
     * Si ya existe un registro activo para esa cédula, lo retorna sin crear uno nuevo.
     */
    public static function generarParaCedula(string $cedula): self
    {
        $existente = self::where('emp_ced', $cedula)->where('activo', false)->whereNull('dispositivo_id')->first();
        if ($existente) {
            return $existente;
        }

        return self::create([
            'emp_ced' => $cedula,
            'codigo'  => strtoupper(Str::random(8)),
            'activo'  => false,
        ]);
    }
}
