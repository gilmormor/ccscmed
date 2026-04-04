<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePermisoRol extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('permiso_rol')) {
            Schema::create('permiso_rol', function (Blueprint $table) {
                $table->bigInteger('rol_id')->unsigned();
                $table->bigInteger('permiso_id')->unsigned();
                $table->softDeletes();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
                $table->timestamps();

                $table->index('rol_id', 'fk_permisorol_rol');
                $table->index('permiso_id', 'fk_permisorol_permiso');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('permiso_rol');
    }
}
