<?php

namespace App;

use App\Models\ClienteBloqueado;
use App\Models\Despacho;
use App\Models\DespachoOrd;
use App\Models\NoConformidad;
use App\Models\Persona;
use App\Models\SolDespacho;
use App\Models\Sucursal;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $table = "usuario";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //Relacion uno a uno con persona
    public function persona()
    {
        return $this->hasOne(Persona::class);
    }

    //RELACION MUCHO A MUCHOS CUN SUCURSAL A TRAVES DE SUCURSAL_USUARIO
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_usuario')->withTimestamps();
    }

    //RELACION DE UNO A MUCHOS noconformidad
    public function noconformidad()
    {
        return $this->hasMany(NoConformidad::class);
    }

    //RELACION DE UNO A MUCHOS noconformidad con usuario quien modifico el paso 2
    public function noconformidad_mp2()
    {
        return $this->hasMany(NoConformidad::class,'usuario_idmp2');
    }

    //RELACION DE UNO A MUCHOS DespachoSol
    public function despachosols()
    {
        return $this->hasMany(DespachoSol::class);
    }
    //RELACION DE UNO A MUCHOS DespachoOrd
    public function despachoOrds()
    {
        return $this->hasMany(DespachoOrd::class);
    }

    //RELACION DE UNO A MUCHOS ClienteBloqueado
    public function clientebloqueados()
    {
        return $this->hasMany(ClienteBloqueado::class);
    }
    

}
