<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Categorías de empleados: catálogo (nm_categoria) y su relación 1:N con
 * nm_empleados a través de nm_empleados.categoria_id.
 *
 * Una categoría agrupa a muchos empleados; cada empleado tiene, a lo sumo,
 * una categoría. nm_categoria.id es el lado "uno" (referenciado),
 * nm_empleados.categoria_id es el lado "muchos" (referencia).
 *
 * ORIGEN DEL ID — igual que nm_especialidad
 *   nm_categoria.id NO es autoincremental: lo trae VFP8 junto con cat_descrip.
 *   Dejarlo autoincremental arriesgaría que un alta hecha desde Laravel tomara
 *   un id que luego reclame el sistema local.
 *
 * SOBRE LA CLAVE FORÁNEA
 *   Aquí sí se pone FK (a diferencia de nm_empleadoespecialidad.emp_id, que no
 *   la lleva contra nm_empleados): la dirección es la opuesta. nm_categoria es
 *   el catálogo estable (el padre); nm_empleados.categoria_id es quien
 *   referencia. El riesgo que se evitó en el otro caso —una carga masiva de
 *   nm_empleados truncando la tabla referenciada— no aplica aquí porque
 *   nm_categoria no se recarga con cada cierre de nómina, y el "id" en
 *   nm_empleados llegó nullable precisamente porque ese campo se puebla con
 *   UPDATE, no con TRUNCATE + INSERT.
 *
 *   Si la carga desde VFP8 llega a fallar por esta FK, la causa más probable
 *   es orden: hay que subir/actualizar nm_categoria antes que
 *   nm_empleados.categoria_id.
 *
 * MOTOR — MySQL exige InnoDB en AMBAS tablas para crear una FK (error 1215
 * si alguna es MyISAM). En producción ya encontramos varias tablas legacy en
 * MyISAM (ver la migración convert_myisam_tables_to_innodb), así que antes de
 * crear la restricción se fuerza el motor de las dos, en vez de asumirlo.
 *
 * PASOS GRANULARES A PROPÓSITO
 *   Laravel ejecuta "add column" y "add foreign key" como dos ALTER TABLE
 *   separados. Si el segundo falla (como pasó en producción por el motor),
 *   la columna queda creada sin la restricción y la migración no se marca
 *   como ejecutada. Cada paso comprueba su propio estado por separado para
 *   que un reintento complete solo lo que falta, sin chocar con "column
 *   already exists" ni con "duplicate foreign key".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('nm_categoria')) {
            Schema::create('nm_categoria', function (Blueprint $t) {
                $t->engine = 'InnoDB';
                $t->unsignedInteger('id')->primary();
                $t->string('cat_descrip', 120);
                $t->timestamps();
                $t->softDeletes();
            });
        }

        $this->asegurarInnoDB('nm_categoria');
        $this->asegurarInnoDB('nm_empleados');

        if (!Schema::hasColumn('nm_empleados', 'categoria_id')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->unsignedInteger('categoria_id')->nullable()->after('id');
            });
        }

        if (!$this->existeIndice('nm_empleados', 'idx_empleados_categoria')) {
            DB::statement('CREATE INDEX `idx_empleados_categoria` ON `nm_empleados` (`categoria_id`)');
        }

        if (!$this->existeForeignKey('nm_empleados', 'fk_empleados_categoria')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->foreign('categoria_id', 'fk_empleados_categoria')
                  ->references('id')->on('nm_categoria')
                  ->onUpdate('cascade')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        if ($this->existeForeignKey('nm_empleados', 'fk_empleados_categoria')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->dropForeign('fk_empleados_categoria');
            });
        }

        if (Schema::hasColumn('nm_empleados', 'categoria_id')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->dropColumn('categoria_id');
            });
        }

        Schema::dropIfExists('nm_categoria');
    }

    private function asegurarInnoDB(string $tabla): void
    {
        $motor = DB::selectOne(
            'SELECT engine FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?',
            [$tabla]
        )->engine ?? null;

        if ($motor !== null && strcasecmp($motor, 'InnoDB') !== 0) {
            DB::statement("ALTER TABLE `$tabla` ENGINE=InnoDB");
        }
    }

    private function existeIndice(string $tabla, string $nombre): bool
    {
        return count(DB::select("SHOW INDEX FROM `$tabla` WHERE Key_name = ?", [$nombre])) > 0;
    }

    private function existeForeignKey(string $tabla, string $nombre): bool
    {
        return count(DB::select(
            'SELECT constraint_name FROM information_schema.table_constraints
             WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ?',
            [$tabla, $nombre]
        )) > 0;
    }
};
