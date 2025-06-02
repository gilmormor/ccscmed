<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNmHonpacientedet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nm_honpacientedet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nm_controlnomcls_id');
            $table->foreign('nm_controlnomcls_id','fk_nm_controlnomcls_nm_honpacientedet')->references('id')->on('nm_controlnomcls')->onDelete('cascade')->onUpdate('restrict');
            $table->string('tipo_documento',4)->nullable()->comment('Tipo Documento.');
            $table->string('factura',12)->nullable()->comment('Nro Factura.');
            $table->dateTime('fecha_fact')->comment('Fecha factura');
            $table->string('tipo_acreedor',10)->nullable()->comment('Tipo acreedor.');
            $table->string('cod_medico',8)->nullable()->comment('Codigo acreedor codigo de medico.');
            $table->integer('emp_ced')->nullable()->comment('Nro cedula Medico')->unsigned();
            $table->string('nom_paciente',60)->nullable()->comment('Nombre paciente.');
            $table->string('concepto',60)->nullable()->comment('Concepto.');
            $table->float('honorario',18,2)->nullable()->comment('Monto Horario.');
            $table->float('pagado',18,2)->nullable()->comment('Monto pagado.');
            $table->float('dscto',18,2)->nullable()->comment('Monto dscto.');
            $table->float('pago_actual',18,2)->nullable()->comment('Monto pago actual.');
            $table->float('moneda_nac',18,2)->nullable()->comment('Monto moneda nac.');
            $table->float('otra_moneda_bs',18,2)->nullable()->comment('Monto otra moneda.');
            $table->float('tasa_cambio',18,2)->nullable()->comment('Monto tasa cambio.');
            $table->float('monto_otra_moneda',18,2)->nullable()->comment('Monto monto otra moneda');
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nm_honpacientedet');
    }
}
