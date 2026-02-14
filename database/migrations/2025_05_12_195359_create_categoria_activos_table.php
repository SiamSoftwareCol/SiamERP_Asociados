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
        Schema::create('categoria_activos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('vida_util_defecto'); // en meses
            $table->string('metodo_depreciacion_defecto');
            $table->string('cuenta_contable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_activos');
    }
};
