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
        Schema::create('f3_s', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cuenta', 20);
            $table->string('nombre_cuenta', 255);
            $table->decimal('saldo_final', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f3_s');
    }
};
