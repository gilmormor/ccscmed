<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Registra el permiso y la entrada de menú del Dashboard de Honorarios
 * Profesionales (/dashboardhon).
 *
 * El rol Administrador tiene acceso a todo automáticamente; aquí se asigna
 * explícitamente al rol Nómina, que es quien opera el módulo.
 */
return new class extends Migration
{
    private const SLUG      = 'listar-dashboard-honorarios';
    private const MENU_URL  = 'dashboardhon';
    private const ROL_NOMINA = 3;

    public function up(): void
    {
        $ahora = now();

        // ── Permiso
        $permisoId = DB::table('permiso')->where('slug', self::SLUG)->value('id');
        if (!$permisoId) {
            $permisoId = DB::table('permiso')->insertGetId([
                'nombre'     => 'Listar Dashboard Honorarios',
                'slug'       => self::SLUG,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        // ── Entrada de menú, colgando del menú padre "Dashboard"
        $padreId = DB::table('menu')->where('nombre', 'Dashboard')->where('menu_id', 0)->value('id');

        $menuId = DB::table('menu')->where('url', self::MENU_URL)->value('id');
        if (!$menuId && $padreId) {
            $orden = (int) DB::table('menu')->where('menu_id', $padreId)->max('orden') + 1;
            $menuId = DB::table('menu')->insertGetId([
                'menu_id'    => $padreId,
                'nombre'     => 'Honorarios Profesionales.',
                'url'        => self::MENU_URL,
                'orden'      => $orden,
                'icono'      => 'fa fa-circle-o',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        // ── Asignar al rol Nómina
        if (DB::table('rol')->where('id', self::ROL_NOMINA)->exists()) {
            $yaTienePermiso = DB::table('permiso_rol')
                ->where('rol_id', self::ROL_NOMINA)->where('permiso_id', $permisoId)->exists();
            if (!$yaTienePermiso) {
                DB::table('permiso_rol')->insert([
                    'rol_id'     => self::ROL_NOMINA,
                    'permiso_id' => $permisoId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }

            if ($menuId) {
                $yaTieneMenu = DB::table('menu_rol')
                    ->where('rol_id', self::ROL_NOMINA)->where('menu_id', $menuId)->exists();
                if (!$yaTieneMenu) {
                    DB::table('menu_rol')->insert([
                        'rol_id'     => self::ROL_NOMINA,
                        'menu_id'    => $menuId,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $permisoId = DB::table('permiso')->where('slug', self::SLUG)->value('id');
        if ($permisoId) {
            DB::table('permiso_rol')->where('permiso_id', $permisoId)->delete();
            DB::table('permiso')->where('id', $permisoId)->delete();
        }

        $menuId = DB::table('menu')->where('url', self::MENU_URL)->value('id');
        if ($menuId) {
            DB::table('menu_rol')->where('menu_id', $menuId)->delete();
            DB::table('menu')->where('id', $menuId)->delete();
        }
    }
};
