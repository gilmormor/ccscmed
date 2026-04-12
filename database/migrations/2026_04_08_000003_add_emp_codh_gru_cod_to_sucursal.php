<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmpCodhGruCodToSucursal extends Migration
{
    public function up()
    {
        Schema::table('sucursal', function (Blueprint $table) {
            if (!Schema::hasColumn('sucursal', 'emp_codh')) {
                $table->string('emp_codh', 10)->nullable()->after('id')
                    ->comment('Empresa de nómina (nm_empresa.emp_codh)');
            }
            if (!Schema::hasColumn('sucursal', 'gru_cod')) {
                $table->string('gru_cod', 10)->nullable()->after('emp_codh')
                    ->comment('Grupo empresarial (nm_grupo.gru_cod)');
            }
        });
    }

    public function down()
    {
        Schema::table('sucursal', function (Blueprint $table) {
            $table->dropColumn(['emp_codh', 'gru_cod']);
        });
    }
}
