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
        Schema::create('f130_s', function (Blueprint $table) {
            $table->id();

            // Campos de Identificación del Registro Estadístico
            $table->string('unidad_captura', 10)->comment('Identificador de la unidad de captura de la entidad');
            $table->string('codigo_renglon', 20)->comment('Código único del renglón o indicador estadístico');
            $table->string('descripcion_renglon', 255)->comment('Descripción detallada del indicador estadístico reportado');

            // Campos de Valor y Porcentaje
            $table->decimal('valor', 20, 2)->comment('Valor absoluto o conteo reportado para el renglón');
            $table->decimal('porcentaje', 5, 2)->nullable()->comment('Porcentaje de participación o variación del renglón');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f130_s');
    }
};
