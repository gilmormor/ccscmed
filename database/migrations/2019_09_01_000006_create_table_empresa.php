<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmpresa extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('empresa')) {
            Schema::create('empresa', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nombre', 150);
                $table->string('razonsocial', 100)->default('')->comment('Razon Social');
                $table->string('rut', 14)->unique();
                $table->string('giro', 80)->default('')->comment('Giro del negocio');
                $table->float('iva')->unsigned()->comment('Porcentaje IVA Ejemplo: 19');
                $table->string('acteco', 6)->default('252090')->comment('Codigo de Actividad economica empresa emisora. SII');
                $table->bigInteger('sucursal_id')->unsigned();
                $table->bigInteger('moneda_id')->unsigned()->nullable()->default(1)->comment('Moneda');
                $table->bigInteger('usuariodel_id')->unsigned()->nullable()->comment('ID Usuario que elimino el registro');
                $table->softDeletes();
                $table->timestamps();

                $table->index('sucursal_id', 'empresa_sucursal_id_foreign');
                $table->index('moneda_id', 'fk_empresa_moneda');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('empresa');
    }
}
