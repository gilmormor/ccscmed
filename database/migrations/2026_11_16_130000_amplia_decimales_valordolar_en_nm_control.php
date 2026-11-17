<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * nm_control.cot_valordolar (la tasa del dólar del período) pasa de FLOAT(8,2)
 * a DECIMAL(14,6). Es idempotente: no hace nada si ya tiene ese tipo, y se
 * omite si la tabla o la columna no existen en el ambiente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('nm_control') || !Schema::hasColumn('nm_control', 'cot_valordolar')) {
            return;
        }

        $fila = DB::selectOne(
            "SELECT column_type AS t FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'nm_control' AND column_name = 'cot_valordolar'"
        );

        if ($fila && strtolower($fila->t) === 'decimal(14,6)') {
            return;
        }

        DB::statement('ALTER TABLE `nm_control` MODIFY `cot_valordolar` DECIMAL(14,6) NULL');

        // Al pasar de FLOAT a DECIMAL aparece ruido (914.02 -> 914.020020). Los datos
        // históricos solo tenían 2 decimales, así que se redondean para recuperarlos exactos.
        DB::statement('UPDATE `nm_control` SET `cot_valordolar` = ROUND(`cot_valordolar`, 2) WHERE `cot_valordolar` IS NOT NULL');
    }

    public function down(): void
    {
        // Sin reversión: bajar de 6 a 2 decimales perdería datos.
    }
};
