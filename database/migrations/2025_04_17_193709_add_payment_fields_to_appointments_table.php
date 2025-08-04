<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('price_id')->nullable()->constrained();
            $table->string('payment_status')->default('pending');
            $table->decimal('amount', 8, 2)->nullable()->after('price_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['price_id']);
            $table->dropColumn(['price_id', 'payment_status', 'amount']);
        });
    }
};