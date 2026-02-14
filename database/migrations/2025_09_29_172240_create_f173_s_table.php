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
        Schema::create('f173_s', function (Blueprint $table) {
            $table->id();

           // Campos de Identificación del Registro
            $table->string('unidad_captura', 10)->comment('Identificador de la unidad de captura');
            $table->string('codigo_renglon', 10)->comment('Código del renglón del indicador');
            $table->string('descripcion_renglon', 255)->comment('Descripción del indicador o renglón');

            // Columna de Saldo y Porcentaje a la Fecha (General)
            $table->decimal('saldo_fecha', 20, 2)->comment('Saldo o valor del indicador a la fecha de corte');
            $table->decimal('porcentaje_fecha', 5, 2)->comment('Porcentaje calculado a la fecha de corte');

            // Columna de Flujo (Mes anterior)
            $table->decimal('flujos_reales_mes_anterior', 20, 2)->nullable()->comment('Valor de los flujos reales del mes inmediatamente anterior');

            // Flujos de Corto Plazo por Tramos (1 al 15 y 16 a Cierre)
            $table->decimal('dias_1_al_15', 20, 2)->comment('Flujos proyectados entre el día 1 y 15');
            $table->decimal('porcentaje_dias_1_al_15', 5, 2)->comment('Porcentaje de flujos entre el día 1 y 15');
            $table->decimal('dia_16_a_cierre_mes', 20, 2)->comment('Flujos proyectados entre el día 16 y el cierre del mes');
            $table->decimal('porcentaje_dia_16_a_cierre', 5, 2)->comment('Porcentaje de flujos entre el día 16 y el cierre del mes');

            // Flujos de Mediano Plazo por Tramos
            $table->decimal('mayor_1_mes_menor_igual_2_meses', 20, 2)->comment('Flujos proyectados entre > 1 mes y <= 2 meses');
            $table->decimal('porcentaje_mayor_1_mes_menor_igual_2_meses', 5, 2)->comment('Porcentaje de flujos entre > 1 mes y <= 2 meses');
            $table->decimal('mayor_2_meses_menor_igual_3_meses', 20, 2)->comment('Flujos proyectados entre > 2 meses y <= 3 meses');
            $table->decimal('porcentaje_mayor_2_meses_menor_igual_3_meses', 5, 2)->comment('Porcentaje de flujos entre > 2 meses y <= 3 meses');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f173_s');
    }
};
