<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppMarcajeExentoTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('app_marcaje_exento')) return;

        Schema::create('app_marcaje_exento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_ced', 20);
            $table->string('emp_codh', 10)->nullable()->comment('Si es null aplica a todas las empresas');
            // T=Teletrabajo, J=Jefe/Gerente, O=Otro
            $table->char('tipo_exento', 1)->default('O')->comment('T=Teletrabajo, J=Jefe/Gerente, O=Otro');
            $table->string('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['emp_ced', 'emp_codh']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_marcaje_exento');
    }
}
