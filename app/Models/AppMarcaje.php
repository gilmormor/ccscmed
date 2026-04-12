<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppMarcaje extends Model
{
    use SoftDeletes;

    protected $table = 'app_marcajes';

    protected $fillable = [
        'emp_ced',
        'emp_codh',
        'tipo',
        'fecha',
        'hora',
        'latitud',
        'longitud',
        'dispositivo_id',
        'foto',
    ];

    protected $casts = [
        'latitud'  => 'float',
        'longitud' => 'float',
        'fecha'    => 'date',
    ];
}
