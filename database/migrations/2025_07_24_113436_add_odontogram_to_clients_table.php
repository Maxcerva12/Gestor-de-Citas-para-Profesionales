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
        Schema::table('clients', function (Blueprint $table) {
            $table->jsonb('odontogram')->nullable()->comment('Dental chart data in JSON format');
            $table->text('dental_notes')->nullable()->comment('Additional dental notes');
            $table->date('last_dental_visit')->nullable()->comment('Date of last dental visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['odontogram', 'dental_notes', 'last_dental_visit']);
        });
    }
};
