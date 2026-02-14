<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiarios_cdat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cdat_id')
                ->constrained('cdats')
                ->onDelete('cascade');
            $table->string('tercero_id'); // porque en terceros es varchar(16)
            $table->foreign('tercero_id')
                ->references('tercero_id')
                ->on('terceros')
                ->onDelete('cascade');
            $table->string('observaciones');
            $table->boolean('principal')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('beneficiarios_cdat');
    }
};
