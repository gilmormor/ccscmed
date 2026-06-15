<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "libro";
    protected $fillable = ['titulo','isbn','autor','cantidad','editorial','foto']; 
    protected $guarded = ['id']; 
}
