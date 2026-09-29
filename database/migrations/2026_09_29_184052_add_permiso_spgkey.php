<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permiso para la pantalla de generación de clave SPG Key (/spgkey).
 *
 * Solo Administrador (1) por defecto: es una herramienta de activación,
 * no un módulo operativo de nómina. Otros roles se pueden agregar luego
 * desde Admin > Permisos.
 *
 * El permiso se asigna explícitamente: el atajo de can() compara contra
 * 'administrador' en minúscula, pero rol.nombre es 'Administrador', así
 * que ese bypass no se dispara y el rol necesita el permiso asignado
 * (misma convención que listar-dashboard-admin y listar-dashboard-honorarios).
 */
return new class extends Migration
{
    private const SLUG  = 'listar-spgkey';
    private const ROLES = [1]; // Administrador

    public function up(): void
    {
        $ahora = now();

        $permisoId = DB::table('permiso')->where('slug', self::SLUG)->value('id');
        if (!$permisoId) {
            $permisoId = DB::table('permiso')->insertGetId([
                'nombre'     => 'Listar SPG Key',
                'slug'       => self::SLUG,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        foreach (self::ROLES as $rolId) {
            if (!DB::table('rol')->where('id', $rolId)->exists()) {
                continue;
            }

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

    public function down(): void
    {
        $permisoId = DB::table('permiso')->where('slug', self::SLUG)->value('id');
        if ($permisoId) {
            DB::table('permiso_rol')->where('permiso_id', $permisoId)->delete();
            DB::table('permiso')->where('id', $permisoId)->delete();
        }
    }
};
