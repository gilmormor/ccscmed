<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * nm_control.cot_valordolar (la tasa del dólar del período) pasa a DECIMAL(14,4).
 * Es idempotente: no hace nada si ya tiene ese tipo, y se omite si la tabla o
 * la columna no existen en el ambiente.
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
        $tipo = $fila ? strtolower($fila->t) : '';

        if ($tipo === 'decimal(14,4)') {
            return;
        }

        DB::statement('ALTER TABLE `nm_control` MODIFY `cot_valordolar` DECIMAL(14,4) NULL');

        // Al pasar de FLOAT a DECIMAL aparece ruido (914.02 -> 914.0200 con decimales
        // sobrantes en el float). Los datos históricos solo tenían 2 decimales, así que
        // se redondean para recuperarlos exactos. No aplica si ya venía de un DECIMAL.
        if (strpos($tipo, 'float') === 0 || strpos($tipo, 'double') === 0) {
            DB::statement('UPDATE `nm_control` SET `cot_valordolar` = ROUND(`cot_valordolar`, 2) WHERE `cot_valordolar` IS NOT NULL');
        }
    }

    public function down(): void
    {
        // Sin reversión: bajar decimales perdería datos.
    }
};
