<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMenu extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('menu')) {
            Schema::create('menu', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('menu_id')->default(0);
                $table->string('nombre', 50);
                $table->string('url', 100);
                $table->unsignedInteger('orden')->default(0);
                $table->string('icono', 50)->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
