<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Especialidades médicas, para la constancia de honorarios profesionales.
 *
 * La especialidad no existía en ninguna parte: nm_cargos.car_desc devuelve
 * "MEDICO" para 269 de los 294 médicos, y la constancia exige el detalle
 * ("ANESTESIOLOGO", "CIRUJANO", …).
 *
 * ESTRUCTURA
 *   nm_especialidad          catálogo de especialidades
 *   nm_empleadoespecialidad  relación N:M — un médico puede tener varias
 *   nm_empleados.id          identificador que viene de nm_empleado.emp_id
 *                            del sistema local; es la llave que usa la relación
 *
 *   Ambas tablas llevan emp_codh y gru_cod, como el resto de las nm_*, para
 *   quedar alineadas con el esquema multiempresa.
 *
 * SIN CLAVES FORÁNEAS SOBRE nm_empleados A PROPÓSITO
 *   nm_empleados se recarga desde VFP8. Si la carga borra e inserta filas, una
 *   FK con ON DELETE CASCADE borraría en silencio las especialidades asignadas
 *   en cada cierre de nómina, y una sin cascada haría fallar la carga. Se deja
 *   solo el índice: la integridad de esa punta se cuida desde la aplicación.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Identificador de empleado proveniente del sistema local
        if (!Schema::hasColumn('nm_empleados', 'id')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                // No es autoincremental: el valor lo trae VFP8 desde nm_empleado.emp_id.
                // Nullable porque las filas ya cargadas todavía no lo tienen.
                $t->unsignedInteger('id')->nullable()->first();
            });

            // Unique permite varios NULL en MySQL, así que convive con las filas
            // que aún no tienen el identificador asignado.
            DB::statement('CREATE UNIQUE INDEX `uq_empleados_id` ON `nm_empleados` (`id`)');
        }

        // ── Catálogo de especialidades
        if (!Schema::hasTable('nm_especialidad')) {
            Schema::create('nm_especialidad', function (Blueprint $t) {
                // Sin autoincremento: el id lo trae VFP8, igual que el nombre.
                // Dejarlo autoincremental arriesgaría que un alta hecha desde
                // Laravel tomara un id que luego reclame el sistema local.
                $t->unsignedInteger('id')->primary();
                $t->string('nombre', 120);
                $t->integer('emp_codh')->nullable();
                $t->integer('gru_cod')->nullable();
                $t->timestamps();
                $t->softDeletes();

                // El nombre es único DENTRO de cada empresa y grupo, no en toda
                // la tabla: dos empresas pueden tener su propio "ANESTESIOLOGO".
                $t->unique(['emp_codh', 'gru_cod', 'nombre'], 'uq_especialidad_nombre');
                $t->index(['emp_codh', 'gru_cod'], 'idx_especialidad_empgru');
            });

            $this->anchoInt3('nm_especialidad');
        }

        // ── Relación médico ↔ especialidad
        if (!Schema::hasTable('nm_empleadoespecialidad')) {
            Schema::create('nm_empleadoespecialidad', function (Blueprint $t) {
                $t->unsignedInteger('id')->primary();   // también viene de VFP8
                $t->unsignedInteger('emp_id')->comment('nm_empleados.id');
                $t->unsignedInteger('esp_id')->comment('nm_especialidad.id');
                $t->integer('emp_codh')->nullable();
                $t->integer('gru_cod')->nullable();
                $t->timestamps();
                $t->softDeletes();

                // emp_id y esp_id ya son identificadores globales del sistema
                // local, así que la pareja basta para evitar duplicados.
                $t->unique(['emp_id', 'esp_id'], 'uq_empleadoespecialidad');
                $t->index('emp_id', 'idx_empesp_emp');
                $t->index('esp_id', 'idx_empesp_esp');
                $t->index(['emp_codh', 'gru_cod'], 'idx_empesp_empgru');

                // Solo sobre el catálogo, que gestiona Laravel. Ver la nota de
                // cabecera sobre por qué no se pone FK contra nm_empleados.
                $t->foreign('esp_id', 'fk_empesp_especialidad')
                  ->references('id')->on('nm_especialidad')
                  ->onUpdate('cascade')->onDelete('restrict');
            });

            $this->anchoInt3('nm_empleadoespecialidad');
        }
    }

    /**
     * Ajusta emp_codh y gru_cod a INT(3), el ancho que usan el resto de las
     * tablas nm_*. Laravel no expone el display width, así que se hace aparte.
     */
    private function anchoInt3(string $tabla): void
    {
        foreach (['emp_codh', 'gru_cod'] as $col) {
            DB::statement("ALTER TABLE `$tabla` MODIFY `$col` INT(3) NULL");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nm_empleadoespecialidad');
        Schema::dropIfExists('nm_especialidad');

        if (Schema::hasColumn('nm_empleados', 'id')) {
            $existe = DB::select("SHOW INDEX FROM `nm_empleados` WHERE Key_name = 'uq_empleados_id'");
            if ($existe) {
                DB::statement('DROP INDEX `uq_empleados_id` ON `nm_empleados`');
            }
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->dropColumn('id');
            });
        }
    }
};
