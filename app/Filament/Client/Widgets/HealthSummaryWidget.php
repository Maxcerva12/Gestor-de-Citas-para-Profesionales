<?php

namespace App\Filament\Client\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class HealthSummaryWidget extends Widget
{
    protected static string $view = 'filament.client.widgets.health-summary';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    protected static ?string $heading = 'Resumen de Salud';

    protected function getViewData(): array
    {
        $client = Auth::guard('client')->user();

        // Verificar completitud del perfil médico
        $profileComplete = $this->calculateProfileCompleteness($client);

        return [
            'client' => $client,
            'profileComplete' => $profileComplete,
            'healthData' => [
                'tipo_sangre' => $client->tipo_sangre,
                'alergias' => $client->alergias,
                'aseguradora' => $client->aseguradora,
                'contacto_emergencia' => $client->nombre_contacto_emergencia,
                'telefono_emergencia' => $client->telefono_contacto_emergencia,
            ]
        ];
    }

    private function calculateProfileCompleteness($client): array
    {
        $fields = [
            'tipo_sangre' => 'Tipo de sangre',
            'aseguradora' => 'Aseguradora',
            'alergias' => 'Alergias',
            'nombre_contacto_emergencia' => 'Contacto de emergencia',
            'telefono_contacto_emergencia' => 'Teléfono de emergencia',
        ];

        $completed = [];
        $missing = [];

        foreach ($fields as $field => $label) {
            if (!empty($client->$field)) {
                $completed[] = $label;
            } else {
                $missing[] = $label;
            }
        }

        $percentage = count($completed) / count($fields) * 100;

        return [
            'percentage' => round($percentage),
            'completed' => $completed,
            'missing' => $missing,
        ];
    }
}