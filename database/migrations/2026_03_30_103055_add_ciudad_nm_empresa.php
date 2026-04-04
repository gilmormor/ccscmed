<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCiudadNmEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            $table->string('ciudad')->comment('Ciudad donde se encuentra la empresa')->after('emp_direc');
            $table->string('emp_telf', 100)->change(); // nuevo tamaño
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            $table->dropColumn('ciudad');
            $table->dropColumn('emp_telf');
        });
    }
}
