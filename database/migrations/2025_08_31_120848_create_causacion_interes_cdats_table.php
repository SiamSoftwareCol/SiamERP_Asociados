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
        Schema::create('causacion_interes_cdats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cdat_id')->constrained('cdats');
            $table->date('fecha_causacion');
            $table->date('periodo_desde');
            $table->date('periodo_hasta');
            $table->integer('dias_liquidados');
            $table->decimal('valor_interes_bruto', 15, 2);
            $table->decimal('valor_retencion', 15, 2);
            $table->enum('estado', ['CAUSADO', 'PAGADO'])->default('CAUSADO');
            $table->foreignId('pago_interes_cdat_id')->nullable()->constrained('pago_interes_cdats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('causacion_interes_cdats');
    }
};
