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
            // Eliminar campos médicos adicionales
            // Esta información debe manejarse a través de la tabla medical_histories
            $table->dropColumn([
                'tipo_sangre',      // Tipo de sangre (pertenece a historia clínica)
                'alergias',         // Alergias (pertenece a historia clínica)
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Restaurar las columnas en caso de rollback
            $table->enum('tipo_sangre', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('alergias')->nullable();
        });
    }
};
