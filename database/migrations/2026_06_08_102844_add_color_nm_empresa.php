<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorNmEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            if (!Schema::hasColumn('nm_empresa', 'color')) {
                $table->string('color', 20);
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
            $table->dropColumn('color');
        });
    }
}
