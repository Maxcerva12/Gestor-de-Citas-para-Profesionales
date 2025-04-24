<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
                Horarios disponibles de {{ $this->record->name }} {{ $this->record->last_name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Selecciona una fecha y hora disponible para reservar tu cita
            </p>
            
            @livewire(\App\Filament\Client\Resources\UserResource\Widgets\CalendarWidget::class, [
                'record' => $this->record
            ])
        </div>
    </div>
</x-filament-panels::page>