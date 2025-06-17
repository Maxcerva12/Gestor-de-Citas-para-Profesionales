<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ProAppointments - Professional Appointment Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|inter:300,400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.5/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        
        .slide-in {
            animation: slide-in 0.5s ease-out;
        }
        
        @keyframes slide-in {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
            100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Instrument Sans', 'sans-serif'],
                    },
                }
            }
        }
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('app', () => ({
                darkMode: false,
                mobileMenuOpen: false,
                testimonials: [
                    {
                        name: 'Sarah Johnson',
                        role: 'Therapist',
                        image: '/api/placeholder/64/64',
                        content: 'ProAppointments has reduced my no-shows by 75% and saves me hours every week on scheduling tasks.'
                    },
                    {
                        name: 'Michael Chen',
                        role: 'Financial Advisor',
                        image: '/api/placeholder/64/64',
                        content: 'The client portal and automated reminders have transformed my practice. My clients love the ease of scheduling.'
                    },
                    {
                        name: 'Alicia Rodriguez',
                        role: 'Legal Consultant',
                        image: '/api/placeholder/64/64',
                        content: 'I can manage my complex schedule across multiple locations seamlessly. The ROI has been incredible.'
                    }
                ],
                currentTestimonial: 0,

                init() {
                    if (localStorage.getItem('darkMode') === 'true' || 
                        (!('darkMode' in localStorage) && 
                         window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.darkMode = true;
                        document.documentElement.classList.add('dark');
                    }
                    
                    setInterval(() => {
                        this.currentTestimonial = (this.currentTestimonial + 1) % this.testimonials.length;
                    }, 5000);
                },
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }));
        });
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-100" x-data="app">
    <!-- Header -->
    <header class="w-full border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 sticky top-0 z-50 transition-all duration-200" 
            x-bind:class="{ 'shadow-md': window.pageYOffset > 0 }"
            @scroll.window="navbarShadow = (window.pageYOffset > 0)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-600 dark:bg-indigo-500 rounded-lg p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-display font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">Gestor Médico</span>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Características</a>
                    <a href="#testimonials" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Testimonios</a>
                    <a href="#pricing" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Precios</a>
                    <a href="#contact" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contacto</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-4">
                    <button @click="toggleDarkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    <a href="/admin" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Log in</a>
                 
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleDarkMode" class="p-2 mr-4 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </nav>
        </div>
        
        <!-- Mobile Navigation -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4">
            <div class="pt-2 pb-4 px-4 space-y-1">
                <a href="#features" @click="mobileMenuOpen = false" class="block py-2 px-4 text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition">Características</a>
                <a href="#testimonials" @click="mobileMenuOpen = false" class="block py-2 px-4 text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition">Testimonios</a>
                <a href="#pricing" @click="mobileMenuOpen = false" class="block py-2 px-4 text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition">Precios</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="block py-2 px-4 text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition">Contacto</a>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-800 mt-4">
                    <a href="/login" class="block py-2 px-4 text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition">Log in</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative w-full bg-gradient-to-br from-white to-indigo-50 dark:from-gray-900 dark:to-gray-800 overflow-hidden">
        <div class="absolute inset-0 bg-grid-indigo-100 dark:bg-grid-indigo-900 bg-opacity-50"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Con la confianza de más de 10.000 profesionales
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-bold tracking-tight">
                        Administrar citas <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">Con confianza</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300">
                        Optimiza tu agenda, reduce las inasistencias y concéntrate en lo que más importa. El sistema de citas integral diseñado para profesionales.                    </p>
                    
                    <div class="pt-4">
                        <div class="flex items-center">
                            {{-- <div class="flex -space-x-2">
                                <img src="/api/placeholder/32/32" alt="User" class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800">
                                <img src="/api/placeholder/32/32" alt="User" class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800">
                                <img src="/api/placeholder/32/32" alt="User" class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800">
                                <img src="/api/placeholder/32/32" alt="User" class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800">
                            </div> --}}
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-medium text-gray-900 dark:text-white">4.9/5</span> de más de 1200 reseñas
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl blur opacity-30 dark:opacity-40 animate-pulse"></div>
                    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="bg-gray-100 dark:bg-gray-700 px-4 py-2 flex items-center space-x-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">ProAppointments Dashboard</div>
                        </div>
                        <img src="https://images.ctfassets.net/pdf29us7flmy/50kVKgdwULKaOgvkJBRic5/68591241ae297e886978aa9f17d16e00/resized.png?w=1440&q=100&fm=avif" alt="ProAppointments Dashboard" class="w-full h-64 sm:h-80 object-cover object-top">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Companies banner -->
        <div class="relative border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-6">Con la confianza de empresas líderes a nivel mundial</p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 justify-items-center items-center">
                    <div class="h-8 text-gray-400 dark:text-gray-500">ECOPETROL</div>
                    <div class="h-8 text-gray-400 dark:text-gray-500">BANCOLOMBIA</div>
                    <div class="h-8 text-gray-400 dark:text-gray-500">GRUPO NUTRESA</div>
                    <div class="h-8 text-gray-400 dark:text-gray-500">GRUPO ÉXITO</div>
                    <div class="h-8 text-gray-400 dark:text-gray-500">AVIANCA</div>
                    <div class="h-8 text-gray-400 dark:text-gray-500">JUAN VALDEZ</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="w-full py-16 md:py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold font-display mb-4">Todas Las Herramientas Que Necesitas</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">Optimiza tu flujo de trabajo con nuestras funciones completas diseñadas específicamente para profesionales.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 shadow-sm hover:shadow-md transition border border-gray-100 dark:border-gray-700" 
                     x-data="{hover: false}" 
                     @mouseenter="hover = true" 
                     @mouseleave="hover = false">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg inline-block mb-6" 
                         x-bind:class="{ 'bg-indigo-200 dark:bg-indigo-900': hover }">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Programación Inteligente</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Crea páginas de reserva personalizables, establece tiempos de espera y gestiona tu disponibilidad en múltiples ubicaciones y servicios.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Ventanas de reserva personalizadas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Tiempo de margen entre citas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Soporte para múltiples ubicaciones</span>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-8 shadow-md border border-indigo-100 dark:border-indigo-800/30"
                     x-data="{hover: false}" 
                     @mouseenter="hover = true" 
                     @mouseleave="hover = false">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg inline-block mb-6"
                         x-bind:class="{ 'bg-indigo-200 dark:bg-indigo-900': hover }">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Recordatorios Inteligentes</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Reduce las inasistencias con recordatorios automáticos y personalizables que mantienen a tus clientes informados y preparados.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Notificaciones por email, SMS y push</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Frecuencia y tiempo personalizado</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Contenido personalizado y marca</span>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 shadow-sm hover:shadow-md transition border border-gray-100 dark:border-gray-700"
                     x-data="{hover: false}" 
                     @mouseenter="hover = true" 
                     @mouseleave="hover = false">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg inline-block mb-6"
                         x-bind:class="{ 'bg-indigo-200 dark:bg-indigo-900': hover }">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10m6 0V9m0 10h2a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v8" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Análisis de Negocio</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Rastrea métricas clave con análisis potentes que te ayudan a optimizar tu agenda y hacer crecer tu negocio.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Informes completos</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Seguimiento de inasistencias y cancelaciones</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Información de ingresos y utilización</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- More Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 flex items-start space-x-4 hover:shadow-sm transition">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Seguridad Avanzada</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Seguridad de nivel empresarial con cifrado de datos y cumplimiento de estándares de la industria para proteger tu información y la de tus clientes.</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 flex items-start space-x-4 hover:shadow-sm transition">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Pagos Integrados</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Acepta depósitos, procesa pagos completos o cobra tarifas de cancelación directamente a través de la plataforma con múltiples opciones de pago.</p>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="w-full py-16 md:py-24 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl font-bold font-display mb-4">Amado por Profesionales</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">No confíe sólo en nuestra palabra. Vea lo que otros profesionales dicen sobre ProAppointments.</p>
            </div>
            
            <div class="relative">
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700 relative">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 bg-indigo-500 text-white rounded-full p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        
                        <div x-show="currentTestimonial === 0" class="slide-in" style="height: 300px">
                            <svg class="w-10 h-10 text-indigo-200 dark:text-indigo-800 mb-6" fill="currentColor" viewBox="0 0 32 32">
                                <path d="M9.352 4C4.582 7.552 1.428 13.548 1.428 20.295c0 3.19 1.816 5.497 4.75 5.497 2.5 0 4.618-1.913 4.618-4.687 0-2.51-1.553-4.488-3.862-4.488-0.213 0-0.636 0-0.85 0.111 0.85-3.060 3.434-7.121 6.655-9.43L9.352 4zM25.002 4c-4.771 3.552-7.924 9.548-7.924 16.295 0 3.19 1.816 5.497 4.75 5.497 2.5 0 4.618-1.913 4.618-4.687 0-2.51-1.553-4.488-3.861-4.488-0.213 0-0.637 0-0.851 0.111 0.85-3.060 3.435-7.121 6.656-9.43L25.002 4z"/>
                            </svg>
                            
                            <p class="text-xl text-gray-700 dark:text-gray-200 font-medium italic mb-8">
                                ProAppointments ha reducido mis inasistencias en un 75% y me ahorra horas cada semana en tareas de programación. Sus recordatorios automáticos han revolucionado mi práctica.
                            </p>
                            
                            <div class="flex items-center">
                               
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Sarah Johnson</h4>
                                    <p class="text-gray-600 dark:text-gray-400">Terapeuta</p>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="currentTestimonial === 1" class="slide-in" style="height: 300px">
                            <svg class="w-10 h-10 text-indigo-200 dark:text-indigo-800 mb-6" fill="currentColor" viewBox="0 0 32 32">
                                <path d="M9.352 4C4.582 7.552 1.428 13.548 1.428 20.295c0 3.19 1.816 5.497 4.75 5.497 2.5 0 4.618-1.913 4.618-4.687 0-2.51-1.553-4.488-3.862-4.488-0.213 0-0.636 0-0.85 0.111 0.85-3.060 3.434-7.121 6.655-9.43L9.352 4zM25.002 4c-4.771 3.552-7.924 9.548-7.924 16.295 0 3.19 1.816 5.497 4.75 5.497 2.5 0 4.618-1.913 4.618-4.687 0-2.51-1.553-4.488-3.861-4.488-0.213 0-0.637 0-0.851 0.111 0.85-3.060 3.435-7.121 6.656-9.43L25.002 4z"/>
                            </svg>
                            
                            <p class="text-xl text-gray-700 dark:text-gray-200 font-medium italic mb-8">
                                El portal para clientes y los recordatorios automáticos han transformado mi práctica. A mis clientes les encanta la facilidad de programar citas y yo aprecio la impresión profesional que crea.
                            </p>
                            
                            <div class="flex items-center">
                                
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Michael Chen</h4>
                                    <p class="text-gray-600 dark:text-gray-400">Asesor Financiero</p>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="currentTestimonial === 2" class="slide-in" style="height: 300px">
                            <svg class="w-10 h-10 text-indigo-200 dark:text-indigo-800 mb-6" fill="currentColor" viewBox="0 0 32 32">
                                <path d="M9.352 4C4.582 7.552 1.428 13.548 1.428 20.295c0 3.19 1.816 5.497 4.75 5.497 2.5 0 4.618-1.913 4.618-4.687 0-2.51-1.553-4.488-3.862-4.488-0.213 0-0.636 0-0.85 0.111 0.85-3.060 3.434-7.121 6.655-9.43L9.352 4zM25.002 4c-4.771 3.552-7.924 9.548-7.924 16.295 0 3.19 1.816 5.497 4.75 5.497 2.5 0 4.618-1.913 4.618-4.687 0-2.51-1.553-4.488-3.861-4.488-0.213 0-0.637 0-0.851 0.111 0.85-3.060 3.435-7.121 6.656-9.43L25.002 4z"/>
                            </svg>
                            
                            <p class="text-xl text-gray-700 dark:text-gray-200 font-medium italic mb-8">
                                Puedo gestionar mi compleja agenda en múltiples ubicaciones sin problemas. El retorno de inversión ha sido increíble — recuperé el costo de mi suscripción anual en solo el primer mes.
                            </p>
                            
                            <div class="flex items-center">
                               
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Alicia Rodriguez</h4>
                                    <p class="text-gray-600 dark:text-gray-400">Consultora Legal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center mt-6 space-x-2">
                    <button @click="currentTestimonial = 0" class="w-3 h-3 rounded-full" 
                            :class="currentTestimonial === 0 ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-gray-300 dark:bg-gray-600'"></button>
                    <button @click="currentTestimonial = 1" class="w-3 h-3 rounded-full" 
                            :class="currentTestimonial === 1 ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-gray-300 dark:bg-gray-600'"></button>
                    <button @click="currentTestimonial = 2" class="w-3 h-3 rounded-full" 
                            :class="currentTestimonial === 2 ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-gray-300 dark:bg-gray-600'"></button>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">96%</div>
                    <p class="text-gray-600 dark:text-gray-300">Reducción en carga de trabajo de programación</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">75%</div>
                    <p class="text-gray-600 dark:text-gray-300">Menos inasistencias a citas</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">10k+</div>
                    <p class="text-gray-600 dark:text-gray-300">Profesionales satisfechos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    {{-- <section id="pricing" class="w-full py-16 md:py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold font-display mb-4">Simple, Transparent Pricing</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">Choose the plan that's right for your business. All plans include a 14-day free trial.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Starter Plan -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-md transition duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-1">Starter</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Perfect for solo practitioners</p>
                        <div class="flex items-baseline mb-6">
                            <span class="text-4xl font-bold">$19</span>
                            <span class="text-gray-500 dark:text-gray-400 ml-2">/month</span>
                        </div>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Up to 100 appointments/month</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Email reminders</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Basic reporting</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>1 user account</span>
                            </li>
                        </ul>
                        <a href="/register" class="block w-full py-3 px-4 text-center font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition shadow-sm">
                            Start Free Trial
                        </a>
                    </div>
                </div>
                
                <!-- Professional Plan -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden border-2 border-indigo-500 dark:border-indigo-400 transform hover:-translate-y-1 transition duration-300">
                    <div class="absolute inset-x-0 -top-px h-0.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    <div class="bg-indigo-500 dark:bg-indigo-600 text-white px-6 py-2">
                        <span class="text-xs font-semibold uppercase tracking-wider">Most Popular</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-1">Professional</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Ideal for growing practices</p>
                        <div class="flex items-baseline mb-6">
                            <span class="text-4xl font-bold">$49</span>
                            <span class="text-gray-500 dark:text-gray-400 ml-2">/month</span>
                        </div>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Unlimited appointments</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Email & SMS reminders</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Advanced analytics</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Up to 5 user accounts</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Payment processing</span>
                            </li>
                        </ul>
                        <a href="/register" class="block w-full py-3 px-4 text-center font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition shadow-md pulse">
                            Start Free Trial
                        </a>
                    </div>
                </div>
                
                <!-- Business Plan -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-md transition duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-1">Business</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">For multi-location businesses</p>
                        <div class="flex items-baseline mb-6">
                            <span class="text-4xl font-bold">$99</span>
                            <span class="text-gray-500 dark:text-gray-400 ml-2">/month</span>
                        </div>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Everything in Professional</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Multiple locations</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Unlimited user accounts</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>API access</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Dedicated support</span>
                            </li>
                        </ul>
                        <a href="/register" class="block w-full py-3 px-4 text-center font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition shadow-sm">
                            Start Free Trial
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 bg-gray-50 dark:bg-gray-800 rounded-xl p-8 border border-gray-100 dark:border-gray-700 text-center">
                <h3 class="text-xl font-semibold mb-4">Need a custom solution?</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 max-w-2xl mx-auto">
                    We offer tailored enterprise solutions for larger organizations with specific requirements. Our team will work with you to create a custom plan.
                </p>
                <a href="#contact" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm">
                    Contact Sales
                </a>
            </div>
        </div>
    </section> --}}

    <!-- Contact Section -->
    {{-- <section id="contact" class="w-full py-16 md:py-24 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold font-display mb-4">Get In Touch</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">Have questions or need more information? Our team is here to help.</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="bg-white dark:bg-gray-900 rounded-xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                    <form>
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                            <input type="text" id="name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 dark:bg-gray-800 transition">
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" id="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 dark:bg-gray-800 transition">
                        </div>
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <select id="subject" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 dark:bg-gray-800 transition">
                                <option value="">Select a topic</option>
                                <option value="pricing">Pricing & Plans</option>
                                <option value="features">Features</option>
                                <option value="support">Technical Support</option>
                                <option value="partnership">Partnership Opportunities</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Message</label>
                            <textarea id="message" rows="5" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 dark:bg-gray-800 transition"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 px-4 text-center font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition shadow-sm">
                            Send Message
                        </button>
                    </form>
                </div>
                
                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-start space-x-4">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Email Us</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-2">For general inquiries and support:</p>
                            <a href="mailto:info@proappointments.com" class="text-indigo-600 dark:text-indigo-400 hover:underline">info@proappointments.com</a>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-start space-x-4">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Call Us</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-2">Monday to Friday, 9am to 5pm:</p>
                            <p class="text-indigo-600 dark:text-indigo-400">+1 (555) 123-4567</p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-start space-x-4">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Live Chat</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-2">Available during business hours:</p>
                            <a href="#" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:underline">
                                Start a conversation
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 shadow-sm border border-indigo-100 dark:border-indigo-800/30">
                        <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow-md transition">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
                                </svg>
                            </a>
                            <a href="#" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow-md transition">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow-md transition">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm-1-6.7c-.66 0-1.2-.54-1.2-1.2 0-.65.55-1.2 1.2-1.2.66 0 1.2.55 1.2 1.2 0 .66-.54 1.2-1.2 1.2zM17 17h-2v-3c0-1.85-2-1.7-2 0v3h-2v-6h2v1.2c.58-.7 2-1.35 2 .72V17z"/>
                                </svg>
                            </a>
                            <a href="#" class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow-md transition">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2zm-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6zm9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25zM12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Pie de página -->
    <footer class="w-full bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-indigo-600 dark:bg-indigo-500 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-display font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">Gestor Médico</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Simplificando la gestión de citas para profesionales en todo el mundo desde 2023.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm-1-6.7c-.66 0-1.2-.54-1.2-1.2 0-.65.55-1.2 1.2-1.2.66 0 1.2.55 1.2 1.2 0 .66-.54 1.2-1.2 1.2zM17 17h-2v-3c0-1.85-2-1.7-2 0v3h-2v-6h2v1.2c.58-.7 2-1.35 2 .72V17z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Producto</h3>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Características</a></li>
                        <li><a href="#pricing" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Precios</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Integraciones</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Actualizaciones</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Empresa</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Acerca de</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Blog</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Carreras</a></li>
                        <li><a href="#contact" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contacto</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Recursos</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Centro de Ayuda</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Documentación</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Referencia de API</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Política de Privacidad</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Términos de Servicio</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 mt-8 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    &copy; 2023 Gestor Médico. Todos los derechos reservados.
                </p>
                <div class="flex items-center mt-4 md:mt-0">
                    <button @click="toggleDarkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition text-gray-500 dark:text-gray-400">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>