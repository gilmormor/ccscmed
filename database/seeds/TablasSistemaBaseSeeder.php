<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablasSistemaBaseSeeder extends Seeder
{
    public function run()
    {
        // Deshabilitar FK temporalmente para insertar en orden seguro
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ── sucursal ──────────────────────────────────────────────
        DB::table('sucursal')->truncate();
        DB::table('sucursal')->insert([
            [
                'id'           => 1,
                'nombre'       => 'Las pilas',
                'abrev'        => 'Las P',
                'region_id'    => 0,
                'provincia_id' => 0,
                'comuna_id'    => 0,
                'direccion'    => '',
                'telefono1'    => '',
                'telefono2'    => null,
                'telefono3'    => null,
                'email'        => null,
                'staaprobnv'   => '0',
                'usuariodel_id'=> null,
                'deleted_at'   => null,
                'created_at'   => '2023-10-06 11:02:52',
                'updated_at'   => '2023-10-06 11:02:53',
            ],
        ]);

        // ── rol ───────────────────────────────────────────────────
        DB::table('rol')->truncate();
        DB::table('rol')->insert([
            ['id'=>1,'nombre'=>'Administrador','created_at'=>'2019-09-10 14:28:03','updated_at'=>'2019-09-10 14:28:03','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>2,'nombre'=>'Trabajador',   'created_at'=>'2023-10-02 21:15:15','updated_at'=>'2023-10-02 21:15:15','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>3,'nombre'=>'Nomina',        'created_at'=>'2023-10-06 11:31:23','updated_at'=>'2023-10-06 11:31:23','deleted_at'=>null,'usuariodel_id'=>null],
        ]);

        // ── permiso ───────────────────────────────────────────────
        DB::table('permiso')->truncate();
        DB::table('permiso')->insert([
            ['id'=>1, 'nombre'=>'Listar Recibo Honorarios',             'slug'=>'listar-recibo-honorarios',             'created_at'=>'2023-10-02 21:48:39','updated_at'=>'2023-10-02 21:48:39','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>2, 'nombre'=>'Listar Empresa',                       'slug'=>'listar-empresa',                       'created_at'=>'2023-10-06 10:54:48','updated_at'=>'2023-10-06 10:54:48','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>3, 'nombre'=>'Crear Empresa',                        'slug'=>'crear-empresa',                        'created_at'=>'2023-10-06 10:54:54','updated_at'=>'2023-10-06 10:54:54','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>4, 'nombre'=>'Editar Empresa',                       'slug'=>'editar-empresa',                       'created_at'=>'2023-10-06 10:54:58','updated_at'=>'2023-10-06 10:54:58','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>5, 'nombre'=>'Guardar Empresa',                      'slug'=>'guardar-empresa',                      'created_at'=>'2023-10-06 10:55:02','updated_at'=>'2023-10-06 10:55:02','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>6, 'nombre'=>'Eliminar Empresa',                     'slug'=>'eliminar-empresa',                     'created_at'=>'2023-10-06 10:55:08','updated_at'=>'2023-10-06 10:55:08','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>7, 'nombre'=>'Listar Sucursal',                      'slug'=>'listar-sucursal',                      'created_at'=>'2023-10-06 10:55:28','updated_at'=>'2023-10-06 10:55:28','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>8, 'nombre'=>'Crear Sucursal',                       'slug'=>'crear-sucursal',                       'created_at'=>'2023-10-06 10:55:32','updated_at'=>'2023-10-06 10:55:32','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>9, 'nombre'=>'Guardar Sucursal',                     'slug'=>'guardar-sucursal',                     'created_at'=>'2023-10-06 10:56:05','updated_at'=>'2023-10-06 10:56:05','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>10,'nombre'=>'Eliminar Sucursal',                    'slug'=>'eliminar-sucursal',                    'created_at'=>'2023-10-06 10:56:10','updated_at'=>'2023-10-06 10:56:10','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>11,'nombre'=>'Listar Recibo Honorarios Send Mail',   'slug'=>'listar-recibo-honorarios-send-mail',   'created_at'=>'2023-10-10 07:20:00','updated_at'=>'2023-10-10 07:20:00','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>12,'nombre'=>'Listar Recibo Honorarios General',     'slug'=>'listar-recibo-honorarios-general',     'created_at'=>'2024-02-26 18:01:06','updated_at'=>'2024-02-26 18:01:06','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>13,'nombre'=>'Listar correos no validos',            'slug'=>'listar-correos-no-validos',            'created_at'=>'2025-03-12 11:01:19','updated_at'=>'2025-03-12 11:02:59','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>14,'nombre'=>'Listar Recibo Empleados',              'slug'=>'listar-recibo-empleados',              'created_at'=>'2026-03-14 13:52:39','updated_at'=>'2026-03-14 13:52:39','deleted_at'=>null,'usuariodel_id'=>null],
        ]);

        // ── menu ──────────────────────────────────────────────────
        DB::table('menu')->truncate();
        DB::table('menu')->insert([
            ['id'=>1, 'menu_id'=>0,  'nombre'=>'Admin',              'url'=>'#',                         'orden'=>1,'icono'=>'fa-dashboard',    'created_at'=>'2019-09-13 13:49:57','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>2, 'menu_id'=>1,  'nombre'=>'Menú',               'url'=>'admin/menu',                'orden'=>2,'icono'=>'fa-navicon',      'created_at'=>'2019-09-09 13:47:50','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>3, 'menu_id'=>9,  'nombre'=>'Menú Rol',           'url'=>'admin/menu-rol',            'orden'=>2,'icono'=>'fa-server',       'created_at'=>'2019-09-09 13:49:06','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>5, 'menu_id'=>1,  'nombre'=>'Usuario',            'url'=>'admin/usuario',             'orden'=>1,'icono'=>'fa-users',        'created_at'=>'2019-09-10 13:03:17','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>8, 'menu_id'=>9,  'nombre'=>'Roles',              'url'=>'admin/rol',                 'orden'=>1,'icono'=>'fa-stack-exchange','created_at'=>'2019-09-12 20:26:35','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>9, 'menu_id'=>1,  'nombre'=>'Roles padre',        'url'=>'#',                         'orden'=>3,'icono'=>'fa-bars',         'created_at'=>'2019-09-13 13:49:57','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>0],
            ['id'=>10,'menu_id'=>1,  'nombre'=>'Permisos Padre',     'url'=>'#',                         'orden'=>4,'icono'=>'fa-bars',         'created_at'=>'2019-12-13 16:07:48','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>11,'menu_id'=>10, 'nombre'=>'Permiso',            'url'=>'admin/permiso',             'orden'=>1,'icono'=>'fa-cubes',        'created_at'=>'2023-09-23 16:23:43','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>12,'menu_id'=>10, 'nombre'=>'Permiso - Rol',      'url'=>'admin/permiso-rol',         'orden'=>2,'icono'=>'fa-feed',         'created_at'=>'2023-09-23 16:24:13','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>13,'menu_id'=>0,  'nombre'=>'Reporte',            'url'=>'#',                         'orden'=>4,'icono'=>'fa-file-pdf-o',   'created_at'=>'2023-10-02 21:11:13','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>14,'menu_id'=>13, 'nombre'=>'Recibo Honorarios',  'url'=>'reportrechon',              'orden'=>3,'icono'=>'fa-file-text-o',  'created_at'=>'2023-10-02 21:14:33','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>15,'menu_id'=>0,  'nombre'=>'Empresa',            'url'=>'#',                         'orden'=>2,'icono'=>'fa-map',          'created_at'=>'2023-10-06 10:52:37','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>16,'menu_id'=>15, 'nombre'=>'Datos Empresa',      'url'=>'empresa',                   'orden'=>1,'icono'=>'fa-bank',         'created_at'=>'2023-10-06 10:53:03','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>17,'menu_id'=>15, 'nombre'=>'Sucursales',         'url'=>'sucursal',                  'orden'=>2,'icono'=>'fa-building',     'created_at'=>'2023-10-06 10:53:22','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>18,'menu_id'=>0,  'nombre'=>'Honorarios',         'url'=>'#',                         'orden'=>3,'icono'=>'fa-tv',           'created_at'=>'2023-10-06 11:38:56','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>19,'menu_id'=>18, 'nombre'=>'Enviar recibos email','url'=>'reportrechonsendemail',    'orden'=>1,'icono'=>'fa-envelope-o',   'created_at'=>'2023-10-06 11:41:11','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>20,'menu_id'=>13, 'nombre'=>'Recibo General',     'url'=>'reportrechongen',           'orden'=>2,'icono'=>'fa-file-pdf-o',   'created_at'=>'2024-02-26 18:00:16','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>21,'menu_id'=>13, 'nombre'=>'Correos no validos', 'url'=>'enviaremailconerrorgen',    'orden'=>4,'icono'=>'fa-file-pdf-o',   'created_at'=>'2025-03-10 17:29:14','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>22,'menu_id'=>13, 'nombre'=>'Recibo Emp',         'url'=>'reportrecemp',              'orden'=>1,'icono'=>'fa-file-pdf-o',   'created_at'=>'2026-03-14 13:51:11','updated_at'=>'2026-04-03 17:36:31','deleted_at'=>null,'usuariodel_id'=>null],
            ['id'=>23,'menu_id'=>0,  'nombre'=>'Manual Recibos',     'url'=>'reportrecemp/manualPdf',    'orden'=>0,'icono'=>'fa-file-pdf-o',   'created_at'=>'2026-04-03 18:01:29','updated_at'=>'2026-04-03 18:01:51','deleted_at'=>null,'usuariodel_id'=>null],
        ]);

        // ── usuario ───────────────────────────────────────────────
        DB::table('usuario')->truncate();
        DB::table('usuario')->insert([
            [
                'id'           => 1,
                'usuario'      => 'admin',
                'password'     => '$2y$10$1QclywZbKyRAGhO6oAvRXeQT0sB8NbyJs/e8ANxbURXLPRSjiLPzO',
                'pass'         => 'Ggmm$3300$',
                'nombre'       => 'Gilmer Moreno',
                'email'        => 'gilmormor1@gmail.com',
                'foto'         => 'admin.jpg',
                'created_at'   => null,
                'updated_at'   => '2025-03-26 10:26:03',
                'deleted_at'   => null,
                'usuariodel_id'=> 0,
            ],
        ]);

        // ── empresa ───────────────────────────────────────────────
        DB::table('empresa')->truncate();
        DB::table('empresa')->insert([
            [
                'id'           => 1,
                'nombre'       => 'CCSC',
                'razonsocial'  => 'Centro Clinico San Cristobal',
                'rut'          => 'J-090080171',
                'giro'         => 'CLINICA',
                'iva'          => 19,
                'acteco'       => '252090',
                'sucursal_id'  => 1,
                'moneda_id'    => 1,
                'usuariodel_id'=> null,
                'deleted_at'   => null,
                'created_at'   => '2019-09-24 21:00:35',
                'updated_at'   => '2020-08-25 11:22:18',
            ],
        ]);

        // ── sucursal_usuario ──────────────────────────────────────
        DB::table('sucursal_usuario')->truncate();
        DB::table('sucursal_usuario')->insert([
            [
                'id'           => 130,
                'sucursal_id'  => 1,
                'usuario_id'   => 1,
                'usuariodel_id'=> null,
                'deleted_at'   => null,
                'created_at'   => '2024-04-04 10:35:46',
                'updated_at'   => '2024-04-04 10:35:46',
            ],
        ]);

        // ── usuario_rol ───────────────────────────────────────────
        DB::table('usuario_rol')->truncate();
        DB::table('usuario_rol')->insert([
            ['rol_id'=>1,'usuario_id'=>1,'created_at'=>null,                 'updated_at'=>null,                 'deleted_at'=>null,'usuariodel_id'=>0],
            ['rol_id'=>2,'usuario_id'=>1,'created_at'=>'2025-03-13 10:27:12','updated_at'=>'2025-03-13 10:27:12','deleted_at'=>null,'usuariodel_id'=>null],
            ['rol_id'=>3,'usuario_id'=>1,'created_at'=>'2025-04-14 11:49:02','updated_at'=>'2025-04-14 11:49:02','deleted_at'=>null,'usuariodel_id'=>null],
        ]);

        // ── menu_rol ──────────────────────────────────────────────
        DB::table('menu_rol')->truncate();
        DB::table('menu_rol')->insert([
            ['rol_id'=>1,'menu_id'=>5, 'deleted_at'=>null,'usuariodel_id'=>0,   'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>3, 'deleted_at'=>null,'usuariodel_id'=>0,   'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>2, 'deleted_at'=>null,'usuariodel_id'=>0,   'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>8, 'deleted_at'=>null,'usuariodel_id'=>0,   'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>1, 'deleted_at'=>null,'usuariodel_id'=>0,   'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>9, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>10,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>null,                 'updated_at'=>null],
            ['rol_id'=>1,'menu_id'=>11,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-09-23 16:27:49','updated_at'=>'2023-09-23 16:27:49'],
            ['rol_id'=>1,'menu_id'=>12,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-09-23 16:27:50','updated_at'=>'2023-09-23 16:27:50'],
            ['rol_id'=>1,'menu_id'=>13,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-02 21:15:40','updated_at'=>'2023-10-02 21:15:40'],
            ['rol_id'=>1,'menu_id'=>14,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-02 21:15:43','updated_at'=>'2023-10-02 21:15:43'],
            ['rol_id'=>1,'menu_id'=>15,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:53:58','updated_at'=>'2023-10-06 10:53:58'],
            ['rol_id'=>1,'menu_id'=>16,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:53:59','updated_at'=>'2023-10-06 10:53:59'],
            ['rol_id'=>1,'menu_id'=>17,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:54:00','updated_at'=>'2023-10-06 10:54:00'],
            ['rol_id'=>1,'menu_id'=>18,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 11:41:35','updated_at'=>'2023-10-06 11:41:35'],
            ['rol_id'=>1,'menu_id'=>19,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 11:41:36','updated_at'=>'2023-10-06 11:41:36'],
            ['rol_id'=>3,'menu_id'=>19,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 11:41:38','updated_at'=>'2023-10-06 11:41:38'],
            ['rol_id'=>1,'menu_id'=>20,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2024-02-26 18:00:24','updated_at'=>'2024-02-26 18:00:24'],
            ['rol_id'=>3,'menu_id'=>20,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2024-02-26 18:00:25','updated_at'=>'2024-02-26 18:00:25'],
            ['rol_id'=>3,'menu_id'=>13,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2024-02-27 10:50:36','updated_at'=>'2024-02-27 10:50:36'],
            ['rol_id'=>1,'menu_id'=>21,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2025-03-10 17:29:23','updated_at'=>'2025-03-10 17:29:23'],
            ['rol_id'=>3,'menu_id'=>21,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2025-03-12 10:58:03','updated_at'=>'2025-03-12 10:58:03'],
            ['rol_id'=>1,'menu_id'=>22,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2026-03-14 13:51:24','updated_at'=>'2026-03-14 13:51:24'],
            ['rol_id'=>2,'menu_id'=>22,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2026-03-14 16:27:39','updated_at'=>'2026-03-14 16:27:39'],
            ['rol_id'=>2,'menu_id'=>13,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2026-03-28 21:00:03','updated_at'=>'2026-03-28 21:00:03'],
            ['rol_id'=>3,'menu_id'=>14,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2026-03-28 21:00:09','updated_at'=>'2026-03-28 21:00:09'],
        ]);

        // ── permiso_rol ───────────────────────────────────────────
        DB::table('permiso_rol')->truncate();
        DB::table('permiso_rol')->insert([
            ['rol_id'=>1,'permiso_id'=>1, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-02 21:52:10','updated_at'=>'2023-10-02 21:52:10'],
            ['rol_id'=>1,'permiso_id'=>2, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:38','updated_at'=>'2023-10-06 10:56:38'],
            ['rol_id'=>1,'permiso_id'=>3, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:40','updated_at'=>'2023-10-06 10:56:40'],
            ['rol_id'=>1,'permiso_id'=>4, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:41','updated_at'=>'2023-10-06 10:56:41'],
            ['rol_id'=>1,'permiso_id'=>5, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:42','updated_at'=>'2023-10-06 10:56:42'],
            ['rol_id'=>1,'permiso_id'=>6, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:43','updated_at'=>'2023-10-06 10:56:43'],
            ['rol_id'=>1,'permiso_id'=>7, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:44','updated_at'=>'2023-10-06 10:56:44'],
            ['rol_id'=>1,'permiso_id'=>8, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:46','updated_at'=>'2023-10-06 10:56:46'],
            ['rol_id'=>1,'permiso_id'=>9, 'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:49','updated_at'=>'2023-10-06 10:56:49'],
            ['rol_id'=>1,'permiso_id'=>10,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-06 10:56:50','updated_at'=>'2023-10-06 10:56:50'],
            ['rol_id'=>1,'permiso_id'=>11,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2023-10-10 07:20:10','updated_at'=>'2023-10-10 07:20:10'],
            ['rol_id'=>1,'permiso_id'=>12,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2024-02-26 18:01:20','updated_at'=>'2024-02-26 18:01:20'],
            ['rol_id'=>3,'permiso_id'=>12,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2024-02-26 18:01:22','updated_at'=>'2024-02-26 18:01:22'],
            ['rol_id'=>3,'permiso_id'=>11,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2024-02-26 18:01:58','updated_at'=>'2024-02-26 18:01:58'],
            ['rol_id'=>3,'permiso_id'=>13,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2025-03-12 11:01:34','updated_at'=>'2025-03-12 11:01:34'],
            ['rol_id'=>1,'permiso_id'=>13,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2025-03-12 11:02:01','updated_at'=>'2025-03-12 11:02:01'],
            ['rol_id'=>1,'permiso_id'=>14,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2026-03-14 13:53:06','updated_at'=>'2026-03-14 13:53:06'],
            ['rol_id'=>2,'permiso_id'=>14,'deleted_at'=>null,'usuariodel_id'=>null,'created_at'=>'2026-03-14 13:53:09','updated_at'=>'2026-03-14 13:53:09'],
        ]);

        // ── nm_empresa ────────────────────────────────────────────
        $nmEmpresas = [
            [
                'emp_codh'  => 1,
                'emp_nombre'=> 'Centro Clinico San Cristóbal Hospital Privado C.A.',
                'emp_direc' => 'Av. Las Pilas Edif. Centro Clínico San Cristóbal Piso 3 Of. 3 Urb. Santa Inés',
                'ciudad'    => 'San Cristóbal',
                'region'    => 'Estado Táchira',
                'pais'      => 'Venezuela',
                'emp_rif'   => 'J-090080171',
                'emp_telf'  => '(0276)340.61.99 / 340.61.00',
                'logo'      => 'ccsc2.png',
            ],
            [
                'emp_codh'  => 2,
                'emp_nombre'=> 'CENTRO REGIONAL DE RESONANCIA MAGNETICA S.A.',
                'emp_direc' => 'AVDA LAS PILAS URB SANTA INES',
                'ciudad'    => '',
                'region'    => '',
                'pais'      => '',
                'emp_rif'   => 'J-301303946',
                'emp_telf'  => '02763406322',
                'logo'      => '',
            ],
            [
                'emp_codh'  => 3,
                'emp_nombre'=> 'CENTRO INTEGRADO DE DENSITOMETRIA OSEA C.A.',
                'emp_direc' => '',
                'ciudad'    => '',
                'region'    => '',
                'pais'      => '',
                'emp_rif'   => '',
                'emp_telf'  => '',
                'logo'      => '',
            ],
            [
                'emp_codh'  => 4,
                'emp_nombre'=> 'Servicios Clinicos Nutricionales, C.A.',
                'emp_direc' => 'AVDA LAS PILAS URB SANTA INES',
                'ciudad'    => 'San Cristóbal',
                'region'    => 'Estado Táchira',
                'pais'      => 'Venezuela',
                'emp_rif'   => 'J-309114077',
                'emp_telf'  => '(0276)340.61.48 / 340.63.09',
                'logo'      => 'nutricion.png',
            ],
            [
                'emp_codh'  => 5,
                'emp_nombre'=> 'Farmaclinico, C.A.',
                'emp_direc' => 'AVDA LAS PILAS URB SANTA INES',
                'ciudad'    => 'San Cristóbal',
                'region'    => 'Estado Táchira',
                'pais'      => 'Venezuela',
                'emp_rif'   => 'J-317003659',
                'emp_telf'  => '(0276)340.64.54 / 340.63.09',
                'logo'      => 'farmaclinico.png',
            ],
        ];
        foreach ($nmEmpresas as $row) {
            DB::table('nm_empresa')->updateOrInsert(
                ['emp_codh' => $row['emp_codh']],
                [
                    'emp_nombre' => $row['emp_nombre'],
                    'emp_direc'  => $row['emp_direc'],
                    'ciudad'     => $row['ciudad'],
                    'region'     => $row['region'],
                    'pais'       => $row['pais'],
                    'emp_rif'    => $row['emp_rif'],
                    'emp_telf'   => $row['emp_telf'],
                    'logo'       => $row['logo'],
                ]
            );
        }

        // ── bitacora — vacía ──────────────────────────────────────
        DB::table('bitacora')->truncate();

        // ── notificaciones — vacía ────────────────────────────────
        DB::table('notificaciones')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
