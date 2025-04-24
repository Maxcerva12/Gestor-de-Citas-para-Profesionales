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
    
    <!-- Modifica cómo se define el modal -->
    <div x-data="{
        open: false,
        toggle() { this.open = ! this.open },
    }" 
    x-on:open-modal.window="if ($event.detail.id === 'book-appointment-modal') { open = true }" 
    x-on:close-modal.window="if ($event.detail.id === 'book-appointment-modal') { open = false }">
        <x-filament::modal 
            :heading="'Reservar cita con ' . $this->record->name"
            x-bind:open="open" 
            id="book-appointment-modal" 
            width="md">
            <div class="py-4">
                {{ $this->getBookAppointmentForm() }}
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-filament::button wire:click="submitBooking" color="primary">
                        Reservar cita
                    </x-filament::button>
                    
                    <x-filament::button x-on:click="open = false" color="secondary">
                        Cancelar
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
</x-filament-panels::page>