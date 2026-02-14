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
        Schema::create('cdat_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('codigo_producto')->unique()->nullable();
            $table->foreignId('cdat_plazo_id')
                  ->nullable()
                  ->constrained('cdat_plazos')
                  ->onDelete('set null');
            $table->foreignId('cdat_tasa_id')
                  ->nullable()
                  ->constrained('cdat_tasas')
                  ->onDelete('set null');
            $table->decimal('valor_minimo', 15, 2)->nullable();
            $table->decimal('valor_maximo', 15, 2)->nullable();
            $table->boolean('permite_renovacion')->default(true);
            $table->decimal('porcentaje_retencion_fuente_rendimientos', 5, 2)->nullable()->comment('Ej: 7.00 para 7%');
            $table->decimal('base_minima_retencion_fuente', 15, 2)->nullable()->comment('Monto a partir del cual aplica retención');
            $table->integer('dias_notificacion_previa_vencimiento')->unsigned()->nullable()->comment('Ej: 15 para 15 días');
            $table->boolean('permite_cancelacion_anticipada')->default(true);
            $table->decimal('porcentaje_penalizacion_cancelacion_anticipada', 5, 2)->nullable()->comment('Ej: 1.50 para 1.5% de penalización sobre intereses si se cancela anticipadamente');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdat_tipos');
    }
};
