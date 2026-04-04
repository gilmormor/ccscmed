<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsuario extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('usuario')) {
            Schema::create('usuario', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('usuario', 50);
                $table->string('password', 100);
                $table->string('pass', 50);
                $table->string('nombre', 50);
                $table->string('email', 100);
                $table->string('foto', 100)->nullable()->comment('Foto de Usuario');
                $table->timestamps();
                $table->softDeletes();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
