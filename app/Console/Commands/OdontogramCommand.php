<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\OdontogramService;

class OdontogramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odontogram:manage
                            {action : La acción a realizar (init, validate, export, stats)}
                            {--client= : ID específico del cliente}
                            {--format=json : Formato de exportación (json, csv)}
                            {--output= : Archivo de salida para exportación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar odontogramas de clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $clientId = $this->option('client');
        $format = $this->option('format');
        $output = $this->option('output');

        switch ($action) {
            case 'init':
                return $this->initializeOdontograms();

            case 'validate':
                return $this->validateOdontograms($clientId);

            case 'export':
                return $this->exportOdontogram($clientId, $format, $output);

            case 'stats':
                return $this->showStatistics($clientId);

            default:
                $this->error("Acción no reconocida: {$action}");
                $this->info('Acciones disponibles: init, validate, export, stats');
                return Command::FAILURE;
        }
    }

    /**
     * Inicializar odontogramas vacíos para clientes que no los tengan
     */
    private function initializeOdontograms()
    {
        $this->info('Inicializando odontogramas vacíos...');

        $clients = Client::whereNull('odontogram')->get();
        $count = 0;

        foreach ($clients as $client) {
            $client->update([
                'odontogram' => OdontogramService::initializeEmptyOdontogram()
            ]);
            $count++;
        }

        $this->info("✅ {$count} odontogramas inicializados correctamente.");
        return Command::SUCCESS;
    }

    /**
     * Validar odontogramas existentes
     */
    private function validateOdontograms(?string $clientId = null)
    {
        $this->info('Validando odontogramas...');

        $query = Client::whereNotNull('odontogram');
        if ($clientId) {
            $query->where('id', $clientId);
        }

        $clients = $query->get();
        $validCount = 0;
        $errorCount = 0;

        foreach ($clients as $client) {
            $errors = OdontogramService::validateOdontogram($client->odontogram);

            if (empty($errors)) {
                $validCount++;
                $this->info("✅ Cliente {$client->id} ({$client->name}): Válido");
            } else {
                $errorCount++;
                $this->error("❌ Cliente {$client->id} ({$client->name}): Errores encontrados");
                foreach ($errors as $error) {
                    $this->line("   - {$error}");
                }
            }
        }

        $this->info("\n📊 Resumen:");
        $this->info("✅ Válidos: {$validCount}");
        $this->info("❌ Con errores: {$errorCount}");

        return $errorCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Exportar odontograma de un cliente
     */
    private function exportOdontogram(?string $clientId, string $format, ?string $output)
    {
        if (!$clientId) {
            $this->error('Se requiere especificar el ID del cliente con --client=ID');
            return Command::FAILURE;
        }

        $client = Client::find($clientId);
        if (!$client) {
            $this->error("Cliente con ID {$clientId} no encontrado.");
            return Command::FAILURE;
        }

        if (!$client->odontogram) {
            $this->error("El cliente {$client->name} no tiene odontograma.");
            return Command::FAILURE;
        }

        try {
            $exportData = OdontogramService::export($client->odontogram, $format);

            if ($output) {
                file_put_contents($output, $exportData);
                $this->info("✅ Odontograma exportado a: {$output}");
            } else {
                $this->info("📄 Odontograma de {$client->name}:");
                $this->line($exportData);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error al exportar: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Mostrar estadísticas de odontogramas
     */
    private function showStatistics(?string $clientId = null)
    {
        $this->info('📊 Estadísticas de Odontogramas');
        $this->info('================================');

        if ($clientId) {
            $client = Client::find($clientId);
            if (!$client || !$client->odontogram) {
                $this->error("Cliente no encontrado o sin odontograma.");
                return Command::FAILURE;
            }

            $this->showClientStatistics($client);
        } else {
            $this->showGlobalStatistics();
        }

        return Command::SUCCESS;
    }

    /**
     * Mostrar estadísticas de un cliente específico
     */
    private function showClientStatistics(Client $client)
    {
        $this->info("Cliente: {$client->name} (ID: {$client->id})");
        $this->line('');

        $stats = OdontogramService::generateStatistics($client->odontogram);

        $this->info("📈 Resumen:");
        $this->line("Dientes permanentes registrados: {$stats['total_permanent']}");
        $this->line("Dientes temporales registrados: {$stats['total_temporary']}");

        $this->line('');
        $this->info("🦷 Estados de dientes:");

        foreach ($stats['status_counts'] as $status => $count) {
            if ($count > 0) {
                $statusInfo = OdontogramService::TOOTH_STATUSES[$status];
                $this->line("  {$statusInfo['label']}: {$count}");
            }
        }
    }

    /**
     * Mostrar estadísticas globales
     */
    private function showGlobalStatistics()
    {
        $totalClients = Client::count();
        $clientsWithOdontogram = Client::whereNotNull('odontogram')->count();
        $clientsWithoutOdontogram = $totalClients - $clientsWithOdontogram;

        $this->info("👥 Clientes:");
        $this->line("Total: {$totalClients}");
        $this->line("Con odontograma: {$clientsWithOdontogram}");
        $this->line("Sin odontograma: {$clientsWithoutOdontogram}");

        if ($clientsWithOdontogram > 0) {
            $this->line('');
            $this->info("🦷 Estadísticas consolidadas:");

            $globalStats = [];
            foreach (OdontogramService::TOOTH_STATUSES as $status => $config) {
                $globalStats[$status] = 0;
            }

            $clients = Client::whereNotNull('odontogram')->get();
            foreach ($clients as $client) {
                $stats = OdontogramService::generateStatistics($client->odontogram);
                foreach ($stats['status_counts'] as $status => $count) {
                    $globalStats[$status] += $count;
                }
            }

            foreach ($globalStats as $status => $count) {
                if ($count > 0) {
                    $statusInfo = OdontogramService::TOOTH_STATUSES[$status];
                    $this->line("  {$statusInfo['label']}: {$count}");
                }
            }
        }
    }
}
