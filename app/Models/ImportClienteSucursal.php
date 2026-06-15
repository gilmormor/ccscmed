<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportClienteSucursal extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "importclientesucursal";
    protected $fillable = ['cliente_id','sucursal_id','usuariodel_id'];
}
