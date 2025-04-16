<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function redirectToGoogle()
    {
        return redirect($this->googleService->getClient()->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = $this->googleService->getClient();

        if ($code = $request->get('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($code);
            $this->googleService->saveToken($token);
        }

        return redirect()->route('filament.admin.pages.dashboard')->with('success', 'Google Calendar conectado exitosamente.');
    }
}
