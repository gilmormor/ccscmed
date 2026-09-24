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
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('nm_categoria')) {
            Schema::create('nm_categoria', function (Blueprint $t) {
                $t->unsignedInteger('id')->primary();
                $t->string('cat_descrip', 120);
                $t->timestamps();
                $t->softDeletes();
            });
        }

        if (!Schema::hasColumn('nm_empleados', 'categoria_id')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->unsignedInteger('categoria_id')->nullable()->after('id');

                $t->foreign('categoria_id', 'fk_empleados_categoria')
                  ->references('id')->on('nm_categoria')
                  ->onUpdate('cascade')->onDelete('restrict');
            });

            DB::statement('CREATE INDEX `idx_empleados_categoria` ON `nm_empleados` (`categoria_id`)');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('nm_empleados', 'categoria_id')) {
            Schema::table('nm_empleados', function (Blueprint $t) {
                $t->dropForeign('fk_empleados_categoria');
                $t->dropColumn('categoria_id');
            });
        }

        Schema::dropIfExists('nm_categoria');
    }
};
