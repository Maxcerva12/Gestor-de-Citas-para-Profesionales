<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;

class AppointmentObserver
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        // Verificar si hay un usuario autenticado antes de acceder a sus propiedades
        if (Auth::check() && Auth::user()->google_token) {
            $eventId = $this->googleService->createEvent($appointment);
            $appointment->google_event_id = $eventId;
            $appointment->save();
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
