<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNotificaciones extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notificaciones')) {
            Schema::create('notificaciones', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('usuarioorigen_id')->unsigned()->comment('Usuario quien creo la notificacion');
                $table->bigInteger('usuariodestino_id')->unsigned()->nullable()->comment('Usuario destino de la notificacion');
                $table->bigInteger('vendedor_id')->unsigned()->nullable()->comment('Vendedor a quien va dirigida la Notificacion');
                $table->integer('status')->comment('Estatus de notificacion. 1=Activa, 2=Vista.');
                $table->string('nombretabla', 30)->comment('Nombre de tabla donde se origino la accion');
                $table->string('mensaje', 45)->comment('Mensaje al usuario.');
                $table->string('mensajetitle', 50)->nullable()->comment('Mensaje para el titulo al pasar el mouse.');
                $table->string('nombrepantalla', 30)->comment('Nombre de pantalla donde se genero la Accion.');
                $table->string('rutaorigen', 40)->comment('Ruta origen donde se genero la accion.');
                $table->string('rutadestino', 40)->comment('Ruta destino donde debe ir cuando se seleccione la notificacion.');
                $table->bigInteger('tabla_id')->unsigned()->comment('Valor del campo id donde se genero la accion.');
                $table->string('accion', 40)->comment('Es la accion ejecutada.');
                $table->string('icono', 40)->comment('Icono de la notificacion.');
                $table->timestamps();
                $table->softDeletes();

                $table->index('usuarioorigen_id', 'fk_notificacion_usuarioorigen');
                $table->index('usuariodestino_id', 'fk_notificacion_usuariodestino');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
}
