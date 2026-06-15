<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "rol";
    protected $fillable = ['nombre']; 
    protected $guarded = ['id']; 
}
