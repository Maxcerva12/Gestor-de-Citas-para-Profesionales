<x-filament-widgets::widget>
    <div class="p-4 bg-white rounded-xl shadow-sm dark:bg-gray-800">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Disponibilidad de {{ $record->name }} {{ $record->last_name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $record->profession }} - {{ $record->especialty ?? 'Profesional' }}
            </p>
        </div>
        
        <div class="min-h-[500px]" wire:ignore>
            <div
                x-data="{}"
                x-on:livewire:initialized="
                    $wire.getFullCalendarOptions().then(options => {
                        options.events = $wire.getEvents.bind($wire);
                        options.initialDate = $wire.initialDate;
                        
                        fullCalendar = new FullCalendar.Calendar($el, options);
                        fullCalendar.render();
                        
                        $wire.on('refreshCalendar', () => {
                            fullCalendar.refetchEvents();
                        });
                    })
                "
            ></div>
        </div>
        
        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-4">
            <div class="flex items-center">
                <span class="inline-block w-3 h-3 mr-1 bg-green-500 rounded-full"></span>
                <span>Horarios disponibles</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
