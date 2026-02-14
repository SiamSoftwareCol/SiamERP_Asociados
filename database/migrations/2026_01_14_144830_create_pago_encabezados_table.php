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
        Schema::create('pago_encabezados', function (Blueprint $table) {
            $table->id();
            $table->string('tdocto')->default('PAG');
            $table->string('nro_docto')->unique();
            $table->date('fecha_docto');
            $table->string('cliente');
            $table->string('con_nota_credito');
            $table->decimal('moneda')->default(0);
            $table->decimal('tasa_cambio', 12, 2)->default(0);
            $table->decimal('vlr_pago_efectivo', 15, 2)->default(0);
            $table->decimal('vlr_pago_cheque', 15, 2)->default(0);
            $table->decimal('vlr_pago_otros', 15, 2)->default(0);
            $table->decimal('vlr_descuento', 15, 2)->default(0);
            $table->string('usuario_crea');
            $table->text('observaciones')->nullable();
            $table->string('cliente_inicial')->nullable();
            $table->string('estado', 1)->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_encabezados');
    }
};
