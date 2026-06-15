<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppMarcaje extends Model
{
    use SerializaFechasLegacy;
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
    ];

    protected $casts = [
        'latitud'  => 'float',
        'longitud' => 'float',
        'fecha'    => 'date',
    ];
}
