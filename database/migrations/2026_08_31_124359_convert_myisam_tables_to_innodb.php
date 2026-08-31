<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Convierte a InnoDB las tablas que quedaron en MyISAM.
 *
 * MOTIVO
 *   - MyISAM no tiene recuperación ante caídas: un reinicio sucio del servidor
 *     en medio de una escritura deja la tabla corrupta y exige REPAIR TABLE.
 *   - MyISAM bloquea la tabla completa al escribir. En app_marcajes eso importa:
 *     si VFP8 está cargando marcajes mientras alguien consulta el reporte, la
 *     consulta se queda esperando. InnoDB bloquea por fila.
 *
 *   El costo es un INSERT algo más lento, despreciable en tablas de este tamaño
 *   frente al riesgo de corrupción.
 *
 * NOTAS DE OPERACIÓN
 *   - Cada ALTER reconstruye la tabla y la bloquea mientras dura. Ejecutar fuera
 *     del horario de carga de nómina desde VFP8.
 *   - InnoDB ocupa más disco que MyISAM (del orden de 2 a 3 veces).
 *   - Se omite la tabla `migrations`: es interna de Laravel, se está escribiendo
 *     durante esta misma migración y su motor no afecta a nada.
 */
return new class extends Migration
{
    /**
     * Tablas a convertir, de menor a mayor volumen esperado: si algo falla,
     * falla temprano y sobre la tabla más pequeña.
     */
    private array $tablas = [
        'personal_access_tokens',
        'app_activaciones',
        'app_marcaje_exento',
        'nm_vacproc',
        'app_marcajes',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            $motor = $this->motorActual($tabla);

            if ($motor === null) {
                Log::info("convert_myisam: '$tabla' no existe en esta base, se omite.");
                continue;
            }

            if (strcasecmp($motor, 'InnoDB') === 0) {
                continue;
            }

            if ($this->tieneFullText($tabla)) {
                Log::warning("convert_myisam: '$tabla' tiene un índice FULLTEXT; se omite "
                           . "para no perderlo. Convertir a mano tras revisar el índice.");
                continue;
            }

            DB::statement("ALTER TABLE `$tabla` ENGINE=InnoDB");
            Log::info("convert_myisam: '$tabla' convertida de $motor a InnoDB.");
        }
    }

    /**
     * Revertir a MyISAM reintroduciría el riesgo de corrupción que esta
     * migración vino a eliminar, y sobre tablas que para entonces podrían tener
     * claves foráneas MySQL las descartaría en silencio. Por eso no se revierte:
     * si hiciera falta volver atrás, hágalo a mano y de forma consciente.
     */
    public function down(): void
    {
        Log::info('convert_myisam: down() no revierte el motor a propósito. '
                . 'Ver el comentario en la migración.');
    }

    /** Motor de la tabla, o null si la tabla no existe en esta base. */
    private function motorActual(string $tabla): ?string
    {
        $fila = DB::selectOne(
            "SELECT engine FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ? AND table_type = 'BASE TABLE'",
            [$tabla]
        );

        return $fila->engine ?? null;
    }

    /** Los índices FULLTEXT no sobreviven a la conversión en MySQL anteriores a 5.6. */
    private function tieneFullText(string $tabla): bool
    {
        $fila = DB::selectOne(
            "SELECT COUNT(*) AS n FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_type = 'FULLTEXT'",
            [$tabla]
        );

        return ($fila->n ?? 0) > 0;
    }
};
