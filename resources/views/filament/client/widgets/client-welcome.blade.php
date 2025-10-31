<x-filament-widgets::widget>
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-800 dark:to-indigo-900 rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-user-circle class="w-8 h-8 text-white/90 dark:text-blue-100" />
                    <div>
                        <h2 class="text-xl font-bold text-black dark:text-black">{{ $greeting }}, {{ $client->name }}</h2>
                        <p class="text-blue-100 dark:text-blue-100 text-sm">{{ $currentDate }}</p>
                    </div>
                </div>
                
                @if($nextAppointment)
                    <div class="bg-white/20 dark:bg-white/10 rounded-lg p-3 mt-4">
                        <div class="flex items-center gap-2 mb-1">
                            <x-heroicon-o-calendar class="w-4 h-4 text-black dark:text-black" />
                            <span class="font-medium text-sm text-black dark:text-black">Próxima Cita</span>
                        </div>
                        <p class="text-sm text-white/90 dark:text-blue-100">
                            {{ $nextAppointment->start_time->locale('es')->isoFormat('dddd, D [de] MMMM [a las] HH:mm') }}
                        </p>
                        <p class="text-xs text-white/80 dark:text-blue-200 mt-1">
                            {{ $nextAppointment->service->name ?? 'Consulta médica' }}
                        </p>
                    </div>
                @else
                    <div class="bg-white/20 dark:bg-white/10 rounded-lg p-3 mt-4">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-4 h-4 text-black dark:text-black" />
                            <span class="text-sm text-black dark:text-black">No tienes citas programadas</span>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="hidden md:block">
                <x-heroicon-o-sparkles class="w-16 h-16 text-white/50 dark:text-blue-200" />
            </div>
        </div>
    </div>
</x-filament-widgets::widget>