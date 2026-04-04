<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTimestampsSoftdeleteToAllTables extends Migration
{
    /**
     * Tablas del framework Laravel que NO se deben modificar.
     */
    protected $tablasExcluidas = [
        'migrations',
        'password_resets',
        'failed_jobs',
        'personal_access_tokens',
    ];

    public function up()
    {
        $baseDatos = env('DB_DATABASE');
        $tablas    = DB::select('SHOW TABLES');
        $columna   = 'Tables_in_' . $baseDatos;

        foreach ($tablas as $tabla) {
            $nombre = $tabla->$columna;

            if (in_array($nombre, $this->tablasExcluidas)) {
                continue;
            }

            $tieneCreated = Schema::hasColumn($nombre, 'created_at');
            $tieneUpdated = Schema::hasColumn($nombre, 'updated_at');
            $tieneDeleted = Schema::hasColumn($nombre, 'deleted_at');

            // Solo entrar al Schema::table si hay algo que agregar
            if ($tieneCreated && $tieneUpdated && $tieneDeleted) {
                continue;
            }

            Schema::table($nombre, function (Blueprint $table) use ($nombre, $tieneCreated, $tieneUpdated, $tieneDeleted) {
                if (!$tieneCreated) {
                    $table->timestamp('created_at')->useCurrent()->nullable();
                }
                if (!$tieneUpdated) {
                    $table->timestamp('updated_at')->useCurrent()->nullable();
                }
                if (!$tieneDeleted) {
                    $table->softDeletes(); // deleted_at TIMESTAMP NULL DEFAULT NULL
                }
            });
        }
    }

    public function down()
    {
        $baseDatos = env('DB_DATABASE');
        $tablas    = DB::select('SHOW TABLES');
        $columna   = 'Tables_in_' . $baseDatos;

        foreach ($tablas as $tabla) {
            $nombre = $tabla->$columna;

            if (in_array($nombre, $this->tablasExcluidas)) {
                continue;
            }

            $columnas = [];
            if (Schema::hasColumn($nombre, 'created_at')) $columnas[] = 'created_at';
            if (Schema::hasColumn($nombre, 'updated_at')) $columnas[] = 'updated_at';
            if (Schema::hasColumn($nombre, 'deleted_at')) $columnas[] = 'deleted_at';

            if (empty($columnas)) {
                continue;
            }

            Schema::table($nombre, function (Blueprint $table) use ($columnas) {
                $table->dropColumn($columnas);
            });
        }
    }
}
