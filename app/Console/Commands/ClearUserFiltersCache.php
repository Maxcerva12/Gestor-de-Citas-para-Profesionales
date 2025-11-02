<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearUserFiltersCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clear-filters-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia el caché de filtros de profesiones y especialidades';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Limpiando caché de filtros de usuarios...');

        // Limpiar caché de profesiones y especialidades
        Cache::forget('user_professions');
        Cache::forget('user_especialties');

        $this->info('✓ Caché de profesiones eliminado');
        $this->info('✓ Caché de especialidades eliminado');
        
        $this->newLine();
        $this->comment('El caché se regenerará automáticamente en la próxima carga de filtros.');

        return Command::SUCCESS;
    }
}
