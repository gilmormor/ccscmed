<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Índices de rendimiento para nómina y honorarios.
 *
 * Las tablas nm_* venían del sistema externo sin índices, lo que obligaba a
 * MySQL a hacer full table scan en cada JOIN de los dashboards y reportes.
 * Referencia: nm_honpacientedet 327k filas, nm_movhist 61k, nm_movhismonext 22k.
 *
 * IMPORTANTE — carga desde VFP8
 * Estas tablas se llenan por INSERT fila a fila desde el sistema local de
 * nómina, así que cada índice encarece la subida. El conjunto se acotó midiendo
 * el aporte real de cada uno; solo quedan los que se ganan su costo:
 *
 *   - nm_honpacientedet solo lleva fecha_fact. Es el que importa: sin él la
 *     relación de pacientes pasa de ~10 ms a ~370 ms. Se descartaron emp_ced
 *     (sin efecto medible ni filtrando por un médico de 80k filas),
 *     tipo_documento (+28 ms, no compensa) y el compuesto con
 *     nm_controlnomcls_id (redundante con el índice de la FK).
 *   - Se descartó mov_nummon en nm_movhist: el optimizador lo prefería sobre el
 *     compuesto y los KPIs eran 366 ms más lentos con él presente.
 *
 * Antes de añadir un índice nuevo aquí, medir que aporte y que no encarezca la
 * carga sin contrapartida.
 */
return new class extends Migration
{
    /** Índices a crear: tabla => [nombre => columnas] */
    private array $indices = [
        'nm_movhist' => [
            'idx_movhist_emp_ced'      => 'emp_ced',
            'idx_movhist_mov_id'       => 'mov_id',
            'idx_movhist_mov_codcon'   => 'mov_codcon',
            'idx_movhist_nummon_ced'   => 'mov_nummon, emp_ced',
        ],
        'nm_movhismonext' => [
            'idx_movhismonext_mov_id'  => 'mov_id',
        ],
        'nm_control' => [
            'idx_control_cot_numnom'   => 'cot_numnom',
            'idx_control_cot_fdesde'   => 'cot_fdesde',
        ],
        'nm_honpacientedet' => [
            'idx_honpacdet_fecha_fact' => 'fecha_fact',
        ],
        'tipodocumento' => [
            'idx_tipodocumento_tipodoc' => 'tipodoc',
        ],
        'nm_empleados' => [
            'idx_empleados_emp_ced'    => 'emp_ced',
        ],
        'nm_conceptos' => [
            'idx_conceptos_con_cod'    => 'con_cod',
        ],
    ];

    /**
     * Índices de una versión anterior de esta migración que encarecían la carga
     * desde VFP8 sin aportar rendimiento. Se eliminan si existen.
     */
    private array $obsoletos = [
        'nm_movhist'        => ['idx_movhist_mov_nummon'],
        'nm_honpacientedet' => [
            'idx_honpacdet_emp_ced',
            'idx_honpacdet_tipo_doc',
            'idx_honpacdet_cls_ced',
        ],
    ];

    public function up(): void
    {
        foreach ($this->indices as $tabla => $defs) {
            foreach ($defs as $nombre => $columnas) {
                if ($this->existeIndice($tabla, $nombre)) {
                    continue;
                }
                DB::statement("CREATE INDEX `$nombre` ON `$tabla` ($columnas)");
            }
        }

        foreach ($this->obsoletos as $tabla => $nombres) {
            foreach ($nombres as $nombre) {
                if (!$this->existeIndice($tabla, $nombre)) {
                    continue;
                }
                DB::statement("DROP INDEX `$nombre` ON `$tabla`");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indices as $tabla => $defs) {
            foreach (array_keys($defs) as $nombre) {
                if (!$this->existeIndice($tabla, $nombre)) {
                    continue;
                }
                DB::statement("DROP INDEX `$nombre` ON `$tabla`");
            }
        }
    }

    private function existeIndice(string $tabla, string $nombre): bool
    {
        return count(DB::select("SHOW INDEX FROM `$tabla` WHERE Key_name = ?", [$nombre])) > 0;
    }
};
