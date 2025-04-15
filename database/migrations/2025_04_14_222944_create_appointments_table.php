<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Profesional
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Cliente
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade'); // Horario seleccionado
            $table->timestamp('start_time'); // Hora de inicio de la cita
            $table->timestamp('end_time'); // Hora de fin de la cita
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'completed'])->default('pending'); // Estado de la cita
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
