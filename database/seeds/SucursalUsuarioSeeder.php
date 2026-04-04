<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalUsuarioSeeder extends Seeder
{
    /**
     * Recorre la tabla usuario y crea un registro en sucursal_usuario
     * con sucursal_id=1 para los usuarios que no tengan ninguna sucursal asignada,
     * siempre y cuando exista el id=1 en la tabla sucursal.
     */
    public function run()
    {
        // Verificar que exista la sucursal con id=1
        $sucursalExiste = DB::table('sucursal')->where('id', 1)->exists();

        if (!$sucursalExiste) {
            $this->command->warn('No existe sucursal con id=1. No se crearon registros en sucursal_usuario.');
            return;
        }

        $usuarios = DB::table('usuario')->get();
        $now      = now();
        $creados  = 0;
        $omitidos = 0;

        foreach ($usuarios as $usuario) {
            $tieneAsignacion = DB::table('sucursal_usuario')
                ->where('usuario_id', $usuario->id)
                ->exists();

            if ($tieneAsignacion) {
                $omitidos++;
                continue;
            }

            DB::table('sucursal_usuario')->insert([
                'sucursal_id' => 1,
                'usuario_id'  => $usuario->id,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $creados++;
        }

        $this->command->info("sucursal_usuario: $creados registros creados, $omitidos omitidos (ya tenían asignación).");
    }
}
