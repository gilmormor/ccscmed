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
            if (!Schema::hasColumn('nm_empresa', 'ciudad')) {
                $table->string('ciudad')->comment('Ciudad donde se encuentra la empresa')->after('emp_direc');
            }
            if (Schema::hasColumn('nm_empresa', 'emp_telf')) {
                $table->string('emp_telf', 100)->change();
            }
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
