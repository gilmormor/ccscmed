<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIdColumnToNmControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_control', function (Blueprint $table) {
            // Primero agregamos la columna sin ser autoincremental
            $table->unsignedBigInteger('id_temp')->nullable()->first();
        });

        // Asignar valores correlativos temporales
        DB::statement('SET @row_number = 0');
        DB::statement('UPDATE nm_control SET id_temp = (@row_number:=@row_number+1)');

        Schema::table('nm_control', function (Blueprint $table) {
            // Renombrar la columna temporal a id y hacerla autoincremental
            DB::statement('ALTER TABLE nm_control CHANGE id_temp id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nm_control', function (Blueprint $table) {
            // Primero eliminar el autoincremento de la columna id
            DB::statement('ALTER TABLE nm_control MODIFY id BIGINT UNSIGNED NOT NULL');
            
            // Luego eliminar la clave primaria
            $table->dropPrimary();
            
            // Finalmente eliminar la columna id
            $table->dropColumn('id');
            
            // Si necesitas restaurar una clave primaria anterior, hazlo aquí
            // $table->primary(['otra_columna']);
        });
    }
}
