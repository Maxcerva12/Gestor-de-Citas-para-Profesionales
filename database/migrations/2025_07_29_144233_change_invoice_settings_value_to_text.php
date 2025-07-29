<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para PostgreSQL, necesitamos cambiar el tipo de columna usando ALTER COLUMN
        DB::statement("ALTER TABLE invoice_settings ALTER COLUMN value TYPE text USING value#>>'{}'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->json('value')->nullable()->change();
        });
    }
};
