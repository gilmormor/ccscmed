<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NmGrupo extends Model
{
    protected $table      = 'nm_grupo';
    protected $primaryKey = 'gru_cod';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps = false;

    public function empresas()
    {
        return $this->hasMany(NmEmpresa::class, 'gru_cod', 'gru_cod');
    }
}
