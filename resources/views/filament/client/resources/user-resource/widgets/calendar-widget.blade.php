<x-filament-widgets::widget>
    <div class="p-4 bg-white rounded-xl shadow-sm dark:bg-gray-800">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Disponibilidad del profesional
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Haz clic en un horario disponible para agendar tu cita
            </p>
        </div>
        
        <div class="min-h-[500px]">
            {{ $this->fullCalendar }}
        </div>
        
        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-4">
            <div class="flex items-center">
                <span class="inline-block w-3 h-3 mr-1 bg-green-500 rounded-full"></span>
                <span>Horarios disponibles</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>