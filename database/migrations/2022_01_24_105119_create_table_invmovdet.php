<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvmovdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invmovdet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invmov_id');
            $table->foreign('invmov_id','fk_invmovdet_invmov')->references('id')->on('invmov')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodegaproducto_id');
            $table->foreign('invbodegaproducto_id','fk_invmovdet_invbodegaproducto')->references('id')->on('invbodegaproducto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_invmovdet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodega_id');
            $table->foreign('invbodega_id','fk_invmovdet_invbodega')->references('id')->on('invbodega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_invmovdet_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_invmovdet_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invmovtipo_id');
            $table->foreign('invmovtipo_id','fk_invmovdet_invmovtipo')->references('id')->on('invmovtipo')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',18,2)->comment('Cantidad');
            $table->float('cantgrupo',10,2)->comment('Cantidad agrupada ejemplo: 1 paquete, 1 rollo, 1 caja');
            $table->float('cantxgrupo',10,2)->comment('Cantidad por Grupo');
            $table->float('peso',10,4)->comment('Peso unitario Producto')->nullable();
            $table->float('cantkg',18,2)->comment('Cantidad en kg');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invmovdet');
    }
}
