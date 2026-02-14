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
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->foreignId('categoria_id')->constrained('categoria_activos');
            $table->date('fecha_adquisicion');
            $table->decimal('valor_adquisicion', 12, 2);
            $table->decimal('valor_residual', 12, 2);
            $table->integer('vida_util_meses');
            $table->enum('estado', ['activo', 'en_reparacion', 'dado_de_baja'])->default('activo');
            $table->string('ubicacion')->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('users');
            $table->enum('metodo_depreciacion', ['linea_recta']);
            $table->date('ultima_depreciacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
