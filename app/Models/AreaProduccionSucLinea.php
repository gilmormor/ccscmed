<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class AreaProduccionSucLinea extends Model
{
    use SerializaFechasLegacy;
    protected $table = "areaproduccionsuclinea";
    protected $fillable = [
        'areaproduccionsuc_id',
        'nombre',
        'desc',
        'obs',
        'activo'
    ];
    //Relacion inversa a Sucursal
    public function areaproduccionsuc()
    {
        return $this->belongsTo(AreaProduccionSuc::class);
    }
    //RELACION UNO A MUCHOS PesajeDet
    public function pesajedets()
    {
        return $this->hasMany(PesajeDet::class);
    }
}
