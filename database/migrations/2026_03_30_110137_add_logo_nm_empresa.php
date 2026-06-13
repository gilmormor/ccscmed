<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoNmEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            if (!Schema::hasColumn('nm_empresa', 'logo')) {
                $table->string('logo')->comment('Logo empresa')->after('emp_telf');
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
            $table->dropColumn('logo');
        });
    }
}
