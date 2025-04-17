<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('price_id')->nullable()->constrained();
            $table->string('payment_status')->default('pending');
            $table->string('stripe_payment_intent')->nullable();
            $table->string('stripe_checkout_session')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['price_id']);
            $table->dropColumn(['price_id', 'payment_status', 'stripe_payment_intent', 'stripe_checkout_session']);
        });
    }
};