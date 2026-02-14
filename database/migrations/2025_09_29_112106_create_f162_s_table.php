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
        Schema::create('f162_s', function (Blueprint $table) {
            $table->id();

            // Campos de Identificación de la Aplicación
            $table->string('unidad_captura', 10)->comment('Identificador de la unidad de captura de la entidad');
            $table->string('codigo_renglon', 20)->comment('Código único del renglón de aplicación del excedente (ej: Fondo de Educación, Reserva de Protección)');
            $table->string('descripcion_renglon', 255)->comment('Descripción de la destinación del excedente');

            // Campo de Saldo (Monto)
            $table->decimal('saldo', 20, 2)->comment('Monto monetario asignado a la aplicación del excedente');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f162_s');
    }
};
