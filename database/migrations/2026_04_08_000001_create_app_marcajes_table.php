<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppMarcajesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('app_marcajes')) return;

        Schema::create('app_marcajes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_ced', 20);
            $table->string('emp_codh', 10)->nullable();
            $table->char('tipo', 1);          // 'E' = Entrada, 'S' = Salida
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('latitud',  10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->string('dispositivo_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['emp_ced', 'fecha']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_marcajes');
    }
}
