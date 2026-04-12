<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppMarcajeExento extends Model
{
    use SoftDeletes;

    protected $table = 'app_marcaje_exento';

    protected $fillable = [
        'emp_ced',
        'emp_codh',
        'tipo_exento',
        'descripcion',
    ];

    public static $tipos = [
        'T' => 'Teletrabajo',
        'J' => 'Jefe / Gerente',
        'O' => 'Otro',
    ];
}
