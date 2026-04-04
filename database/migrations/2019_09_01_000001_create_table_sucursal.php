<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSucursal extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sucursal')) {
            Schema::create('sucursal', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nombre', 100);
                $table->string('abrev', 5)->comment('Abreviatura de nombre.');
                $table->bigInteger('region_id')->unsigned();
                $table->bigInteger('provincia_id')->unsigned();
                $table->bigInteger('comuna_id')->unsigned();
                $table->string('direccion', 250);
                $table->string('telefono1', 14);
                $table->string('telefono2', 14)->nullable();
                $table->string('telefono3', 14)->nullable();
                $table->string('email', 50)->nullable()->comment('Email');
                $table->string('staaprobnv', 10)->nullable()->default('0');
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sucursal');
    }
}
