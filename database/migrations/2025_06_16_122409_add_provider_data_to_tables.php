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
          Schema::table('certsaldos', function (Blueprint $table) {
            $table->string('tipo')->default('asociado')->after('id');
            $table->decimal('servicios', 15, 2)->nullable()->default(0);
            $table->decimal('servicios', 15, 2)->nullable()->default(0);
            $table->decimal('ingresos_servicios', 15, 2)->nullable()->default(0);
            $table->decimal('ingresos_transporte', 15, 2)->nullable()->default(0);
            $table->decimal('ingresos_salarios', 15, 2)->nullable()->default(0);
            $table->decimal('deuda_a_favor', 15, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
