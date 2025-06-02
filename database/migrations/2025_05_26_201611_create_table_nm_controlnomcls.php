<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNmControlnomcls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nm_controlnomcls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nm_control_id');
            $table->foreign('nm_control_id','fk_nm_control_nm_controlnomcls')->references('id')->on('nm_control')->onDelete('cascade')->onUpdate('restrict');
            $table->integer('ccl_nronomciclos')->comment('Numero de nomina Ciclos')->unsigned();
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
        Schema::dropIfExists('nm_controlnomcls');
    }
}
