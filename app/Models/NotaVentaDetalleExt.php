<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class NotaVentaDetalleExt extends Model
{
    use SerializaFechasLegacy;
    protected $table = "notaventadetalleext";
    protected $fillable = [
        'notaventadetalle_id',
        'cantext',
    ];
    
    //RELACION INVERSA NotaVenta
    public function notaventadetalle()
    {
        return $this->belongsTo(NotaVentaDetalle::class);
    }    
}
