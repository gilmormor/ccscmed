<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Índices de rendimiento para nómina y honorarios.
 *
 * Las tablas nm_* venían del sistema externo sin índices, lo que obligaba a
 * MySQL a hacer full table scan en cada JOIN de los dashboards y reportes.
 * Referencia: nm_honpacientedet 327k filas, nm_movhist 61k, nm_movhismonext 22k.
 */
return new class extends Migration
{
    /** Índices a crear: tabla => [nombre => columnas] */
    private array $indices = [
        'nm_movhist' => [
            'idx_movhist_emp_ced'      => 'emp_ced',
            'idx_movhist_mov_nummon'   => 'mov_nummon',
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
            'idx_honpacdet_emp_ced'    => 'emp_ced',
            'idx_honpacdet_fecha_fact' => 'fecha_fact',
            'idx_honpacdet_cls_ced'    => 'nm_controlnomcls_id, emp_ced',
            'idx_honpacdet_tipo_doc'   => 'tipo_documento',
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
