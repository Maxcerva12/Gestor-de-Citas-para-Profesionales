<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class OdontogramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar directivas Blade personalizadas para el odontograma
        Blade::directive('tooth', function ($expression) {
            return "<?php echo view('components.tooth', compact($expression)); ?>";
        });

        // Registrar vistas del odontograma
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/odontogram', 'odontogram');
    }
}
