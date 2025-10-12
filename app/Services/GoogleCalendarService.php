<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();

        // Configurar opciones de SSL para cURL
        $this->client->setHttpClient(
            new \GuzzleHttp\Client([
                'verify' => env('CURL_CA_BUNDLE', true),
            ])
        );

        $credentialsPath = config('services.google.credentials_path');

        // Verificar que el archivo de credenciales exista
        if (!file_exists($credentialsPath)) {
            Log::error('El archivo de credenciales de Google no existe en la ruta: ' . $credentialsPath);
            throw new \Exception('El archivo de credenciales de Google no se encuentra. Por favor, verifica la configuración.');
        }

        $this->client->setAuthConfig($credentialsPath);
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        if (Auth::user() && ($token = Auth::user()->google_token)) {
            $this->client->setAccessToken($token);
        }
    }

    public function getClient()
    {
        return $this->client;
    }

    public function saveToken($token)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user) {
            $user->google_token = $token;
            $user->save();
        }
    }

    public function getCalendarService()
    {
        return new Calendar($this->client);
    }

    public function isTokenValid()
    {
        try {
            if (!$this->client->getAccessToken()) {
                return false;
            }

            if ($this->client->isAccessTokenExpired()) {
                Log::warning('Token de Google Calendar expirado');
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error al verificar token de Google Calendar: ' . $e->getMessage());
            return false;
        }
    }

    public function createEvent($appointment)
    {
        // Verificar que el token sea válido antes de proceder
        if (!$this->isTokenValid()) {
            throw new \Exception('Token de Google Calendar inválido o expirado');
        }

        $calendarService = $this->getCalendarService();

        $event = new \Google\Service\Calendar\Event([
            'summary' => 'Cita con ' . $appointment->client->name,
            'location' => $appointment->location ?? 'Sin ubicación',
            'description' =>
                "Cliente: {$appointment->client->name}\n" .
                "Profesional: {$appointment->user->name}\n" .
                "Notas: {$appointment->notes}",

            'start' => [
                'dateTime' => $appointment->start_time->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $appointment->end_time->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],

            'attendees' => [
                ['email' => $appointment->client->email],
                ['email' => $appointment->user->email],
            ],

            'guestsCanModify' => false,
            'guestsCanInviteOthers' => false,
            'guestsCanSeeOtherGuests' => true,

            'reminders' => [
                'useDefault' => true,
            ],
        ]);

        $calendarId = 'primary';

        $createdEvent = $calendarService->events->insert($calendarId, $event, [
            'sendUpdates' => 'all',
        ]);

        return $createdEvent->id;
    }

}
