<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppActivacionesTable extends Migration
{
    public function up()
    {
        Schema::create('app_activaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_ced', 20)->index();
            $table->string('codigo', 10);
            $table->string('dispositivo_id', 255)->nullable();
            $table->timestamp('activado_en')->nullable();
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_activaciones');
    }
}
