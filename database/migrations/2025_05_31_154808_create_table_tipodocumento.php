<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTipodocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipodocumento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('desc',20)->nullable()->comment('Descripcion.');
            $table->string('tipodoc',4)->nullable()->comment('Codigo del tipo de documento.');
            $table->integer('orden')->nullable()->comment('Orden')->unsigned();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipodocumento');
    }
}
