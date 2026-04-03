<?php

namespace App\Console\Commands;

use App\Models\NmEmpleado;
use App\Models\Seguridad\Usuario;
use App\Models\Seguridad\UsuarioRol;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PoblarUsuariosIniciales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuario:poblar-usuario-iniciales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pobla tabla usuario con usuarios iniciales';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Iniciando población de usuarios...');
        
        $stats = [
            'creados' => 0,
            'duplicados' => 0,
            'sin_email' => 0,
            'sin_password' => 0,
            'errores' => 0
        ];
        
        // Obtener todas las cédulas que ya existen en usuarios para optimizar
        $cedulasExistentes = Usuario::pluck('usuario')->toArray();
        
        NmEmpleado::select('emp_ced', 'emp_contra', 'emp_nom', 'emp_ape', 'emp_email')
            ->whereNotNull('emp_ced')
            ->whereNotIn('emp_ced', $cedulasExistentes) // Filtro a nivel de BD
            ->chunkById(500, function ($nmempleados) use (&$stats) {
                
                DB::beginTransaction();
                
                try {
                    foreach ($nmempleados as $nmempleado) {
                        
                        // Doble verificación por si acaso
                        if (Usuario::where('usuario', $nmempleado->emp_ced)->exists()) {
                            $stats['duplicados']++;
                            continue;
                        }
                        
                        // Procesar email
                        $email = $nmempleado->emp_email ?? "{$nmempleado->emp_ced}@sistema.com";
                        if (empty($nmempleado->emp_email)) {
                            $stats['sin_email']++;
                        }
                        
                        // Procesar contraseña
                        if (empty($nmempleado->emp_contra)) {
                            $password = bcrypt('Cambiar123');
                            $passTexto = 'Cambiar123';
                            $stats['sin_password']++;
                        } else {
                            $password = bcrypt($nmempleado->emp_contra);
                            $passTexto = $nmempleado->emp_contra;
                        }
                        
                        // Crear usuario
                        $usuario = new Usuario();
                        $usuario->usuario = $nmempleado->emp_ced;
                        $usuario->password = $passTexto; // Guardamos el texto plano en la columna 'pass'
                        $usuario->pass = $passTexto;
                        $usuario->nombre = trim($nmempleado->emp_nom) . ' ' . trim($nmempleado->emp_ape);
                        $usuario->email = trim($email);
                        $usuario->foto = 'hombre.jpg';
                        $usuario->save();
                        /* $usuario = Usuario::create([
                            'usuario' => $nmempleado->emp_ced,
                            'password' => $password,
                            'pass' => $passTexto,
                            'nombre' => trim($nmempleado->emp_nom) . ' ' . trim($nmempleado->emp_ape),
                            'email' => trim($email),
                            'foto' => 'hombre.jpg',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]); */
                        
                        $stats['creados']++;

                        // PASO 2: Asignar rol a usuario_rol
                        if ($usuario && $usuario->id) {
                            UsuarioRol::create([
                                'rol_id' => 2, // ID fijo según requerimiento
                                'usuario_id' => $usuario->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);                            
                        }
                    }
                    
                    DB::commit();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $stats['errores'] += count($nmempleados);
                    $this->error("Error en chunk: " . $e->getMessage());
                }
                
                $this->line("   → Progreso: {$stats['creados']} usuarios creados...");
            }, 'emp_ced'); // 👈 Aquí especificamos la columna para el chunk
        
        // Mostrar resumen
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['✅ Usuarios creados', $stats['creados']],
                ['⚠️  Duplicados evitados', $stats['duplicados']],
                ['📧 Emails asignados por defecto', $stats['sin_email']],
                ['🔑 Passwords por defecto', $stats['sin_password']],
                ['❌ Errores', $stats['errores']]
            ]
        );
    }
}
