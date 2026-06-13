<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorLogoEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa', function (Blueprint $table) {
            if (!Schema::hasColumn('empresa', 'logo')) {
                $table->string('logo')->after('moneda_id')->nullable();
            }
            if (!Schema::hasColumn('empresa', 'color')) {
                $table->string('color')->after('logo')->nullable();
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
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn(['color', 'logo']);
        });
    }
}
