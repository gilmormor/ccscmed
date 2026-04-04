<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSucursalUsuario extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sucursal_usuario')) {
            Schema::create('sucursal_usuario', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('sucursal_id')->unsigned();
                $table->bigInteger('usuario_id')->unsigned();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('sucursal_id', 'fk_sucursalusuario_sucursal')->references('id')->on('sucursal');
                $table->foreign('usuario_id', 'fk_sucursalusuario_usuario')->references('id')->on('usuario');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sucursal_usuario');
    }
}
