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
        Schema::create('f143_s', function (Blueprint $table) {
            $table->id();

            // Campos de Identificación del Registro Estadístico/Social
            $table->string('unidad_captura', 10)->comment('Identificador de la unidad de captura de la entidad');
            $table->string('codigo_renglon', 20)->comment('Código único del renglón de impacto social');
            $table->string('descripcion_renglon', 255)->comment('Descripción detallada del programa o actividad de impacto');

            // Campos de Resumen General
            $table->integer('numero')->comment('Conteo general de personas o eventos relacionados con el renglón');
            $table->decimal('porcentaje', 5, 2)->nullable()->comment('Porcentaje de participación o cumplimiento');

            // Recursos y Beneficiarios - ASOCIADOS
            $table->decimal('recursos_girados_benef_asociados', 20, 2)->comment('Valor de los recursos girados en el año para beneficio de asociados');
            $table->integer('numero_asociados_beneficiados')->comment('Número de asociados que recibieron el beneficio');

            // Recursos y Beneficiarios - EMPLEADOS
            $table->decimal('recursos_girados_benef_empleados', 20, 2)->comment('Valor de los recursos girados en el año para beneficio de empleados');
            $table->integer('numero_empleados_beneficiados')->comment('Número de empleados que recibieron el beneficio');

            // Recursos y Beneficiarios - COMUNIDAD
            $table->decimal('recursos_girados_benef_comunidad', 20, 2)->comment('Valor de los recursos girados en el año para beneficio de la comunidad');
            $table->integer('numero_personas_comunidad_beneficiadas')->comment('Número de personas de la comunidad que recibieron el beneficio');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f143_s');
    }
};
