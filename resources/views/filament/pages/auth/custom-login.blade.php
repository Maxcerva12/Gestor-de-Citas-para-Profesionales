<x-filament-panels::page.simple>
    @push('styles')
        <style>
            /* Variables para tema odontológico suave y limpio */
            :root {
                --dental-primary: #06b6d4;
                --dental-secondary: #0891b2;
                --dental-accent: #22d3ee;
                --dental-bg: #f8fafc;
                --dental-white: #ffffff;
                --dental-text-dark: #1e293b;
                --dental-text-light: #64748b;
                --dental-text-muted: #94a3b8;
                --dental-border: #e2e8f0;
                --dental-shadow: rgba(6, 182, 212, 0.1);
                --dental-shadow-lg: rgba(6, 182, 212, 0.15);
                --dental-success: #10b981;
                --dental-danger: #ef4444;
                --dental-panel-bg: #f0fdf4;
                --dental-panel-secondary: #ecfdf5;
                /* Colores específicos para texto del panel */
                --dental-panel-title-color: #0c4a6e;
                --dental-panel-text-color: #0e7490;
            }

            [data-theme="dark"] {
                --dental-primary: #22d3ee;
                --dental-secondary: #06b6d4;
                --dental-accent: #67e8f9;
                --dental-bg: #0f172a;
                --dental-white: #1e293b;
                --dental-text-dark: #f1f5f9;
                --dental-text-light: #cbd5e1;
                --dental-text-muted: #94a3b8;
                --dental-border: #334155;
                --dental-shadow: rgba(34, 211, 238, 0.2);
                --dental-shadow-lg: rgba(34, 211, 238, 0.25);
                --dental-success: #34ce57;
                --dental-danger: #ff6b6b;
                --dental-panel-bg: #0c4a6e;
                --dental-panel-secondary: #075985;
                /* Colores específicos para texto del panel en modo oscuro */
                --dental-panel-title-color: #f0f9ff;
                --dental-panel-text-color: #e0f2fe;
            }

            @media (prefers-color-scheme: dark) {
                :root:not([data-theme="light"]) {
                    --dental-primary: #22d3ee;
                    --dental-secondary: #06b6d4;
                    --dental-accent: #67e8f9;
                    --dental-bg: #0f172a;
                    --dental-white: #1e293b;
                    --dental-text-dark: #f1f5f9;
                    --dental-text-light: #cbd5e1;
                    --dental-text-muted: #94a3b8;
                    --dental-border: #334155;
                    --dental-shadow: rgba(34, 211, 238, 0.2);
                    --dental-shadow-lg: rgba(34, 211, 238, 0.25);
                    --dental-success: #34ce57;
                    --dental-danger: #ff6b6b;
                    --dental-panel-bg: #0c4a6e;
                    --dental-panel-secondary: #075985;
                    /* Colores específicos para texto del panel en modo oscuro */
                    --dental-panel-title-color: #f0f9ff;
                    --dental-panel-text-color: #e0f2fe;
                }
            }

            /* Reset completo */
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: var(--dental-bg);
                line-height: 1.6;
                color: var(--dental-text-dark);
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .fi-simple-header {
                display: none !important; /* Ocultar header de Filament */
            }

            /* Limpiar estilos de Filament completamente */
            .fi-simple-main {
                background: transparent !important;
                min-height: 100vh !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .fi-simple-page {
                background: transparent !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }

            /* Contenedor principal de dos paneles */
            .dental-login-container {
                min-height: 100vh;
                display: flex;
                background: var(--dental-bg);
            }

            /* Panel izquierdo con ilustración - Slider Container */
            .dental-left-panel {
                flex: 1;
                background: linear-gradient(135deg, var(--dental-panel-bg) 0%, var(--dental-panel-secondary) 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem;
                position: relative;
                overflow: hidden;
            }

            /* Slides */
            .slider-container {
                position: relative;
                height: 100%;
                width: 100%;
            }

            .slider-slide {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                transition: opacity 1s ease-in-out, transform 0.8s ease-in-out;
                transform: translateX(30px);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 2rem;
                text-align: center;
            }

            .slider-slide.active {
                opacity: 1;
                transform: translateX(0);
            }

            .slider-slide.next {
                transform: translateX(-30px);
            }

            /* Enhanced SVG styling for prominence */
            .dental-icon {
                width: 180px;
                height: 180px;
                margin-bottom: 2rem;
                filter: drop-shadow(0 20px 40px rgba(6, 182, 212, 0.3));
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-10px);
                }
            }

            /* Progress indicators */
            .slider-dots {
                position: absolute;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 0.75rem;
                z-index: 10;
            }

            .slider-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .slider-dot.active {
                background-color: var(--dental-accent);
                transform: scale(1.2);
                box-shadow: 0 0 20px rgba(103, 232, 249, 0.5);
            }

            .slider-dot:hover {
                background-color: rgba(255, 255, 255, 0.6);
                transform: scale(1.1);
            }

            /* Decoraciones del panel izquierdo */
            .dental-left-panel::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: 
                    radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
                pointer-events: none;
            }

            .dental-panel-title {
                font-size: 2.5rem;
                font-weight: 800;
                color: var(--dental-panel-title-color);
                margin-bottom: 1rem;
                letter-spacing: -0.02em;
                text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .dental-panel-subtitle {
                font-size: 1.25rem;
                color: var(--dental-panel-text-color);
                margin-bottom: 2rem;
                font-weight: 500;
            }

            .dental-panel-description {
                font-size: 1rem;
                color: var(--dental-panel-text-color);
                line-height: 1.6;
                max-width: 400px;
                margin: 0 auto;
                text-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            }

            /* Panel derecho con formulario */
            .dental-right-panel {
                flex: 1;
                background: var(--dental-white);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem;
                min-height: 100vh;
            }

            .dental-form-container {
                width: 100%;
                max-width: 420px;
                animation: fadeInRight 0.8s ease-out;
            }

            @keyframes fadeInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            /* Header del formulario */
            .dental-form-header {
                text-align: center;
                margin-bottom: 3rem;
            }

            .dental-form-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--dental-text-dark);
                margin-bottom: 0.5rem;
                letter-spacing: -0.025em;
            }

            .dental-form-subtitle {
                font-size: 1rem;
                color: var(--dental-text-light);
                font-weight: 500;
            }



            /* Responsive */
            @media (max-width: 1024px) {
                .dental-left-panel {
                    display: none;
                }
                
                .dental-right-panel {
                    flex: none;
                    width: 100%;
                }
            }

            @media (max-width: 640px) {
                .dental-right-panel {
                    padding: 2rem 1.5rem;
                }
                
                .dental-form-title {
                    font-size: 1.75rem;
                }

                .dental-panel-title {
                    font-size: 2rem;
                }

               
            }

           

            /* Accesibilidad */
            @media (prefers-reduced-motion: reduce) {
                * {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            }
        </style>
    @endpush

    <div class="dental-login-container">
        <!-- Panel Izquierdo con Slider de Ilustraciones -->
        <div class="dental-left-panel">
            <div class="slider-container">
                <!-- Slide 1: Sistema Digital -->
            <div class="slider-slide active">
                <div class="dental-icon">
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="screenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#0284c7;stop-opacity:1" />
                            </linearGradient>
                            <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
                                <feDropShadow dx="2" dy="4" stdDeviation="3" flood-color="#0284c7" flood-opacity="0.3"/>
                            </filter>
                        </defs>
                        <circle cx="100" cy="60" r="30" fill="#67e8f9" opacity="0.4"/>
                        <rect x="35" y="85" width="130" height="85" rx="12" fill="url(#screenGrad)" filter="url(#shadow)"/>
                        <rect x="45" y="95" width="110" height="65" rx="8" fill="white"/>
                        <rect x="55" y="105" width="90" height="4" rx="2" fill="#0ea5e9"/>
                        <rect x="55" y="115" width="70" height="3" rx="1.5" fill="#64748b"/>
                        <rect x="55" y="125" width="80" height="3" rx="1.5" fill="#64748b"/>
                        <rect x="55" y="135" width="60" height="3" rx="1.5" fill="#64748b"/>
                        <rect x="55" y="145" width="75" height="3" rx="1.5" fill="#0ea5e9"/>
                        <rect x="125" y="125" width="6" height="15" rx="2" fill="#10b981"/>
                        <rect x="133" y="115" width="6" height="25" rx="2" fill="#0ea5e9"/>
                        <rect x="141" y="120" width="6" height="20" rx="2" fill="#06b6d4"/>
                        <rect x="85" y="170" width="30" height="8" rx="4" fill="#0284c7"/>
                        <ellipse cx="100" cy="185" rx="25" ry="8" fill="#06b6d4"/>
                        <circle cx="70" cy="45" r="3" fill="#10b981"/>
                        <circle cx="130" cy="45" r="3" fill="#0ea5e9"/>
                        <path d="M90 35 Q100 30 110 35" stroke="#06b6d4" stroke-width="2" fill="none" opacity="0.6"/>
                        <path d="M85 30 Q100 20 115 30" stroke="#06b6d4" stroke-width="1.5" fill="none" opacity="0.4"/>
                    </svg>
                </div>
                <h1 class="dental-panel-title">CONTROL DIGITAL</h1>
                <p class="dental-panel-description">
                    Historiales digitales completos y gestión automatizada de pacientes
                </p>
            </div>

                <!-- Slide 2: Tratamientos -->
                <div class="slider-slide">
                    <div class="dental-icon">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <!-- Fondo circular suave -->
                            <circle cx="100" cy="100" r="80" fill="#f0f9ff" opacity="0.6"/>
                            
                            <!-- Diente principal - forma más simple y moderna -->
                            <path d="M100 50 
                                    C115 50, 130 60, 130 75
                                    C130 90, 130 105, 125 120
                                    C120 135, 110 145, 100 150
                                    C90 145, 80 135, 75 120
                                    C70 105, 70 90, 70 75
                                    C70 60, 85 50, 100 50 Z" 
                                    fill="white" 
                                    stroke="#22d3ee" 
                                    stroke-width="3"/>
                            
                            <!-- Cruz médica en el diente -->
                            <rect x="95" y="75" width="10" height="25" rx="2" fill="#06b6d4"/>
                            <rect x="87" y="83" width="26" height="9" rx="2" fill="#06b6d4"/>
                            
                            <!-- Puntos decorativos -->
                            <circle cx="60" cy="70" r="4" fill="#67e8f9" opacity="0.8"/>
                            <circle cx="140" cy="80" r="3" fill="#22d3ee" opacity="0.8"/>
                            <circle cx="50" cy="130" r="5" fill="#0891b2" opacity="0.6"/>
                            <circle cx="150" cy="120" r="4" fill="#67e8f9" opacity="0.8"/>
                            
                            <!-- Líneas suaves de fondo -->
                            <path d="M30 160 Q100 140 170 160" stroke="#e0f7fa" stroke-width="2" fill="none" opacity="0.5"/>
                            <path d="M40 40 Q100 60 160 40" stroke="#e0f7fa" stroke-width="2" fill="none" opacity="0.5"/>
                        </svg>

                    </div>
                    <h1 class="dental-panel-title">TRATAMIENTOS</h1>
                    <p class="dental-panel-description">
                        Planes de tratamiento personalizados y seguimiento profesional
                    </p>
                </div>

                <!-- Slide 3: Fundación -->
<div class="slider-slide">
    <div class="dental-icon">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <!-- Building structure -->
            <rect x="60" y="80" width="80" height="90" rx="8" fill="#67e8f9" opacity="0.9"/>
            <rect x="50" y="75" width="100" height="95" rx="10" fill="#22d3ee" opacity="0.8"/>
            
            <!-- Windows -->
            <rect x="70" y="95" width="15" height="15" rx="2" fill="white"/>
            <rect x="90" y="95" width="15" height="15" rx="2" fill="white"/>
            <rect x="110" y="95" width="15" height="15" rx="2" fill="white"/>
            
            <!-- Door -->
            <rect x="85" y="135" width="30" height="35" rx="4" fill="white"/>
            <circle cx="108" cy="152" r="2" fill="#06b6d4"/>
            
            <!-- Medical cross on top -->
            <circle cx="100" cy="50" r="20" fill="#06b6d4"/>
            <rect x="95" y="40" width="10" height="20" fill="white"/>
            <rect x="90" y="45" width="20" height="10" fill="white"/>
            
            <!-- Foundation name -->
            <text x="100" y="185" text-anchor="middle" fill="#06b6d4" font-size="10" font-weight="bold">Zoila Padilla</text>
        </svg>
    </div>
    <h1 class="dental-panel-title">FUNDACIÓN</h1>
    <p class="dental-panel-description">
        Comprometidos con la salud dental y el bienestar de nuestra comunidad
    </p>
</div>
            </div>

            <!-- Indicadores de progreso -->
            <div class="slider-dots">
                <div class="slider-dot active"></div>
                <div class="slider-dot"></div>
                <div class="slider-dot"></div>
            </div>
        </div>

        <!-- Panel Derecho con Formulario -->
        <div class="dental-right-panel">
            <div class="dental-form-container">
                <!-- Header del Formulario -->
                <div class="dental-form-header">
                    <h2 class="dental-form-title">Entre a su cuenta</h2>
                    <p class="dental-form-subtitle">Acceda a su panel administrativo</p>
                </div>

                <!-- Formulario de Login -->
                <x-filament-panels::form wire:submit="authenticate">
                    {{ $this->form }}
                    
                    <div style="margin-top: 1.5rem;">
                        <x-filament-panels::form.actions 
                            :actions="$this->getCachedFormActions()"
                            :full-width="true"
                        />
                    </div>
                </x-filament-panels::form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Detección de tema
                function detectTheme() {
                    const theme = localStorage.getItem('theme') || 
                                  (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                    document.documentElement.setAttribute('data-theme', theme);
                }

                detectTheme();

                // Escuchar cambios de tema del sistema
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                    if (!localStorage.getItem('theme')) {
                        document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                    }
                });

                // Slider automático
                let currentSlide = 0;
                const slides = document.querySelectorAll('.slider-slide');
                const dots = document.querySelectorAll('.slider-dot');
                const totalSlides = slides.length;

                function showSlide(index) {
                    // Remover clase active de todos los slides y dots
                    slides.forEach(slide => slide.classList.remove('active', 'next'));
                    dots.forEach(dot => dot.classList.remove('active'));
                    
                    // Agregar clase active al slide y dot actual
                    slides[index].classList.add('active');
                    dots[index].classList.add('active');
                    
                    // Agregar clase next al slide anterior para efecto de salida
                    const prevIndex = (index - 1 + totalSlides) % totalSlides;
                    slides[prevIndex].classList.add('next');
                }

                function nextSlide() {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    showSlide(currentSlide);
                }

                // Iniciar slider automático
                setInterval(nextSlide, 4000); // Cambiar cada 4 segundos

                // Permitir navegación manual con dots
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        currentSlide = index;
                        showSlide(currentSlide);
                    });
                });

                // Mejorar experiencia de inputs
                const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.style.transform = 'translateY(-1px)';
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.style.transform = 'translateY(0)';
                    });
                });

                // Efecto en el botón
                const btn = document.querySelector('.fi-btn-primary');
                if (btn) {
                    btn.addEventListener('click', function() {
                        this.style.transform = 'translateY(0) scale(0.98)';
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 150);
                    });
                }
            });
        </script>
    @endpush
</x-filament-panels::page.simple>
