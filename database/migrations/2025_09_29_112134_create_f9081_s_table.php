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
        Schema::create('f9081_s', function (Blueprint $table) {
            $table->id();

            // Identificación del Deudor y el Crédito (Claves de negocio)
            $table->string('tipo_iden', 5)->comment('Tipo de Identificación del deudor');
            $table->string('nit', 20)->comment('Número de Identificación del deudor');
            $table->string('nro_credito', 50)->comment('Número único del crédito al que aplica el anexo');

            // Detalles de la Garantía Secundaria (o principal, si el F9027 solo tiene una)
            $table->string('garantia2', 50)->nullable()->comment('Tipo de garantía adicional o secundaria');
            $table->date('fecha_avaluo2')->nullable()->comment('Fecha de avalúo de la garantía secundaria');
            $table->string('clase_garantia2', 50)->nullable()->comment('Clasificación detallada de la garantía secundaria');
            $table->decimal('cupo_otorgado', 20, 2)->nullable()->comment('Monto del cupo o valor máximo otorgado por la garantía');

            // Clasificación del Crédito y Condiciones de Pago
            $table->string('calif_eval_cartera', 10)->nullable()->comment('Clasificación de riesgo asignada al crédito (ej: A, B, C, D, E)');
            $table->integer('cuotas_pactadas')->nullable()->comment('Número total de cuotas pactadas en el crédito');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f9081_s');
    }
};
