<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PoblarSucursalUsuario extends Command
{
    protected $signature = 'sucursal:poblar-sucursal-usuario';

    protected $description = 'Asigna sucursal_id=1 a usuarios que no tengan ninguna sucursal asignada';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Verificar que exista la sucursal con id=1
        $sucursalExiste = DB::table('sucursal')->where('id', 1)->exists();

        if (!$sucursalExiste) {
            $this->warn('No existe sucursal con id=1. No se crearon registros en sucursal_usuario.');
            return;
        }

        $this->info('Iniciando asignación de sucursal a usuarios...');

        $creados  = 0;
        $omitidos = 0;
        $now      = now();

        $usuarios = DB::table('usuario')->get();

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

        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Registros creados',                    $creados],
                ['Omitidos (ya tenían sucursal asignada)', $omitidos],
            ]
        );
    }
}
