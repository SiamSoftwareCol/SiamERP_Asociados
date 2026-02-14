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
        Schema::create('f9083_s', function (Blueprint $table) {
            $table->id();
            $table->string('numero_credito', 50);
            $table->string('tipo_documento', 5);
            $table->string('numero_documento', 20);
            $table->decimal('saldo_capital', 18, 2);
            $table->decimal('saldo_vencido', 18, 2)->default(0);
            $table->decimal('provision_constituida', 18, 2);
            $table->decimal('perdida_esperada', 18, 2);
            $table->integer('dias_mora')->default(0);
            $table->string('calificacion_riesgo', 5)->nullable(); // A, B, C, D, E
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
        Schema::dropIfExists('f9083_s');
    }
};
