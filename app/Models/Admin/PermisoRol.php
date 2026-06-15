<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermisoRol extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    //
}
