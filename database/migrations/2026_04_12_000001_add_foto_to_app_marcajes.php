<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoToAppMarcajes extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('app_marcajes', 'foto')) return;

        Schema::table('app_marcajes', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('dispositivo_id');
        });
    }

    public function down()
    {
        Schema::table('app_marcajes', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
}
