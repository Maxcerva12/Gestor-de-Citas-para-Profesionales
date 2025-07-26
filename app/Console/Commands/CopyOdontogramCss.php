<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyOdontogramCss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odontogram:copy-css';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copia el archivo CSS del odontograma a la carpeta public';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourcePath = resource_path('css/odontogram-professional.css');
        $destinationPath = public_path('css/odontogram-professional.css');

        // Crear la carpeta css si no existe
        if (!File::exists(public_path('css'))) {
            File::makeDirectory(public_path('css'));
        }

        // Copiar el archivo
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
            $this->info('El archivo CSS del odontograma se ha copiado correctamente a la carpeta public.');
        } else {
            $this->error('El archivo CSS del odontograma no se encontró en resources/css.');
        }
    }
}
