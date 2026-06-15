<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class FacturaGuiaDesp extends Model
{
    use SerializaFechasLegacy;
    protected $table = "factura_guiadesp";
    protected $fillable = ['factura_id','guiadesp_id'];

    //RELACION INVERSA A Cliente
    public function factura()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA A GuiaDesp
    public function guiadesp()
    {
        return $this->belongsTo(GuiaDesp::class);
    }

}
