<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionPaisNmEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            if (!Schema::hasColumn('nm_empresa', 'region')) {
                $table->string('region')->comment('Región empresa')->after('ciudad');
            }
            if (!Schema::hasColumn('nm_empresa', 'pais')) {
                $table->string('pais')->comment('País empresa')->after('region');
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
            $table->dropColumn('region');
            $table->dropColumn('pais');
        });
    }
}
