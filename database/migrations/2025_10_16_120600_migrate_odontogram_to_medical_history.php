<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\MedicalHistory;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Esta migración opcional mueve los datos del odontograma
     * desde la tabla clients a medical_histories
     */
    public function up(): void
    {
        // Obtener todos los clientes
        $clients = Client::whereNotNull('odontogram')->get();

        foreach ($clients as $client) {
            // Verificar si ya tiene historia clínica
            $medicalHistory = $client->medicalHistory;

            if (!$medicalHistory) {
                // Crear historia clínica si no existe
                $medicalHistory = MedicalHistory::create([
                    'client_id' => $client->id,
                    'odontogram' => $client->odontogram,
                    'odontogram_observations' => $client->dental_notes,
                    'odontogram_last_update' => $client->last_dental_visit,
                    'ultima_visita_odontologo' => $client->last_dental_visit,
                    'created_by' => 1, // Usuario administrador
                ]);
            } else {
                // Actualizar historia clínica existente
                $medicalHistory->update([
                    'odontogram' => $client->odontogram,
                    'odontogram_observations' => $client->dental_notes,
                    'odontogram_last_update' => $client->last_dental_visit,
                    'ultima_visita_odontologo' => $client->last_dental_visit,
                ]);
            }

            // Mensaje de progreso
            Log::info("Migrado odontograma del cliente: {$client->name} {$client->apellido}");
        }

        Log::info("Migración completada. Total de registros procesados: " . $clients->count());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se puede revertir automáticamente
        Log::warning('No se puede revertir esta migración automáticamente.');
        Log::warning('Los datos permanecerán en medical_histories.');
    }
};
