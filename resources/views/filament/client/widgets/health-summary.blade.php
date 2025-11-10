<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-heart class="w-5 h-5 text-red-500" />
                Resumen de Salud
            </div>
        </x-slot>

        <div class="space-y-6">
            <!-- Completitud del perfil -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Completitud del Perfil Médico</h3>
                    <span class="text-sm font-bold 
                        {{ $profileComplete['percentage'] >= 80 ? 'text-green-600 dark:text-green-400' : ($profileComplete['percentage'] >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                        {{ $profileComplete['percentage'] }}%
                    </span>
                </div>
                
                <!-- Barra de progreso -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-3">
                    <div class="h-2 rounded-full transition-all duration-300
                        {{ $profileComplete['percentage'] >= 80 ? 'bg-green-500' : ($profileComplete['percentage'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                        style="width: {{ $profileComplete['percentage'] }}%">
                    </div>
                </div>
                
                @if(!empty($profileComplete['missing']))
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Falta por completar:</span>
                        {{ implode(', ', $profileComplete['missing']) }}
                    </div>
                @endif
            </div>

            <!-- Información médica en cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tipo de sangre -->
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-beaker class="w-4 h-4 text-red-500" />
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Tipo de Sangre</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $healthData['tipo_sangre'] ?? 'No especificado' }}
                    </p>
                </div>

                <!-- Aseguradora -->
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-shield-check class="w-4 h-4 text-blue-500" />
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Aseguradora</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $healthData['aseguradora'] ?? 'No especificada' }}
                    </p>
                </div>

                <!-- Contacto de emergencia -->
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-phone class="w-4 h-4 text-green-500" />
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Contacto de emergencia</span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $healthData['contacto_emergencia'] ?? 'No especificado' }}
                    </p>
                    @if($healthData['telefono_emergencia'])
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $healthData['telefono_emergencia'] }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Alergias (si existen) -->
            @if($healthData['alergias'])
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-500" />
                        <span class="text-sm font-medium text-amber-800 dark:text-amber-200">Alergias Conocidas</span>
                    </div>
                    <p class="text-sm text-amber-700 dark:text-amber-300">
                        {{ $healthData['alergias'] }}
                    </p>
                </div>
            @endif

            <!-- Acción para completar perfil si está incompleto -->
            @if($profileComplete['percentage'] < 100)
                <div class="bg-[#fff8e1] dark:bg-[#3a2f0b]/20 border border-[#ebb619] dark:border-[#b38f13] rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-[#b38f13] dark:text-[#ebb619]">
                                Completa tu perfil médico
                            </h4>
                            <p class="text-xs text-[#a68e1a] dark:text-[#ebb619] mt-1">
                                Una información médica completa nos ayuda a brindarte un mejor servicio.
                            </p>
                        </div>
                        <x-heroicon-o-arrow-right class="w-4 h-4 text-[#ebb619]" />
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>