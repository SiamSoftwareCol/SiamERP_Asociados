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
        Schema::create('f9027_s', function (Blueprint $table) {
            $table->id();

// Identificación del Deudor y el Crédito
            $table->string('tipo_iden', 5)->comment('Tipo de Identificación del deudor');
            $table->string('nit', 20)->comment('Número de Identificación del deudor');
            $table->string('nro_credito', 50)->comment('Número único del crédito');
            $table->string('codigo_contable', 15)->comment('Cuenta contable asociada al crédito (PUC)');
            $table->string('linea_cred_entidad', 50)->nullable()->comment('Línea de crédito interna de la entidad');

            // Fechas y Plazos
            $table->date('fecha_desembolso_inicial')->comment('Fecha inicial de desembolso del crédito');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha final de vencimiento del crédito');
            $table->date('fecha_ultimo_pago')->nullable()->comment('Fecha del último pago recibido');
            $table->integer('morosidad')->default(0)->comment('Días de mora del crédito');

            // Montos y Saldos Financieros (Alta Precisión)
            $table->decimal('valor_prestamo', 20, 2)->comment('Valor inicial desembolsado del crédito');
            $table->decimal('valor_cuota_fija', 20, 2)->nullable()->comment('Valor pactado de la cuota fija');
            $table->decimal('saldo_capital', 20, 2)->comment('Saldo de capital pendiente a la fecha de corte');
            $table->decimal('saldo_intereses', 20, 2)->default(0.00)->comment('Saldo de intereses causados pendientes');
            $table->decimal('otros_saldos', 20, 2)->default(0.00)->comment('Otros saldos pendientes (ej: seguros, gastos)');
            $table->decimal('valor_mora', 20, 2)->default(0.00)->comment('Valor total en mora a la fecha de corte');
            $table->decimal('valor_cuotas_extra', 20, 2)->default(0.00)->comment('Valor de las cuotas extraordinarias pactadas');
            $table->integer('meses_cuota_extra')->nullable()->comment('Meses restantes de cuota extraordinaria');
            $table->decimal('aportes_sociales', 20, 2)->default(0.00)->comment('Valor de los aportes sociales utilizados como garantía o respaldo');

            // Características del Crédito
            $table->decimal('tasa_interes_efectiva', 5, 4)->comment('Tasa de interés efectiva anual (E.A.)');
            $table->string('tipo_cuota', 20)->nullable()->comment('Tipo de cuota (ej: Fija, Variable)');
            $table->integer('altura_cuota')->nullable()->comment('Número de cuota actual pagada o vencida');
            $table->string('amortizacion', 20)->nullable()->comment('Sistema de amortización (ej: Francés, Alemán)');
            $table->string('amorti_capital', 20)->nullable()->comment('Amortización de capital (ej: Mensual, Trimestral)');
            $table->string('modalidad', 50)->comment('Modalidad del crédito (ej: Consumo, Vivienda)');
            $table->string('destino_credito', 50)->nullable()->comment('Destino final del crédito según clasificación');

            // Modificaciones y Estado
            $table->integer('num_modificaciones')->default(0)->comment('Número de modificaciones o reestructuraciones realizadas');
            $table->string('modificaciones_al_credito', 50)->nullable()->comment('Tipo de la última modificación (ej: Reestructurado, Novación)');
            $table->string('estado_credito', 20)->default('ACTIVO')->comment('Estado del crédito (ACTIVO, CASTIGADO, CANCELADO)');

            // Garantías y Deterioro (Riesgo)
            $table->string('garantia', 50)->nullable()->comment('Tipo de garantía principal (ej: Hipotecaria, Prendaria, Personal)');
            $table->string('clase_garantia', 50)->nullable()->comment('Clasificación detallada de la garantía (ej: Póliza, FNG)');
            $table->date('fecha_avaluo')->nullable()->comment('Fecha del avalúo de la garantía');
            $table->decimal('deterioro', 20, 2)->default(0.00)->comment('Monto de la provisión/deterioro de capital calculada');
            $table->decimal('deterioro_interes', 20, 2)->default(0.00)->comment('Monto de la provisión/deterioro de intereses');
            $table->decimal('contingencia', 20, 2)->default(0.00)->comment('Valor de la contingencia reportada');
            $table->string('ent_otorgarant', 100)->nullable()->comment('Entidad que otorgó la garantía (si aplica)');
            $table->decimal('tarj_cred_cupo_rot', 20, 2)->nullable()->comment('Cupo rotatorio de tarjeta de crédito (si aplica)');

            // Créditos especiales y Redescuento
            $table->string('tipo_vivienda', 20)->nullable()->comment('Tipo de vivienda (si es crédito de vivienda)');
            $table->boolean('vis')->default(false)->comment('Indicador: Es Vivienda de Interés Social (VIS)');
            $table->string('rango_tipo', 20)->nullable()->comment('Rango de clasificación del crédito (si aplica)');
            $table->string('entidad_redescuento', 50)->nullable()->comment('Entidad de redescuento (ej: FINDETER, BANCOLDEX)');
            $table->decimal('margen_redescuento', 5, 4)->nullable()->comment('Margen de redescuento aplicado');
            $table->decimal('subsidio', 20, 2)->default(0.00)->comment('Valor del subsidio asociado al crédito');
            $table->string('desembolso', 20)->nullable()->comment('Tipo de desembolso (ej: Único, Parcial)');
            $table->string('moneda', 5)->default('COP')->comment('Moneda del crédito');
            $table->string('cod_oficina', 10)->nullable()->comment('Código de la oficina de origen del crédito');

            // Campos de Libranza
            $table->string('nit_patronal', 20)->nullable()->comment('NIT del patronal (si aplica libranza)');
            $table->string('nombre_patronal', 100)->nullable()->comment('Nombre del patronal');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f9027_s');
    }
};
