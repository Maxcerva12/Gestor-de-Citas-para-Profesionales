<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\ServiceProvider;

class FilamentAssetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('custom-login', resource_path('css/custom-login.css'))
                ->loadedOnRequest(),
        ]);
    }
}
