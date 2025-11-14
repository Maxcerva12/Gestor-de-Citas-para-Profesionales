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
        Schema::table('medical_histories', function (Blueprint $table) {
            // Agregar campos que se movieron desde la tabla clients
            $table->enum('tipo_sangre', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
                  ->nullable()
                  ->after('enfermedad_actual');
            
            $table->text('alergias')
                  ->nullable()
                  ->after('tipo_sangre')
                  ->comment('Alergias generales del paciente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_histories', function (Blueprint $table) {
            $table->dropColumn(['tipo_sangre', 'alergias']);
        });
    }
};
