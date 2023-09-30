<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadMedida extends Model
{
    use SoftDeletes;
    protected $table = "unidadmedida";
    protected $fillable = [
        'nombre',
        'descripcion',
        'mostrarfact',
        'agrupado'
    ];

    public function acuerdotectempanchos()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'anchoum_id');
    }
    public function acuerdotectemplargo()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'largoum_id');
    }
    public function acuerdotectempfuelle()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'fuelleum_id');
    }
    public function acuerdotectempespesor()
    {
        return $this->hasMany(AcuerdoTecTemp::class,'espesorum_id');
    }
    //RELACION UNO A MUCHOS CotizacionDetalle
    public function cotizaciondetalles()
    {
        return $this->hasMany(CotizacionDetalle::class);
    }

    //RELACION UNO A MUCHOS EstadisticaVenta
    public function estadisticaventas()
    {
        return $this->hasMany(EstadisticaVenta::class);
    }
    //RELACION UNO A MUCHOS EstadisticaVentaGI Guia Interna
    public function estadisticaventagis()
    {
        return $this->hasMany(EstadisticaVentaGI::class);
    }

    //RELACION UNO A MUCHOS InvmovDet
    public function invmovdets()
    {
        return $this->hasMany(InvMovDet::class);
    }

}
