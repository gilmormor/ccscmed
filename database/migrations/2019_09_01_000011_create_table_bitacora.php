<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBitacora extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bitacora')) {
            Schema::create('bitacora', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('empresa_id')->unsigned()->comment('Codigo de Empresa');
                $table->bigInteger('menu_id')->unsigned()->nullable()->comment('Codigo de Menu');
                $table->bigInteger('usuario_id')->unsigned()->comment('Id Usuario');
                $table->string('codmov', 3)->comment('Codigo de Movimiento Ejm: IS=Inicio de Sesion, FS=Fin de Sesion.');
                $table->string('desc', 250)->comment('Descripcion de movimiento.');
                $table->string('nombretabla', 40)->nullable()->comment('Nombre de tabla donde se origino la accion');
                $table->bigInteger('tabla_id')->unsigned()->nullable()->comment('id de la tabla donde se genero la accion.');
                $table->string('ip', 16)->comment('Direccion IP');
                $table->string('dispositivo', 16)->nullable()->comment('Dispositivo');
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('empresa_id', 'fk_bitacora_empresa')->references('id')->on('empresa');
                $table->foreign('menu_id', 'fk_bitacora_menu')->references('id')->on('menu');
                $table->foreign('usuario_id', 'fk_bitacora_usuario')->references('id')->on('usuario');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bitacora');
    }
}
