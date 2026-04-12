<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerimetroToNmEmpresa extends Migration
{
    public function up()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            if (!Schema::hasColumn('nm_empresa', 'marc_lat')) {
                $table->decimal('marc_lat', 10, 8)->nullable()->comment('Latitud centro perímetro marcaje');
            }
            if (!Schema::hasColumn('nm_empresa', 'marc_lng')) {
                $table->decimal('marc_lng', 11, 8)->nullable()->comment('Longitud centro perímetro marcaje');
            }
            if (!Schema::hasColumn('nm_empresa', 'marc_radio')) {
                $table->unsignedSmallInteger('marc_radio')->default(100)->comment('Radio perímetro en metros');
            }
            if (!Schema::hasColumn('nm_empresa', 'marc_tolerancia')) {
                $table->unsignedSmallInteger('marc_tolerancia')->default(50)->comment('Tolerancia extra en metros');
            }
            if (!Schema::hasColumn('nm_empresa', 'marc_valida_perimetro')) {
                $table->tinyInteger('marc_valida_perimetro')->default(0)->comment('1=valida perímetro, 0=no valida');
            }
        });
    }

    public function down()
    {
        Schema::table('nm_empresa', function (Blueprint $table) {
            $table->dropColumn([
                'marc_lat', 'marc_lng', 'marc_radio',
                'marc_tolerancia', 'marc_valida_perimetro',
            ]);
        });
    }
}
