<div class="p-6 space-y-6" x-data="{
    copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'></path></svg>';
            button.classList.add('bg-green-500', 'text-white');
            
            const notification = this.$refs.notification;
            notification.classList.remove('translate-y-20', 'opacity-0');
            notification.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                notification.classList.remove('translate-y-0', 'opacity-100');
                notification.classList.add('translate-y-20', 'opacity-0');
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-500', 'text-white');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar:', err);
        });
    }
}">
    <!-- Encabezado con avatar - Mejor espaciado -->
    <div class="flex items-center space-x-5 pb-4 border-b border-gray-200 dark:border-gray-700">
        <img 
            src="{{ $user->avatar_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name . ' ' . $user->last_name) . '&color=FFFFFF&background=6366F1&bold=true&size=256' }}" 
            alt="{{ $user->name }}"
            class="w-16 h-16 rounded-full"
        >
        <div style="margin-left: 1rem">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-black">
                {{ $user->name }} {{ $user->last_name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $user->profession }} @if($user->especialty) - {{ $user->especialty }} @endif
            </p>
        </div>
    </div>

    <!-- Información de contacto -->
    <div class="space-y-4">
        <!-- Email -->
        @if($user->email)
        <div class="flex items-start space-x-4 p-4 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-200 dark:border-blue-900">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-blue-900 dark:text-blue-300 uppercase tracking-wider mb-1">
                    Correo Electrónico
                </p>
                <div class="flex items-center gap-2">
                    <a href="mailto:{{ $user->email }}" class="text-sm text-blue-700 dark:text-blue-400 hover:underline break-all">
                        {{ $user->email }}
                    </a>
                    <button 
                        @click="copyToClipboard('{{ $user->email }}', $event.target)"
                        class="flex-shrink-0 p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 rounded transition-colors"
                        title="Copiar correo"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Teléfono -->
        @if($user->phone)
        <div class="flex items-start space-x-4 p-4 bg-green-50 dark:bg-green-950/30 rounded-lg border border-green-200 dark:border-green-900">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            <div class="flex-1">
                <p class="text-xs font-medium text-green-900 dark:text-green-300 uppercase tracking-wider mb-1">
                    Teléfono
                </p>
                <div class="flex items-center gap-2">
                    <a href="tel:{{ $user->phone }}" class="text-sm text-green-700 dark:text-green-400 hover:underline">
                        {{ $user->phone }}
                    </a>
                    <button 
                        @click="copyToClipboard('{{ $user->phone }}', $event.target)"
                        class="flex-shrink-0 p-1.5 text-green-600 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-800 rounded transition-colors"
                        title="Copiar teléfono"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Ubicación -->
        @if($user->city || $user->country)
        <div class="flex items-start space-x-4 p-4 bg-purple-50 dark:bg-purple-950/30 rounded-lg border border-purple-200 dark:border-purple-900">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="text-xs font-medium text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-1">
                    Ubicación
                </p>
                <p class="text-sm text-purple-700 dark:text-purple-400">
                    @if($user->city){{ $user->city }}@endif
                    @if($user->city && $user->country), @endif
                    @if($user->country){{ $user->country }}@endif
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Nota informativa -->
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-start space-x-2">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                Puedes contactar directamente al profesional usando la información proporcionada. Para agendar una cita, utiliza el sistema de agendamiento.
            </p>
        </div>
    </div>

    <!-- Notificación de copiado -->
    <div x-ref="notification" class="fixed bottom-4 right-4 transform translate-y-20 opacity-0 transition-all duration-300 ease-out z-50">
        <div class="bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 px-6 py-3 rounded-lg shadow-2xl flex items-center space-x-3">
            <svg class="w-5 h-5 text-green-400 dark:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm font-medium">¡Copiado!</span>
        </div>
    </div>
</div>
