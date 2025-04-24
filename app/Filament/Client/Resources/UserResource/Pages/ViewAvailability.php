<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Resources\Pages\Page;
use App\Models\User;
use App\Models\Schedule;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Filament\Client\Resources\UserResource\Widgets\CalendarWidget;

class ViewAvailability extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.client.resources.user-resource.pages.view-availability';

    // El profesional seleccionado
    public User $record;

    public function mount(User $record)
    {
        $this->record = $record;

        // Verificar si este profesional tiene horarios disponibles
        $availableSlots = Schedule::where('user_id', $record->id)
            ->where('is_available', true)
            ->count();

        Log::info("Profesional ID {$record->id} tiene {$availableSlots} horarios disponibles");

        if ($availableSlots === 0) {
            Notification::make()
                ->title('Sin horarios disponibles')
                ->body('Este profesional no tiene horarios disponibles por el momento.')
                ->warning()
                ->send();
        }
    }

    // Método para personalizar los widgets de la página
    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

    // Configuración del widget del calendario
    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    // Método para obtener la configuración del widget del calendario
    public function getWidgetData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}