<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRol extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('rol')) {
            Schema::create('rol', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nombre', 50)->unique();
                $table->softDeletes();
                $table->timestamps();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('rol');
    }
}
