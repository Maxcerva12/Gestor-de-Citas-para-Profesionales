<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as FilamentDefaultLoginPage;

class CustomLogin extends FilamentDefaultLoginPage
{
    protected static string $view = 'filament.pages.auth.custom-login';
}
