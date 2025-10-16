<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->settingsForm }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de configuración de facturas cargada - Configurando sidebar colapsado');
            
            function collapseSidebar() {
                // Buscar el botón de toggle del sidebar
                const toggleButton = document.querySelector('button[x-on\\:click*="sidebar"]') || 
                                   document.querySelector('[data-sidebar-toggle]') ||
                                   document.querySelector('.fi-sidebar-toggle') ||
                                   document.querySelector('button[aria-label*="Toggle navigation"]') ||
                                   document.querySelector('button[aria-label*="sidebar"]');
                
                const sidebar = document.querySelector('.fi-sidebar');
                
                if (toggleButton && sidebar) {
                    // Verificar si el sidebar NO está colapsado
                    const isExpanded = !sidebar.classList.contains('fi-sidebar-collapsed') ||
                                     sidebar.classList.contains('lg:w-64') ||
                                     getComputedStyle(sidebar).width !== '5rem';
                    
                    if (isExpanded) {
                        console.log('Sidebar expandido detectado, colapsando...');
                        toggleButton.click();
                    } else {
                        console.log('Sidebar ya está colapsado');
                    }
                }
                
                // También intentar con Alpine.js store si existe
                if (window.Alpine && window.Alpine.store && window.Alpine.store("sidebar")) {
                    if (window.Alpine.store("sidebar").isOpen) {
                        window.Alpine.store("sidebar").isOpen = false;
                        console.log('Sidebar colapsado via Alpine store');
                    }
                }
            }
            
            // Ejecutar después de que el DOM esté completamente cargado
            setTimeout(collapseSidebar, 100);
            setTimeout(collapseSidebar, 300);
            setTimeout(collapseSidebar, 500);
            
            // Ejecutar cuando Alpine.js esté listo
            document.addEventListener('alpine:init', function() {
                setTimeout(collapseSidebar, 100);
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        /* Estilos para mejorar la apariencia cuando el sidebar está colapsado */
        .fi-sidebar-collapsed .fi-sidebar-nav-item-label,
        .fi-sidebar-collapsed .fi-sidebar-nav-group-label {
            opacity: 0;
            visibility: hidden;
        }
        
        .fi-sidebar-collapsed .fi-sidebar-nav-item {
            justify-content: center;
        }
        
        .fi-sidebar-collapsed .fi-sidebar-brand {
            justify-content: center;
        }
        
        /* Asegurar que los iconos sean visibles cuando está colapsado */
        .fi-sidebar-collapsed .fi-sidebar-nav-item-icon {
            margin-right: 0;
        }
        
        /* Mejorar el espaciado del contenido principal */
        .fi-main {
            transition: margin-left 0.3s ease;
        }
    </style>
    @endpush
</x-filament-panels::page>
