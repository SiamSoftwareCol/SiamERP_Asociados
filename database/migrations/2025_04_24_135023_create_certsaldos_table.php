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
        Schema::create('certsaldos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tercero_id');
            $table->foreign('tercero_id')
                  ->references('id')
                  ->on('terceros')
                  ->onDelete('cascade');

            // Campos de retenciones
            $table->decimal('aportes', 15, 2)->default(0);
            $table->decimal('ahorro', 15, 2)->default(0);
            $table->decimal('cartera', 15, 2)->default(0);
            $table->decimal('cdat', 15, 2)->default(0);
            $table->decimal('base_retencion', 15, 2)->default(0);
            $table->decimal('interes', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certsaldos');
    }
};
