<?php

namespace App\Observers;

use App\Models\Client;
use App\Services\OdontogramService;
use Illuminate\Support\Facades\Log;

class ClientOdontogramObserver
{
    /**
     * Handle the Client "creating" event.
     */
    public function creating(Client $client): void
    {
        // Inicializar odontograma vacío si no existe
        if (empty($client->odontogram)) {
            $client->odontogram = OdontogramService::initializeEmptyOdontogram();
        }
    }

    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        Log::info('Odontograma inicializado para cliente', [
            'client_id' => $client->id,
            'client_name' => $client->name
        ]);
    }

    /**
     * Handle the Client "updating" event.
     */
    public function updating(Client $client): void
    {
        // Validar odontograma antes de actualizar
        if ($client->isDirty('odontogram') && !empty($client->odontogram)) {
            $errors = OdontogramService::validateOdontogram($client->odontogram);

            if (!empty($errors)) {
                Log::warning('Errores de validación en odontograma', [
                    'client_id' => $client->id,
                    'errors' => $errors
                ]);

                // En lugar de lanzar excepción, podrías manejar de otra forma
                // throw new \InvalidArgumentException('Odontograma inválido: ' . implode(', ', $errors));
            }

            // Actualizar timestamp de metadata
            if (isset($client->odontogram['metadata'])) {
                $client->odontogram = array_merge($client->odontogram, [
                    'metadata' => array_merge($client->odontogram['metadata'], [
                        'last_updated' => now()->toISOString()
                    ])
                ]);
            }
        }
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        if ($client->wasChanged('odontogram')) {
            Log::info('Odontograma actualizado para cliente', [
                'client_id' => $client->id,
                'client_name' => $client->name
            ]);
        }
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        Log::info('Cliente con odontograma eliminado', [
            'client_id' => $client->id,
            'client_name' => $client->name
        ]);
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        Log::info('Cliente con odontograma restaurado', [
            'client_id' => $client->id,
            'client_name' => $client->name
        ]);
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        Log::warning('Cliente con odontograma eliminado permanentemente', [
            'client_id' => $client->id,
            'client_name' => $client->name
        ]);
    }
}
