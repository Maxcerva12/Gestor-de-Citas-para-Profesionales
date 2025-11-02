<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Añade índices para optimizar las consultas más comunes en UserResource
     */
    public function up(): void
    {
        // Índices para la tabla schedules
        Schema::table('schedules', function (Blueprint $table) {
            // Índice compuesto para búsquedas de disponibilidad
            // Usado en: consultas de horarios disponibles por usuario y fecha
            $table->index(['user_id', 'is_available', 'date'], 'idx_schedules_availability');
            
            // Índice para búsquedas por fecha (útil para filtros temporales)
            $table->index('date', 'idx_schedules_date');
            
            // Índice compuesto para consultas de horarios futuros disponibles
            $table->index(['date', 'is_available'], 'idx_schedules_date_available');
        });

        // Índices para la tabla appointments
        Schema::table('appointments', function (Blueprint $table) {
            // Índice compuesto para verificar horarios ocupados
            // Usado en: subconsultas de disponibilidad
            $table->index(['schedule_id', 'status'], 'idx_appointments_schedule_status');
            
            // Índice para búsquedas por estado
            $table->index('status', 'idx_appointments_status');
        });

        // Índices para la tabla users (si no existen)
        Schema::table('users', function (Blueprint $table) {
            // Solo agregar si no existe (para evitar errores)
            if (!Schema::hasColumn('users', 'profession_idx')) {
                $table->index('profession', 'idx_users_profession');
            }
            if (!Schema::hasColumn('users', 'especialty_idx')) {
                $table->index('especialty', 'idx_users_especialty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices de schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedules_availability');
            $table->dropIndex('idx_schedules_date');
            $table->dropIndex('idx_schedules_date_available');
        });

        // Eliminar índices de appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_schedule_status');
            $table->dropIndex('idx_appointments_status');
        });

        // Eliminar índices de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_profession');
            $table->dropIndex('idx_users_especialty');
        });
    }
};
