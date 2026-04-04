<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMenuRol extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('menu_rol')) {
            Schema::create('menu_rol', function (Blueprint $table) {
                $table->bigInteger('rol_id')->unsigned();
                $table->bigInteger('menu_id')->unsigned();
                $table->softDeletes();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
                $table->timestamps();

                $table->foreign('menu_id', 'fk_menurol_menu')->references('id')->on('menu')->onDelete('cascade');
                $table->foreign('rol_id', 'fk_menurol_rol')->references('id')->on('rol');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('menu_rol');
    }
}
