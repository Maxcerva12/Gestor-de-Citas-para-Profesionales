<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('service_price', 10, 2)->nullable(); // Precio del servicio al momento de la cita
            $table->enum('payment_method', ['efectivo', 'transferencia', 'tarjeta_debito'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');

            // Índices para mejorar rendimiento
            $table->index(['service_id']);
            $table->index(['payment_status']);
            $table->index(['payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn(['service_id', 'service_price', 'payment_method', 'payment_status']);
        });
    }
};
