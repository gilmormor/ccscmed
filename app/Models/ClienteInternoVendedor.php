<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\SerializaFechasLegacy;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteInternoVendedor extends Model
{
    use SerializaFechasLegacy;
    use SoftDeletes;
    protected $table = "clienteinterno_vendedor";
    protected $fillable = ['clienteinterno_id','vendedor_id','usuariodel_id'];

    
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function clienteinterno()
    {
        return $this->belongsTo(ClienteInterno::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
}
