<?php

namespace App\Observers;

use App\Models\MedicalHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MedicalHistoryObserver
{
    /**
     * Handle the MedicalHistory "created" event.
     */
    public function created(MedicalHistory $medicalHistory): void
    {
        $this->clearHealthCache($medicalHistory->client_id);
    }

    /**
     * Handle the MedicalHistory "updated" event.
     */
    public function updated(MedicalHistory $medicalHistory): void
    {
        $this->clearHealthCache($medicalHistory->client_id);
    }

    /**
     * Handle the MedicalHistory "deleted" event.
     */
    public function deleted(MedicalHistory $medicalHistory): void
    {
        $this->clearHealthCache($medicalHistory->client_id);
    }

    /**
     * Limpiar caché de salud del cliente
     */
    protected function clearHealthCache(int $clientId): void
    {
        Cache::forget("health_summary_{$clientId}");
        Log::info("MedicalHistoryObserver: Caché de salud del cliente {$clientId} invalidado");
    }
}
