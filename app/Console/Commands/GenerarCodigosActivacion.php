<?php

namespace App\Console\Commands;

use App\Models\AppActivacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerarCodigosActivacion extends Command
{
    protected $signature = 'app:generar-codigos-activacion
                            {--emp_ced= : Cédula específica (opcional, si se omite procesa todos los empleados activos sin código)}';

    protected $description = 'Genera códigos de activación para empleados activos de nm_empleados que aún no tienen código pendiente';

    public function handle()
    {
        $cedula = $this->option('emp_ced');

        if ($cedula) {
            $this->generarParaCedula($cedula);
            return 0;
        }

        // Empleados activos en nm_empleados que no tienen código pendiente (no activado)
        $empleados = DB::table('nm_empleados')
            ->select('emp_ced')
            ->where('emp_staact', 1)
            ->whereNotIn('emp_ced', function ($q) {
                $q->select('emp_ced')
                  ->from('app_activaciones')
                  ->where(function ($inner) {
                      $inner->where('activo', true)
                            ->orWhereNull('dispositivo_id'); // tiene código pendiente sin activar
                  });
            })
            ->groupBy('emp_ced')
            ->get();

        if ($empleados->isEmpty()) {
            $this->info('No hay empleados nuevos sin código de activación.');
            return 0;
        }

        $bar = $this->output->createProgressBar($empleados->count());
        $bar->start();

        $creados = 0;
        foreach ($empleados as $emp) {
            // Solo crear si no existe ninguno pendiente para esta cédula
            $existe = AppActivacion::where('emp_ced', $emp->emp_ced)
                ->where('activo', false)
                ->whereNull('dispositivo_id')
                ->exists();

            if (!$existe) {
                AppActivacion::create([
                    'emp_ced' => $emp->emp_ced,
                    'codigo'  => strtoupper(Str::random(8)),
                    'activo'  => false,
                ]);
                $creados++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Códigos generados: {$creados}");

        return 0;
    }

    private function generarParaCedula(string $cedula): void
    {
        $existe = DB::table('nm_empleados')->where('emp_ced', $cedula)->exists();
        if (!$existe) {
            $this->error("Cédula {$cedula} no encontrada en nm_empleados.");
            return;
        }

        $pendiente = AppActivacion::where('emp_ced', $cedula)
            ->where('activo', false)
            ->whereNull('dispositivo_id')
            ->first();

        if ($pendiente) {
            $this->info("Ya existe código pendiente para cédula {$cedula}: {$pendiente->codigo}");
            return;
        }

        $activacion = AppActivacion::create([
            'emp_ced' => $cedula,
            'codigo'  => strtoupper(Str::random(8)),
            'activo'  => false,
        ]);

        $this->info("Código generado para cédula {$cedula}: {$activacion->codigo}");
    }
}
