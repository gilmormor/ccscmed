<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsuarioRol extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('usuario_rol')) {
            Schema::create('usuario_rol', function (Blueprint $table) {
                $table->bigInteger('rol_id')->unsigned();
                $table->bigInteger('usuario_id')->unsigned();
                $table->timestamps();
                $table->softDeletes();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');

                $table->index('rol_id', 'fk_usuariorol_rol');
                $table->index('usuario_id', 'fk_usuariorol_usuario');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('usuario_rol');
    }
}
