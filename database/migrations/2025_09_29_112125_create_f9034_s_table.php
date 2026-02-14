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
        Schema::create('f9034_s', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento', 5);
            $table->string('numero_documento', 20);
            $table->string('nombre_directivo', 255);
            $table->string('cargo', 100);
            $table->decimal('servicios', 18, 2)->default(0);
            $table->decimal('viaticos', 18, 2)->default(0);
            $table->decimal('otros_pagos', 18, 2)->default(0);
            $table->decimal('total_pagado', 18, 2);
            $table->date('fecha_corte');
            $table->timestamp('fecha_reporte')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f9034_s');
    }
};
