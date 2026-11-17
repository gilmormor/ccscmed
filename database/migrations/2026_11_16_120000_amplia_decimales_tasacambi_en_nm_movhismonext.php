<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las tasas de nm_movhismonext (mme_tasacambi y mme_tasacambiorig) pasan a
 * DECIMAL(14,4): 4 decimales y valor exacto (FLOAT guarda aproximaciones y
 * pierde el último decimal cuando la tasa supera los 1.000).
 *
 * nm_movhismonext no existe en todos los ambientes (p.ej. lannube_ccscreal),
 * por eso se omite si falta la tabla o la columna. Es idempotente: solo
 * altera las columnas que todavía no son DECIMAL(14,4).
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
            if ($this->tipoActual($columna) === 'decimal(14,4)') {
                continue;
            }
            DB::statement("ALTER TABLE `nm_movhismonext` MODIFY `$columna` DECIMAL(14,4) NULL");
        }
    }

    public function down(): void
    {
        // Sin reversión: se desconoce el tipo original en cada ambiente
        // y reducir decimales perdería datos.
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
