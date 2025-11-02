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
        Schema::table('invoices', function (Blueprint $table) {
            // Guardar si el descuento estaba habilitado cuando se creó la factura
            $table->boolean('discount_enabled')->default(false)->after('paid_at');
            // Guardar el porcentaje de descuento que se aplicó en el momento de creación
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount_enabled', 'discount_percentage']);
        });
    }
};
