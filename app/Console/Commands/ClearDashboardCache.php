<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache extends Command
{
    protected $signature = 'dashboard:clear-cache {--user= : ID del usuario específico}';
    protected $description = 'Limpiar el caché del dashboard';

    public function handle(): int
    {
        $userId = $this->option('user');

        if ($userId) {
            Cache::forget("dashboard_data_user_{$userId}");
            $this->info("Caché del dashboard limpiado para el usuario {$userId}");
        } else {
            // Limpiar caché de admin
            Cache::forget('dashboard_data_admin');
            
            // Limpiar cachés de usuarios conocidos (hasta 1000 usuarios)
            // Esto funciona para cualquier driver de caché
            for ($i = 1; $i <= 1000; $i++) {
                if (Cache::has("dashboard_data_user_{$i}")) {
                    Cache::forget("dashboard_data_user_{$i}");
                }
            }
            
            $this->info('Caché del dashboard limpiado para todos los usuarios');
        }

        return Command::SUCCESS;
    }
}
