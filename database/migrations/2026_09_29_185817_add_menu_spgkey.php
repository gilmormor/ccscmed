<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Entrada de menú "SPG Key" colgando del menú padre "Admin" (id=1),
 * junto a Usuario, Menú, Roles y Permisos.
 *
 * El permiso 'listar-spgkey' ya existe (migración
 * 2026_09_29_184052_add_permiso_spgkey); aquí solo falta vincular el
 * menú al mismo rol que ya tiene el permiso.
 */
return new class extends Migration
{
    private const SLUG_PERMISO = 'listar-spgkey';
    private const MENU_URL     = 'spgkey';
    private const MENU_PADRE   = 'Admin';
    private const ROLES        = [1]; // Administrador

    public function up(): void
    {
        $ahora = now();

        $padreId = DB::table('menu')->where('nombre', self::MENU_PADRE)->where('menu_id', 0)->value('id');
        if (!$padreId) {
            return;
        }

        $menuId = DB::table('menu')->where('url', self::MENU_URL)->value('id');
        if (!$menuId) {
            $orden = (int) DB::table('menu')->where('menu_id', $padreId)->max('orden') + 1;
            $menuId = DB::table('menu')->insertGetId([
                'menu_id'    => $padreId,
                'nombre'     => 'SPG Key',
                'url'        => self::MENU_URL,
                'orden'      => $orden,
                'icono'      => 'fa fa-key',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        $permisoId = DB::table('permiso')->where('slug', self::SLUG_PERMISO)->value('id');

        foreach (self::ROLES as $rolId) {
            if (!DB::table('rol')->where('id', $rolId)->exists()) {
                continue;
            }

            $tieneMenu = DB::table('menu_rol')
                ->where('rol_id', $rolId)->where('menu_id', $menuId)->exists();
            if (!$tieneMenu) {
                DB::table('menu_rol')->insert([
                    'rol_id'     => $rolId,
                    'menu_id'    => $menuId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }

            // Por si esta migración corre en un entorno donde la del permiso
            // no se aplicó todavía: asegura que el rol también lo tenga.
            if ($permisoId) {
                $tienePermiso = DB::table('permiso_rol')
                    ->where('rol_id', $rolId)->where('permiso_id', $permisoId)->exists();
                if (!$tienePermiso) {
                    DB::table('permiso_rol')->insert([
                        'rol_id'     => $rolId,
                        'permiso_id' => $permisoId,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $menuId = DB::table('menu')->where('url', self::MENU_URL)->value('id');
        if ($menuId) {
            DB::table('menu_rol')->where('menu_id', $menuId)->delete();
            DB::table('menu')->where('id', $menuId)->delete();
        }
    }
};
