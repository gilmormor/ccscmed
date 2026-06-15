<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class DespachoSolDTE extends Model
{
    use SerializaFechasLegacy;
    protected $table = "despachosoldte";
    protected $fillable = [
        'despachosol_id',
        'dte_id'
    ];

    //Relacion inversa a despachosol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }
    //RELACION INVERSA DTE
    public function dte()
    {
        return $this->belongsTo(Dte::class);
    }
}
