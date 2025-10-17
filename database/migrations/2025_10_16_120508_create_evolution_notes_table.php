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
        Schema::create('evolution_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_history_id')->constrained('medical_histories')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');

            $table->timestamp('fecha_nota');
            $table->text('motivo_consulta')->nullable();
            $table->text('sintomas')->nullable();
            $table->text('examen_clinico')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento_realizado')->nullable();
            $table->text('medicamentos_recetados')->nullable();
            $table->text('indicaciones')->nullable();
            $table->date('proxima_cita')->nullable();
            $table->text('observaciones')->nullable();

            $table->foreignId('profesional_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('profesional_nombre')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evolution_notes');
    }
};
