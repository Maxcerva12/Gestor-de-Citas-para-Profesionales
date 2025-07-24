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
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('clients', 'active')) {
                $table->boolean('active')->default(true)->after('country');
            }
            if (!Schema::hasColumn('clients', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('active');
            }
            if (!Schema::hasColumn('clients', 'notes')) {
                $table->longText('notes')->nullable()->after('custom_fields');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['active', 'custom_fields', 'notes']);
        });
    }
};
