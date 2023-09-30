<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtComplementonomprodAcuerdotecnico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->string('at_complementonomprod',20)->nullable()->comment('Complemento nombre de producto para la factura.')->after('at_desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->dropColumn('at_complementonomprod');
        });
    }
}
