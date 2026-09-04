<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domicilio fiscal completo para el encabezado de la constancia.
 *
 * nm_empresa.emp_direc viene de VFP8 truncado a "AVDA LAS PILAS URB SANTA INES":
 * le falta "EDIF. CENTRO CLÍNICO, PISO 3", que sí aparece en el membrete
 * aprobado. Como ese campo lo recarga el sistema local en cada cierre, la
 * dirección del membrete vive en `empresa`, del lado de Laravel.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('empresa', 'direccion')) {
            Schema::table('empresa', function (Blueprint $t) {
                $t->string('direccion', 200)->nullable()->after('giro');
            });
        }

        DB::table('empresa')->whereNotNull('id')->update([
            'direccion' => DB::raw("COALESCE(NULLIF(TRIM(direccion),''), "
                        . "'AV. LAS PILAS URB. SANTA INES, EDIF. CENTRO CLÍNICO, PISO 3')"),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('empresa', 'direccion')) {
            Schema::table('empresa', function (Blueprint $t) {
                $t->dropColumn('direccion');
            });
        }
    }
};
