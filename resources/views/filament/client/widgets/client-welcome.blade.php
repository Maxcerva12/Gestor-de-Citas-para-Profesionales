<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-user-circle class="w-8 h-8 text-gray-600 dark:text-gray-300" />
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $greeting }}, {{ $client->name }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $currentDate }}</p>
                    </div>
                </div>
                
                @if($nextAppointment)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mt-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-1">
                            <x-heroicon-o-calendar class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            <span class="font-medium text-sm text-gray-900 dark:text-white">Próxima Cita</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $nextAppointment->start_time->locale('es')->isoFormat('dddd, D [de] MMMM [a las] HH:mm') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $nextAppointment->service->name ?? 'Consulta médica' }}
                        </p>
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 mt-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">No tienes citas programadas</span>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="hidden md:block">
                <x-heroicon-o-sparkles class="w-16 h-16 text-gray-300 dark:text-gray-600" />
            </div>
        </div>
    </div>
</x-filament-widgets::widget>