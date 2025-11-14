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
            // Eliminar columnas relacionadas con historia clínica
            // Esta información ahora se maneja a través de la tabla medical_histories
            $table->dropColumn([
                'odontogram',           // Odontograma (ahora en medical_histories)
                'dental_notes',         // Notas dentales (ahora en medical_histories)
                'last_dental_visit',    // Última visita dental (ahora en medical_histories)
                'historial_medico',     // Historial médico (ahora en medical_histories)
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
            $table->json('odontogram')->nullable();
            $table->text('dental_notes')->nullable();
            $table->date('last_dental_visit')->nullable();
            $table->text('historial_medico')->nullable();
        });
    }
};
