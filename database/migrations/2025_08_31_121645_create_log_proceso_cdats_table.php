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
        Schema::create('log_proceso_cdats', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_proceso');
            $table->date('periodo_proceso');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('estado', ['INICIADO', 'COMPLETADO', 'FALLIDO'])->default('INICIADO');
            $table->json('detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_proceso_cdats');
    }
};
