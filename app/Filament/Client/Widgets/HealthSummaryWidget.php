<?php

namespace App\Filament\Client\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HealthSummaryWidget extends Widget
{
    protected static string $view = 'filament.client.widgets.health-summary';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    protected static ?string $heading = 'Resumen de Salud';

    protected function getViewData(): array
    {
        $clientId = Auth::guard('client')->id();
        
        // Cache por 10 minutos (datos médicos no cambian frecuentemente)
        return Cache::remember("health_summary_{$clientId}", 600, function () {
            $client = Auth::guard('client')->user();
            
            // Eager load solo campos necesarios de medicalHistory
            $client->load(['medicalHistory' => function ($query) {
                $query->select('id', 'client_id', 'tipo_sangre', 'alergias');
            }]);
            
            $medicalHistory = $client->medicalHistory;

            // Verificar completitud del perfil médico
            $profileComplete = $this->calculateProfileCompleteness($client, $medicalHistory);

            return [
                'client' => $client,
                'medicalHistory' => $medicalHistory,
                'profileComplete' => $profileComplete,
                'healthData' => [
                    'tipo_sangre' => $medicalHistory?->tipo_sangre,
                    'alergias' => $medicalHistory?->alergias,
                    'aseguradora' => $client->aseguradora,
                    'contacto_emergencia' => $client->nombre_contacto_emergencia,
                    'telefono_emergencia' => $client->telefono_contacto_emergencia,
                ]
            ];
        });
    }

    private function calculateProfileCompleteness($client, $medicalHistory): array
    {
        $fieldsToCheck = [
            ['source' => 'medicalHistory', 'field' => 'tipo_sangre', 'label' => 'Tipo de sangre'],
            ['source' => 'client', 'field' => 'aseguradora', 'label' => 'Aseguradora'],
            ['source' => 'medicalHistory', 'field' => 'alergias', 'label' => 'Alergias'],
            ['source' => 'client', 'field' => 'nombre_contacto_emergencia', 'label' => 'Contacto de emergencia'],
            ['source' => 'client', 'field' => 'telefono_contacto_emergencia', 'label' => 'Teléfono de emergencia'],
        ];

        $completed = [];
        $missing = [];

        foreach ($fieldsToCheck as $item) {
            $source = $item['source'] === 'medicalHistory' ? $medicalHistory : $client;
            $field = $item['field'];
            $label = $item['label'];

            if ($source && !empty($source->$field)) {
                $completed[] = $label;
            } else {
                $missing[] = $label;
            }
        }

        $percentage = count($fieldsToCheck) > 0 ? count($completed) / count($fieldsToCheck) * 100 : 0;

        return [
            'percentage' => round($percentage),
            'completed' => $completed,
            'missing' => $missing,
        ];
    }
}