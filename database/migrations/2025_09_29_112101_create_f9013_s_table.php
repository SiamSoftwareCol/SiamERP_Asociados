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
        Schema::create('f9013_s', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_identificacion', 5)->comment('Tipo de Identificación del asociado (ej: CC, CE)');
            $table->string('numero_identificacion', 20)->comment('Número de Identificación del asociado');
            $table->decimal('saldo_fecha', 20, 2)->comment('Saldo total de aportes y contribuciones a la fecha de corte');
            $table->decimal('valor_aporte_mensual', 20, 2)->comment('Valor del aporte/contribución mensual pactado');
            $table->decimal('aporte_ordinario', 20, 2)->comment('Valor acumulado de aportes ordinarios');
            $table->decimal('aporte_extraordinario', 20, 2)->comment('Valor acumulado de aportes extraordinarios');
            $table->decimal('valor_revalorizacion', 20, 2)->comment('Valor de la revalorización aplicada a los aportes');
            $table->decimal('monto_promedio', 20, 2)->nullable()->comment('Monto promedio de los aportes en un periodo, si aplica');
            $table->date('ultima_fecha')->nullable()->comment('Última fecha de pago o movimiento de aportes');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f9013_s');
    }
};
