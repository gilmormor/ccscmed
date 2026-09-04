<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Logo apaisado del membrete, para la constancia de honorarios.
 *
 * empresa.logo lo comparten el manual de usuario y el reporte de médicos, que
 * lo insertan con un ancho fijo de ~100 px pensado para el símbolo cuadrado
 * (ccsc2.png). Cambiar ese campo al logo apaisado lo dejaría diminuto en esos
 * dos documentos, así que la constancia lleva el suyo aparte.
 *
 * Si queda vacío, la constancia cae de vuelta a empresa.logo.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('empresa', 'logo_membrete')) {
            Schema::table('empresa', function (Blueprint $t) {
                $t->string('logo_membrete', 191)->nullable()->after('logo');
            });
        }

        DB::table('empresa')->whereNotNull('id')->update([
            'logo_membrete' => DB::raw("COALESCE(NULLIF(TRIM(logo_membrete),''), 'ccsc.jpg')"),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('empresa', 'logo_membrete')) {
            Schema::table('empresa', function (Blueprint $t) {
                $t->dropColumn('logo_membrete');
            });
        }
    }
};
