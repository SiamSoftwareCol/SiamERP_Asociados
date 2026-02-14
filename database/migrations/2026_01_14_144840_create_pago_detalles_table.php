<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pago_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_encabezado_id')->constrained('pago_encabezados')->onDelete('cascade');
            $table->string('tdocto');
            $table->string('nro_docto');
            $table->integer('consecutivo');
            $table->string('cliente');
            $table->string('tipo_pago');
            $table->string('tdocto_dvt')->nullable();
            $table->string('nro_docto_dvt')->nullable();
            $table->string('nro_cuota_dvt')->nullable();
            $table->string('consecutivo_dvt')->nullable();
            $table->string('con_descuento_dvt')->nullable();
            $table->string('tdocto_dre')->nullable();
            $table->string('nro_docto_dre')->nullable();
            $table->string('con_descuento_vde')->nullable();
            $table->string('consecutivo_vde')->nullable();
            $table->string('nro_cuota_vde')->nullable();
            $table->string('con_descuento_lcd')->nullable();
            $table->string('con_servicio_lcd')->nullable();
            $table->decimal('vlr_pago', 15, 2);
            $table->decimal('vlr_descuento', 15, 2)->default(0);
            $table->decimal('vlr_causado', 15, 2)->default(0);
            $table->string('nro_credito')->nullable();
            $table->string('estado_pago', 1)->default('A');
            $table->string('tipo_recalculo')->nullable();
            $table->decimal('vlr_cuenta_orden',  15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_detalles');
    }
};
