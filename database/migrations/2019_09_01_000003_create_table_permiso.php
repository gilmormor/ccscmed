<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePermiso extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('permiso')) {
            Schema::create('permiso', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nombre', 50);
                $table->string('slug', 50);
                $table->softDeletes();
                $table->timestamps();
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('permiso');
    }
}
