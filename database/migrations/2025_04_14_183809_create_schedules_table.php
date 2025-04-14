<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con el profesional
            $table->date('date'); // Fecha del horario
            $table->time('start_time'); // Hora de inicio
            $table->time('end_time'); // Hora de fin
            $table->boolean('is_available')->default(true); // Indica si el horario está disponible
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
