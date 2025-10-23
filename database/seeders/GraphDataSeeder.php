<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GraphDataSeeder extends Seeder
{
    /**
     * Seeder específico para generar datos que mejoren las gráficas
     * Ejecutar solo cuando necesites regenerar datos para gráficas
     */
    public function run(): void
    {
        $this->command->info('🔄 Generando datos específicos para gráficas...');

        $this->call([
            RealisticDataSeeder::class,
        ]);

        $this->command->info('✅ Datos para gráficas generados exitosamente');
        $this->command->info('💡 Tip: Ejecuta "php artisan db:seed --class=GraphDataSeeder" para regenerar solo estos datos');
    }
}