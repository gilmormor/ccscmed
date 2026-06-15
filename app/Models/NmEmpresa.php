<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\SerializaFechasLegacy;
class NmEmpresa extends Model
{
    use SerializaFechasLegacy;
    protected $table      = 'nm_empresa';
    protected $primaryKey = 'emp_codh';
    public    $keyType    = 'string';
    public    $incrementing = false;
    // La tabla nm_* no tiene timestamps propios de Laravel
    public    $timestamps = false;

    protected $fillable = [
        'marc_lat',
        'marc_lng',
        'marc_radio',
        'marc_tolerancia',
        'marc_valida_perimetro',
    ];

    protected $casts = [
        'marc_lat'              => 'float',
        'marc_lng'              => 'float',
        'marc_radio'            => 'integer',
        'marc_tolerancia'       => 'integer',
        'marc_valida_perimetro' => 'integer',
    ];

    public function grupo()
    {
        return $this->belongsTo(NmGrupo::class, 'gru_cod', 'gru_cod');
    }
}
