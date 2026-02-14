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
        Schema::create('f9026_s', function (Blueprint $table) {
            $table->id();


            $table->string('tipo_iden', 5)->comment('Tipo de Identificación del titular (ej: CC, NIT)');
            $table->string('nit', 20)->comment('Número de Identificación del titular');
            $table->string('codigo_contable', 15)->comment('Cuenta contable asociada a la captación (PUC)');
            $table->string('nombre_deposito', 100)->comment('Nombre o descripción del producto de captación');
            $table->string('tipo_ahorro', 10)->nullable()->comment('Clasificación o tipo de ahorro/depósito (ej: CDT, Ahorro)');
            $table->string('numero_cuenta', 50)->comment('Número único de la cuenta de captación');

            // Campos de Montos y Saldos
            $table->decimal('deposito_inicial', 20, 2)->nullable()->comment('Monto inicial del depósito/captación');
            $table->decimal('saldo', 20, 2)->comment('Saldo actual de la captación a la fecha de corte');
            $table->decimal('intereses_causados', 20, 2)->default(0.00)->comment('Intereses causados y pendientes de pago');

            // Campos de Plazos y Fechas
            $table->date('fecha_apertura')->comment('Fecha de creación o apertura de la cuenta');
            $table->integer('plazo')->nullable()->comment('Plazo original del depósito en días o meses (si aplica)');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha de vencimiento (para CDT o plazos fijos)');

            // Campos de Condiciones Financieras
            $table->string('modalidad', 50)->comment('Modalidad de la captación (ej: a término, a la vista)');
            $table->decimal('tasa_interes_nominal', 5, 4)->comment('Tasa de interés nominal pactada (ej: 0.0500)');
            $table->decimal('tasa_interes_efectiva', 5, 4)->comment('Tasa de interés efectiva anual (E.A.)');
            $table->string('amortizacion', 20)->nullable()->comment('Método de amortización (si aplica para algún tipo de captación)');

            // Campos de Exenciones y Estado (GMF - Gravamen a Movimientos Financieros)
            $table->boolean('excenta_gmf')->default(false)->comment('Indica si la cuenta está exenta del GMF');
            $table->date('fecha_aceptacion_egmf')->nullable()->comment('Fecha de aceptación de la exención del GMF');
            $table->string('estado', 10)->default('ACTIVO')->comment('Estado de la captación (ACTIVO, CANCELADO, etc.)');
            $table->boolean('cta_bajo_monto')->default(false)->comment('Indica si la cuenta es de bajo monto');

            // Campos de Titularidad
            $table->integer('cotitulares')->default(0)->comment('Número de cotitulares de la cuenta');
            $table->string('conjunta_colectivo', 20)->nullable()->comment('Tipo de manejo de la cuenta (Conjunta o Colectiva)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f9026_s');
    }
};
