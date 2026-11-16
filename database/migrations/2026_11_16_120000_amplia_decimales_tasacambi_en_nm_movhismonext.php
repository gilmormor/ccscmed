<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las tasas de cambio de nm_movhismonext pasan a 4 decimales (FLOAT(12,4),
 * igual que quedó en la BD donde se hizo el cambio a mano).
 *
 * nm_movhismonext no existe en todos los ambientes (p.ej. lannube_ccscreal),
 * por eso se omite si falta la tabla o la columna. Es idempotente: solo
 * altera las columnas que todavía no son FLOAT(12,4).
 */
return new class extends Migration
{
    private array $columnas = ['mme_tasacambi', 'mme_tasacambiorig'];

    public function up(): void
    {
        if (!Schema::hasTable('nm_movhismonext')) {
            return;
        }

        foreach ($this->columnas as $columna) {
            if (!Schema::hasColumn('nm_movhismonext', $columna)) {
                continue;
            }
            if ($this->tipoActual($columna) === 'float(12,4)') {
                continue;
            }
            DB::statement("ALTER TABLE `nm_movhismonext` MODIFY `$columna` FLOAT(12,4) NULL");
        }
    }

    public function down(): void
    {
        // Sin reversión: se desconoce el tipo original en cada ambiente
        // (float(6,2) en unos, decimal(14,2) en otros) y reducir decimales perdería datos.
    }

    private function tipoActual(string $columna): ?string
    {
        $fila = DB::selectOne(
            "SELECT column_type AS t FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'nm_movhismonext' AND column_name = ?",
            [$columna]
        );

        return $fila ? strtolower($fila->t) : null;
    }
};
