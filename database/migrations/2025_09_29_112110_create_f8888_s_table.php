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
        Schema::create('f8888_s', function (Blueprint $table) {
            $table->id();


            // Campos de Identificación del Directivo/Miembro
            $table->string('id_tipo_directivo', 10)->comment('Código del tipo de órgano al que pertenece (ej: Consejo, Junta, Revisoría)');
            $table->string('tipo_iden', 5)->comment('Tipo de Identificación del miembro');
            $table->string('nit', 20)->comment('Número de Identificación del miembro');
            $table->string('nombre_cr', 100)->nullable()->comment('Nombre completo del directivo/miembro');
            $table->string('identificacion', 20)->comment('Número de identificación (alias de NIT)');

            // Campos de Rol y Posición
            $table->string('principal_suplente', 10)->nullable()->comment('Indica si es Principal o Suplente');
            $table->string('empleado_socio', 10)->nullable()->comment('Indica si es Empleado, Socio, o Externo');

            // Campos de Nombramiento y Vigencia
            $table->date('fecha_nombra')->nullable()->comment('Fecha de nombramiento o elección del cargo');
            $table->date('fecha_posesion')->nullable()->comment('Fecha de toma de posesión del cargo');
            $table->integer('periodo_vigencia')->nullable()->comment('Duración del periodo de vigencia en años');

            // Campos de Vínculos y Conflictos de Interés (para análisis de riesgos)
            $table->text('parentescos')->nullable()->comment('Detalle de parentescos con otros directivos o empleados (para riesgo de conflicto)');
            $table->text('vinculadas')->nullable()->comment('Detalle de vínculos con empresas vinculadas o subordinadas');

            // Campos Específicos del Revisor Fiscal
            $table->string('empresa_revisor_fiscal', 150)->nullable()->comment('Nombre de la empresa de Revisoría Fiscal (si aplica)');
            $table->string('tarjeta_prof_revisor_fiscal', 20)->nullable()->comment('Número de Tarjeta Profesional del Revisor Fiscal');

            // Campos de Certificación de Idoneidad (si aplica)
            $table->string('tipo_tarjeta', 20)->nullable()->comment('Tipo de Tarjeta o Certificación de idoneidad (ej: Ley 43)');
            $table->string('num_certificado', 50)->nullable()->comment('Número de Certificado de idoneidad');
            $table->date('fecha_expe_certi')->nullable()->comment('Fecha de expedición del certificado');

            // Campos de Diligencia (Quien reporta)
            $table->string('tipo_iddili', 5)->nullable()->comment('Tipo de ID de quien diligencia el informe');
            $table->string('nit_diligencia', 20)->nullable()->comment('NIT de quien diligencia el informe');
            $table->string('cargo_diligencia', 50)->nullable()->comment('Cargo de quien diligencia el informe');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f8888_s');
    }
};
