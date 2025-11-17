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
        Schema::table('clients', function (Blueprint $table) {
            // Campos de tratamiento de datos personales
            $table->boolean('accepts_data_treatment')->default(false)->after('notes');
            $table->boolean('accepts_privacy_policy')->default(false)->after('accepts_data_treatment');
            $table->boolean('accepts_commercial_communications')->default(false)->after('accepts_privacy_policy');
            $table->timestamp('data_treatment_date')->nullable()->after('accepts_commercial_communications');
            $table->text('additional_observations')->nullable()->after('data_treatment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'accepts_data_treatment',
                'accepts_privacy_policy', 
                'accepts_commercial_communications',
                'data_treatment_date',
                'additional_observations'
            ]);
        });
    }
};
