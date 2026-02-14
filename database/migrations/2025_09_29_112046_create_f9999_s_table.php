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
        Schema::create('f9999_s', function (Blueprint $table) {
                $table->id();

// Campos de Identificación Personal
            $table->string('tipo_identificacion', 5)->comment('Tipo de Identificación (ej: CC, CE, NIT)');
            // El NIT es la clave de negocio para el F9999
            $table->string('numero_identificacion', 20)->unique()->comment('Número de Identificación Único');
            $table->string('primer_apellido', 50)->nullable();
            $table->string('segundo_apellido', 50)->nullable();
            $table->string('nombres', 100);
            $table->string('genero', 10)->nullable()->comment('Género (M, F, O)');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('estado_civil', 20)->nullable();

            // Campos de Contacto y Ubicación
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('codigo_municipio', 10)->nullable()->comment('Código DANE del municipio de residencia');
            $table->string('estrato', 2)->nullable()->comment('Estrato socioeconómico');

            // Campos de Relación con la Entidad (Asociado/Empleado)
            $table->boolean('asociado')->default(false)->comment('Indicador: Es asociado de la cooperativa');
            $table->boolean('empleado')->default(false)->comment('Indicador: Es empleado de la cooperativa');
            $table->boolean('activo')->default(true)->comment('Estado de la relación (Activo/Inactivo)');
            $table->date('fecha_ingreso')->nullable()->comment('Fecha de ingreso a la entidad (asociado o empleado)');
            $table->date('fecha_retiro')->nullable()->comment('Fecha de retiro de la entidad (si aplica)');
            $table->boolean('asistio_ult_asamblea')->default(false)->comment('Participó en la última asamblea (relevante para gobernanza)');

            // Campos de Información Socioeconómica y Laboral
            $table->string('actividad_economica', 10)->nullable()->comment('Código CIIU de la actividad económica');
            $table->string('ocupacion', 50)->nullable();
            $table->string('sector_economico', 50)->nullable();
            $table->string('tipo_contrato', 20)->nullable()->comment('Tipo de contrato (si es empleado)');
            $table->string('jornada_laboral', 20)->nullable()->comment('Jornada laboral (si es empleado)');
            $table->string('nivel_escolaridad', 50)->nullable();
            $table->string('nivel_ingresos', 50)->nullable()->comment('Rango o nivel de ingresos (si se reporta en rangos)');
            $table->boolean('mujer_cabeza_familia')->default(false)->comment('Indicador de Mujer Cabeza de Familia');


                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f9999_s');
    }
};
