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
        Schema::create('pago_interes_cdats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cdat_id')->constrained('cdats');
            $table->decimal('valor_bruto', 15, 2);
            $table->decimal('valor_retencion', 15, 2);
            $table->decimal('valor_neto_pagado', 15, 2);
            $table->date('fecha_pago');
            $table->boolean('contabilizado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_interes_cdats');
    }
};
