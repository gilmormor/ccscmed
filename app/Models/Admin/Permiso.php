<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = 'permiso';
    protected $fillable = ['nombre','slug'];
    protected $guarded = ['id'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'permiso_rol', 'permiso_id', 'rol_id')->withTimestamps();
    }
}
