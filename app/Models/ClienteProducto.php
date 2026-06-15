<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteProducto extends Model
{
    use SerializaFechasLegacy;
    protected $table = "cliente_producto";
    protected $fillable = ['cliente_id','producto_id'];

    //RELACION INVERSA A Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA A Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
