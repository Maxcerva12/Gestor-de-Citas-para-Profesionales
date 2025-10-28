<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Fundación Odontológica Zoila Padilla - Atención dental especializada con compromiso social en Ciénaga. Servicios de calidad, prevención y acceso equitativo a la salud oral.">
    <meta name="keywords"
        content="odontología, dental, fundación, Ciénaga, salud oral, prevención dental, tratamientos dentales">
    <meta name="author" content="Fundación Odontológica Zoila Padilla">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Fundación Odontológica Zoila Padilla">
    <meta property="og:description" content="Atención dental especializada con compromiso social">
    <meta property="og:url" content="{{ url('/') }}">

    <title>Fundación Odontológica Zoila Padilla - Atención Dental Especializada</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dental-blue': '#2563eb',
                        'dental-teal': '#0d9488',
                        'dental-green': '#059669',
                        'soft-gray': '#f8fafc',
                        'warm-gray': '#6b7280'
                    },
                    fontFamily: {
                        'display': ['Inter', 'system-ui', 'sans-serif'],
                        'body': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Custom styles and animations -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse-subtle {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .animate-rotate {
            animation: rotate 20s linear infinite;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-pulse-subtle {
            animation: pulse-subtle 3s ease-in-out infinite;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Focus styles for accessibility */
        .focus-ring:focus {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }
    </style>
</head>

<body class="font-body text-gray-900 bg-white antialiased">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" role="banner" id="navbar">
        <div class="bg-gradient-to-br from-blue-50 backdrop-blur-xl shadow-lg shadow-gray-500/5">
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8" aria-label="Navegación principal">
                <div class="flex justify-between items-center h-20">

                    <!-- Logo Section -->
                    <div class="flex-shrink-0">
                        <a href="/" class="group flex items-center space-x-3 focus-ring rounded-xl p-2 -m-2"
                            aria-label="Inicio - Fundación Odontológica Zoila Padilla">
                            <div class="relative">
                                <img src="{{ asset('storage/img/lg_zoila_padilla.svg') }}"
                                    alt="Logo Fundación Odontológica Zoila Padilla"
                                    class="h-10 w-auto md:h-12 transition-all duration-300 group-hover:scale-110">
                            </div>

                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center">
                        <div class="flex items-center space-x-1 bg-gray-50/80 rounded-full p-1 backdrop-blur-sm">
                            <a href="#inicio"
                                class="group relative px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-dental-blue transition-all duration-300 rounded-full hover:bg-white hover:shadow-sm focus-ring">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    <span>Inicio</span>
                                </span>
                            </a>
                            <a href="#sobre-nosotros"
                                class="group relative px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-dental-blue transition-all duration-300 rounded-full hover:bg-white hover:shadow-sm focus-ring">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span>Nosotros</span>
                                </span>
                            </a>
                            <a href="#servicios"
                                class="group relative px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-dental-blue transition-all duration-300 rounded-full hover:bg-white hover:shadow-sm focus-ring">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                        </path>
                                    </svg>
                                    <span>Servicios</span>
                                </span>
                            </a>
                            <a href="#testimonios"
                                class="group relative px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-dental-blue transition-all duration-300 rounded-full hover:bg-white hover:shadow-sm focus-ring">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>
                                    <span>Testimonios</span>
                                </span>
                            </a>

                        </div>
                    </div>

                    <!-- CTA Section -->
                    <div class="hidden md:flex items-center space-x-3">
                        <!-- Primary CTA -->
                        <a href="{{ url('/client') }}"
                            class="inline-flex items-center justify-center px-4 py-2 border-2 border-dental-blue text-dental-blue font-semibold rounded-full hover:bg-dental-blue hover:text-white transition-all duration-300 focus-ring">
                            Agenda tu Cita
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden">
                        <button type="button"
                            class="relative p-2.5 rounded-xl text-gray-700 hover:text-dental-blue hover:bg-gray-50 transition-all duration-300 focus-ring group"
                            aria-label="Abrir menú de navegación" onclick="toggleMobileMenu()">
                            <div class="relative w-5 h-5">
                                <span
                                    class="absolute top-0 left-0 w-full h-0.5 bg-current transition-all duration-300 group-hover:bg-dental-blue"
                                    id="line1"></span>
                                <span
                                    class="absolute top-2 left-0 w-full h-0.5 bg-current transition-all duration-300 group-hover:bg-dental-blue"
                                    id="line2"></span>
                                <span
                                    class="absolute top-4 left-0 w-full h-0.5 bg-current transition-all duration-300 group-hover:bg-dental-blue"
                                    id="line3"></span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Menu -->
                <div id="mobile-menu" class="lg:hidden hidden">
                    <div
                        class="bg-white/95 backdrop-blur-xl border-t border-gray-100/50 mt-1 rounded-b-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-6 space-y-1">
                            <a href="#inicio"
                                class="group flex items-center space-x-3 px-4 py-3 text-gray-700 hover:text-dental-blue hover:bg-dental-blue/5 rounded-xl transition-all duration-300 font-medium">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-dental-blue transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                <span>Inicio</span>
                            </a>
                            <a href="#sobre-nosotros"
                                class="group flex items-center space-x-3 px-4 py-3 text-gray-700 hover:text-dental-blue hover:bg-dental-blue/5 rounded-xl transition-all duration-300 font-medium">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-dental-blue transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <span>Sobre Nosotros</span>
                            </a>
                            <a href="#servicios"
                                class="group flex items-center space-x-3 px-4 py-3 text-gray-700 hover:text-dental-blue hover:bg-dental-blue/5 rounded-xl transition-all duration-300 font-medium">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-dental-blue transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                    </path>
                                </svg>
                                <span>Servicios</span>
                            </a>
                            <a href="#testimonios"
                                class="group flex items-center space-x-3 px-4 py-3 text-gray-700 hover:text-dental-blue hover:bg-dental-blue/5 rounded-xl transition-all duration-300 font-medium">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-dental-blue transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                <span>Testimonios</span>
                            </a>


                            <!-- Mobile CTA Section -->
                            <div class="pt-6 space-y-4 border-t border-gray-100 mt-6">
                                <a href="{{ url('/client') }}"
                                    class="flex items-center justify-center space-x-2 bg-gradient-to-r from-dental-blue to-dental-teal text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg transform hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>Agenda tu Cita</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main>
        <section id="inicio"
            class="pt-24 min-h-screen bg-gradient-to-br from-blue-50 via-white to-teal-50 overflow-hidden"
            role="main">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center min-h-[80vh]">

                    <!-- Content Column -->
                    <div class="space-y-8 animate-fade-in-up">
                        <!-- Badge -->
                        <div class="inline-flex items-center space-x-3 bg-dental-blue/10 px-4 py-2 rounded-full">
                            <div class="w-2 h-2 bg-dental-blue rounded-full animate-pulse-subtle"></div>
                            <span class="text-sm font-medium text-dental-blue uppercase tracking-wide">
                                Fundación con Propósito Social
                            </span>
                        </div>

                        <!-- Main Heading -->
                        <div class="space-y-4">
                            <h1
                                class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-display font-bold leading-tight">
                                <span class="text-gray-900">Sonrisas</span>
                                <span class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl">🦷</span>
                                <br>
                                <span
                                    class="bg-gradient-to-r from-dental-blue to-dental-teal bg-clip-text text-transparent">
                                    Saludables
                                </span>
                                <span class="text-gray-900">para Todos</span>
                            </h1>

                            <p class="text-lg sm:text-xl text-gray-600 leading-relaxed max-w-2xl">
                                En la <strong class="text-dental-blue">Fundación Odontológica Zoila Padilla</strong>
                                brindamos atención dental especializada con compromiso social,
                                promoviendo la prevención y el acceso equitativo a la salud oral en Ciénaga.
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <a href="{{ url('/client') }}"
                                class="group inline-flex items-center justify-center px-8 py-4 bg-dental-blue text-white font-semibold rounded-full hover:bg-dental-blue/90 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl focus-ring">
                                <span>Agenda tu Cita</span>
                                <svg class="ml-2 w-5 h-5 transform group-hover:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>


                        </div>

                        <!-- Trust Indicators -->
                        <div class="flex items-center space-x-6 pt-8 border-t border-gray-100">
                            <div class="flex items-center space-x-2">
                                <div class="w-12 h-12 bg-dental-blue/10 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-dental-blue" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Profesionales Certificados</p>
                                    <p class="text-xs text-gray-600">Equipo especializado</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="w-12 h-12 bg-dental-teal/10 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-dental-teal" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Atención Humanizada</p>
                                    <p class="text-xs text-gray-600">Cuidado integral</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image Column -->
                    <div class="relative lg:order-last">
                        <!-- Background decoration -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-dental-blue/20 to-dental-teal/20 rounded-3xl transform rotate-3">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-dental-teal/20 to-dental-blue/20 rounded-3xl transform -rotate-3">
                        </div>

                        <!-- Main image -->
                        <div
                            class="relative bg-white rounded-3xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-500">
                            <img src="{{ asset('storage/img/img-principal.jpg') }}"
                                alt="Paciente sonriendo después de tratamiento dental en la Fundación Odontológica Zoila Padilla"
                                class="w-full h-96 lg:h-[600px] object-cover">

                            <!-- Overlay with stats -->
                            <div
                                class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-6">
                                <div class="text-white">
                                    <div class="flex space-x-6 mb-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold">500+</div>
                                            <div class="text-sm opacity-90">Pacientes Atendidos</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold">15+</div>
                                            <div class="text-sm opacity-90">Años de Experiencia</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold">98%</div>
                                            <div class="text-sm opacity-90">Satisfacción</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating elements -->
                        <div
                            class="absolute -top-4 -left-4 w-8 h-8 bg-dental-blue rounded-full opacity-60 animate-pulse-subtle">
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-dental-teal rounded-full opacity-40 animate-pulse-subtle"
                            style="animation-delay: 1s;"></div>
                        <div class="absolute top-1/2 -right-8 w-6 h-6 bg-blue-400 rounded-full opacity-80 animate-pulse-subtle"
                            style="animation-delay: 2s;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="sobre-nosotros" class="py-20 lg:py-28 bg-gradient-to-b from-white to-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-flex items-center space-x-2 bg-dental-blue/10 px-4 py-2 rounded-full mb-6">
                        <span class="text-sm font-medium text-dental-blue uppercase tracking-wide">
                            Sobre Nosotros
                        </span>
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-display font-bold text-gray-900 mb-6">
                        Una <span class="text-dental-blue">Fundación</span> con Propósito Social
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Comprometidos con brindar atención odontológica de calidad,
                        accesible y humana para toda la comunidad de Ciénaga.
                    </p>
                </div>

                <!-- Main Content Grid -->
                <div class="grid lg:grid-cols-2 gap-16 items-center mb-20">

                    <!-- Content -->
                    <div class="space-y-8">
                        <div class="prose prose-lg max-w-none">
                            <p class="text-gray-700 leading-relaxed">
                                La <strong class="text-dental-blue">Fundación Odontológica Zoila Padilla</strong>
                                nació con el compromiso de brindar atención odontológica especializada
                                a la población de Ciénaga y sus alrededores.
                            </p>

                            <p class="text-gray-700 leading-relaxed">
                                Nuestro equipo de profesionales trabaja bajo principios de ética,
                                calidad y responsabilidad social, promoviendo la prevención y el
                                acceso equitativo a servicios de salud oral.
                            </p>
                        </div>

                        <!-- Mission & Vision -->
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="bg-dental-blue/5 p-6 rounded-2xl">
                                <div
                                    class="w-12 h-12 bg-dental-blue/10 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-dental-blue" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Nuestra Misión</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Proporcionar servicios odontológicos de excelencia con responsabilidad social.
                                </p>
                            </div>

                            <div class="bg-dental-teal/5 p-6 rounded-2xl">
                                <div
                                    class="w-12 h-12 bg-dental-teal/10 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-dental-teal" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Nuestra Visión</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Ser referente en salud oral comunitaria y responsabilidad social.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Team Image -->
                    <div class="relative">
                        <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl">
                            <picture>
                                <source srcset="{{ asset('storage/img/grupo.jpg') }}" type="image/webp">
                                <img src="{{ asset('storage/img/team.jpg') }}"
                                    alt="Equipo de profesionales de la Fundación Odontológica Zoila Padilla"
                                    class="w-full h-96 lg:h-[420px] object-cover" loading="lazy">
                            </picture>
                        </div>

                        <!-- Floating stats -->
                        <div
                            class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-lg p-4 border border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-dental-blue/10 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-dental-blue" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">500+</p>
                                    <p class="text-sm text-gray-600">Pacientes Satisfechos</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="absolute -top-6 -right-6 bg-white rounded-2xl shadow-lg p-4 border border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-dental-teal/10 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-dental-teal" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">15+</p>
                                    <p class="text-sm text-gray-600">Años de Experiencia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Values Section -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center group">
                        <div
                            class="w-16 h-16 bg-dental-blue/10 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-dental-blue/20 transition-colors">
                            <svg class="w-8 h-8 text-dental-blue" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Compromiso Social</h3>
                        <p class="text-gray-600 text-sm">Atención accesible para toda la comunidad</p>
                    </div>

                    <div class="text-center group">
                        <div
                            class="w-16 h-16 bg-dental-teal/10 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-dental-teal/20 transition-colors">
                            <svg class="w-8 h-8 text-dental-teal" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Calidad Garantizada</h3>
                        <p class="text-gray-600 text-sm">Estándares internacionales de atención</p>
                    </div>

                    <div class="text-center group">
                        <div
                            class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Equipo Experto</h3>
                        <p class="text-gray-600 text-sm">Profesionales certificados y especializados</p>
                    </div>

                    <div class="text-center group">
                        <div
                            class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-sky-200 transition-colors">
                            <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Innovación</h3>
                        <p class="text-gray-600 text-sm">Tecnología dental de vanguardia</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="servicios" class="py-20 lg:py-28 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-flex items-center space-x-2 bg-dental-teal/10 px-4 py-2 rounded-full mb-6">
                        <span class="text-sm font-medium text-dental-teal uppercase tracking-wide">
                            Nuestros Servicios
                        </span>
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-display font-bold text-gray-900 mb-6">
                        Atención <span class="text-dental-teal">Integral</span> para tu Sonrisa
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Ofrecemos una amplia gama de servicios odontológicos con tecnología avanzada
                        y un equipo comprometido con tu bienestar.
                    </p>
                </div>

                <!-- Featured Services Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">

                    <!-- Service 1: Odontología General -->
                    <div
                        class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border border-gray-100">
                        <!-- Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/img/odontologiaGeneral.jpg') }}"
                                alt="Servicios de Odontología General - Consultas y limpiezas dentales profesionales"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dental-blue/30 to-transparent"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-dental-blue transition-colors">
                                Odontología General
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                                Consultas, limpiezas dentales, extracciones y tratamientos preventivos
                                para mantener tu salud oral en óptimas condiciones.
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-dental-blue/10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-dental-blue" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Exámenes orales completos</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-dental-blue/10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-dental-blue" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Limpiezas profesionales</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-dental-blue/10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-dental-blue" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Radiografías digitales</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Service 2: Operatoria Dental -->
                    <div
                        class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border border-gray-100">
                        <!-- Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/img/operacion.jpg') }}"
                                alt="Operatoria Dental - Restauraciones estéticas y tratamiento de caries"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dental-teal/30 to-transparent"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-dental-teal transition-colors">
                                Operatoria Dental
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                                Restauración de dientes dañados con materiales de alta calidad
                                para devolver funcionalidad y estética a tu sonrisa.
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-dental-teal/10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-dental-teal" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Resinas estéticas</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-dental-teal/10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-dental-teal" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Tratamiento de caries</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-dental-teal/10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-dental-teal" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Incrustaciones dentales</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Service 3: Endodoncia -->
                    <div
                        class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border border-gray-100">
                        <!-- Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/img/endodoncia.jpg') }}"
                                alt="Endodoncia - Tratamientos de conducto especializados"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/30 to-transparent"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                Endodoncia
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                                Tratamientos de conducto especializados para preservar
                                tus dientes naturales y eliminar el dolor.
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Tratamientos de conducto</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Retratamientos endodónticos</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Cirugía endodóntica</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Service 4: Periodoncia -->
                    <div
                        class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border border-gray-100">
                        <!-- Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/img/periodoncia.jpg') }}"
                                alt="Periodoncia - Tratamiento especializado de encías"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-cyan-600/30 to-transparent"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-cyan-600 transition-colors">
                                Periodoncia
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                                Tratamiento especializado de encías y tejidos que sostienen
                                los dientes para una base sólida y saludable.
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-cyan-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-cyan-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Tratamiento de gingivitis</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-cyan-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-cyan-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Raspado y alisado radicular</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-cyan-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-cyan-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Cirugía periodontal</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Service 5: Odontopediatría -->
                    <div
                        class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border border-gray-100">
                        <!-- Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/img/odontopediatria.jpg') }}"
                                alt="Odontopediatría - Atención dental especializada para niños"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-sky-600/30 to-transparent"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-sky-600 transition-colors">
                                Odontopediatría
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                                Atención dental especializada para niños en un ambiente
                                amigable y cómodo que fomenta buenos hábitos.
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-sky-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Consultas preventivas</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-sky-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Sellantes de fosas y fisuras</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-sky-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Educación en higiene oral</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Service 6: Rehabilitación Oral -->
                    <div
                        class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-3 border border-gray-100">
                        <!-- Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/img/rehabilitacion-oral.jpg') }}"
                                alt="Rehabilitación Oral - Prótesis dentales y tratamientos integrales"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/30 to-transparent"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                Rehabilitación Oral
                            </h3>
                            <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                                Restauración completa de la función y estética dental
                                con prótesis y tratamientos integrales.
                            </p>
                            <ul class="space-y-3 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Prótesis dentales</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Coronas y puentes</span>
                                </li>
                                <li class="flex items-center">
                                    <div
                                        class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span>Implantes dentales</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Testimonials Section -->
                <section id="testimonios" class="py-20 lg:py-28 bg-gradient-to-b from-gray-50 to-white">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                        <!-- Section Header -->
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center space-x-2 bg-blue-100 px-4 py-2 rounded-full mb-6">
                                <span class="text-sm font-medium text-blue-700 uppercase tracking-wide">
                                    Testimonios
                                </span>
                            </div>
                            <h2 class="text-4xl lg:text-5xl font-display font-bold text-gray-900 mb-6">
                                Lo que Dicen Nuestros <span class="text-dental-blue">Pacientes</span>
                            </h2>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                                La satisfacción de nuestros pacientes es nuestro mayor logro.
                                Conoce sus experiencias transformadoras.
                            </p>
                        </div>

                        <!-- Stats Section -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                            <div class="text-center">
                                <div class="text-4xl lg:text-5xl font-bold text-dental-blue mb-2">98%</div>
                                <p class="text-gray-600 font-medium">Satisfacción del Paciente</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl lg:text-5xl font-bold text-dental-teal mb-2">500+</div>
                                <p class="text-gray-600 font-medium">Pacientes Atendidos</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl lg:text-5xl font-bold text-dental-green mb-2">15+</div>
                                <p class="text-gray-600 font-medium">Años de Experiencia</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl lg:text-5xl font-bold text-blue-600 mb-2">24/7</div>
                                <p class="text-gray-600 font-medium">Atención de Emergencia</p>
                            </div>
                        </div>

                        <!-- Testimonials Grid -->
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">

                            <!-- Testimonial 1 -->
                            <div
                                class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                                <div class="flex items-center mb-6">
                                    <div class="flex text-blue-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                </div>
                                <blockquote class="text-gray-700 leading-relaxed mb-6">
                                    "Excelente atención desde el primer momento. El Dr. y su equipo me hicieron sentir
                                    muy
                                    cómoda durante todo el tratamiento. Mi sonrisa ahora es completamente diferente."
                                </blockquote>
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-300 rounded-full flex items-center justify-center text-white font-semibold">
                                        MP
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold text-gray-900">María Pérez</div>
                                        <div class="text-sm text-gray-600">Paciente desde 2022</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial 2 -->
                            <div
                                class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                                <div class="flex items-center mb-6">
                                    <div class="flex text-blue-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                </div>
                                <blockquote class="text-gray-700 leading-relaxed mb-6">
                                    "Como padre de familia, valoro mucho el trato especial que le dan a los niños. Mi
                                    hijo ahora
                                    no tiene miedo al dentista y siempre quiere volver."
                                </blockquote>
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-300 rounded-full flex items-center justify-center text-white font-semibold">
                                        CL
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold text-gray-900">Carlos López</div>
                                        <div class="text-sm text-gray-600">Padre de familia</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testimonial 3 -->
                            <div
                                class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                                <div class="flex items-center mb-6">
                                    <div class="flex text-blue-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                </div>
                                <blockquote class="text-gray-700 leading-relaxed mb-6">
                                    "La tecnología que manejan es impresionante. Todo fue muy rápido y sin dolor.
                                    Definitivamente la mejor experiencia dental que he tenido."
                                </blockquote>
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-300 rounded-full flex items-center justify-center text-white font-semibold">
                                        AG
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold text-gray-900">Ana García</div>
                                        <div class="text-sm text-gray-600">Profesional</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

                <!-- Brand -->
                <div class="lg:col-span-2">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('storage/img/lg_zoila_padilla.svg') }}"
                            alt="Logo Fundación Odontológica Zoila Padilla" class="h-12 w-auto mr-4 ">
                    </div>
                    <p class="text-gray-400 leading-relaxed max-w-md">
                        Comprometidos con brindar atención odontológica de calidad y
                        accesible para toda la comunidad de Ciénaga.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <a href="https://www.instagram.com/tu_cuenta" target="_blank"
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-dental-blue transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.97.24 2.43.405a4.92 4.92 0 0 1 1.675 1.01 4.92 4.92 0 0 1 1.01 1.675c.165.46.351 1.26.405 2.43.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.24 1.97-.405 2.43a4.92 4.92 0 0 1-1.01 1.675 4.92 4.92 0 0 1-1.675 1.01c-.46.165-1.26.351-2.43.405-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.97-.24-2.43-.405a4.92 4.92 0 0 1-1.675-1.01 4.92 4.92 0 0 1-1.01-1.675c-.165-.46-.351-1.26-.405-2.43C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.054-1.17.24-1.97.405-2.43a4.92 4.92 0 0 1 1.01-1.675 4.92 4.92 0 0 1 1.675-1.01c.46-.165 1.26-.351 2.43-.405C8.416 2.175 8.796 2.163 12 2.163zm0 1.837c-3.18 0-3.558.012-4.805.07-.99.045-1.52.2-1.87.332-.47.182-.8.4-1.15.75-.35.35-.568.68-.75 1.15-.132.35-.287.88-.332 1.87-.058 1.247-.07 1.625-.07 4.805s.012 3.558.07 4.805c.045.99.2 1.52.332 1.87.182.47.4.8.75 1.15.35.35.68.568 1.15.75.35.132.88.287 1.87.332 1.247.058 1.625.07 4.805.07s3.558-.012 4.805-.07c.99-.045 1.52-.2 1.87-.332.47-.182.8-.4 1.15-.75.35-.35.568-.68.75-1.15.132-.35.287-.88.332-1.87.058-1.247.07-1.625.07-4.805s-.012-3.558-.07-4.805c-.045-.99-.2-1.52-.332-1.87-.182-.47-.4-.8-.75-1.15-.35-.35-.68-.568-1.15-.75-.35-.132-.88-.287-1.87-.332-1.247-.058-1.625-.07-4.805-.07zm0 3.248a5.752 5.752 0 1 1 0 11.504 5.752 5.752 0 0 1 0-11.504zm0 1.837a3.915 3.915 0 1 0 0 7.83 3.915 3.915 0 0 0 0-7.83zm6.406-.92a1.34 1.34 0 1 1 0 2.68 1.34 1.34 0 0 1 0-2.68z" />
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/tu_cuenta" target="_blank"
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-dental-blue transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24h11.495v-9.294H9.691v-3.622h3.129V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.794.715-1.794 1.763v2.31h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.324-.593 1.324-1.324V1.325C24 .593 23.407 0 22.675 0z" />
                            </svg>
                        </a>

                    </div>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Servicios</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#servicios" class="hover:text-white transition-colors">Odontología General</a>
                        </li>
                        <li><a href="#servicios" class="hover:text-white transition-colors">Operatoria Dental</a></li>
                        <li><a href="#servicios" class="hover:text-white transition-colors">Endodoncia</a></li>
                        <li><a href="#servicios" class="hover:text-white transition-colors">Periodoncia</a></li>
                        <li><a href="#servicios" class="hover:text-white transition-colors">Odontopediatría</a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#sobre-nosotros" class="hover:text-white transition-colors">Sobre Nosotros</a>
                        </li>
                        <li><a href="#servicios" class="hover:text-white transition-colors">Servicios</a></li>
                        <li><a href="#testimonios" class="hover:text-white transition-colors">Testimonios</a></li>
                        <li><a href="#contacto" class="hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="{{ url('/client') }}" class="hover:text-white transition-colors">Agendar
                                Cita</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    © 2024 Fundación Odontológica Zoila Padilla. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Enhanced JavaScript for Professional UX -->
    <script>
        // Enhanced Mobile Menu Toggle with Animation
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const line1 = document.getElementById('line1');
            const line2 = document.getElementById('line2');
            const line3 = document.getElementById('line3');

            // Toggle menu visibility with slide animation
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                // Animate hamburger to X
                line1.style.transform = 'rotate(45deg) translate(6px, 6px)';
                line2.style.opacity = '0';
                line3.style.transform = 'rotate(-45deg) translate(6px, -6px)';

                // Add slide down animation
                setTimeout(() => {
                    mobileMenu.querySelector('div').style.transform = 'translateY(0)';
                    mobileMenu.querySelector('div').style.opacity = '1';
                }, 10);
            } else {
                // Animate hamburger back to normal
                line1.style.transform = 'rotate(0) translate(0, 0)';
                line2.style.opacity = '1';
                line3.style.transform = 'rotate(0) translate(0, 0)';

                // Add slide up animation
                mobileMenu.querySelector('div').style.transform = 'translateY(-10px)';
                mobileMenu.querySelector('div').style.opacity = '0';

                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 200);
            }
        }

        // Enhanced Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    // Calculate offset for fixed header
                    const headerHeight = document.querySelector('header').offsetHeight;
                    const targetPosition = target.offsetTop - headerHeight - 20;

                    // Smooth scroll with custom easing
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Close mobile menu if open
                    const mobileMenu = document.getElementById('mobile-menu');
                    if (!mobileMenu.classList.contains('hidden')) {
                        toggleMobileMenu();
                    }

                    // Update active navigation state
                    updateActiveNavigation(this.getAttribute('href'));
                }
            });
        });

        // Update Active Navigation State
        function updateActiveNavigation(activeHref) {
            // Remove active state from all nav links
            document.querySelectorAll('nav a[href^="#"]').forEach(link => {
                link.classList.remove('text-dental-blue', 'bg-white', 'shadow-sm');
            });

            // Add active state to current link
            document.querySelectorAll(`nav a[href="${activeHref}"]`).forEach(link => {
                if (link.closest('.lg\\:flex')) { // Desktop nav
                    link.classList.add('text-dental-blue', 'bg-white', 'shadow-sm');
                }
            });
        }

        // Navbar Scroll Effect
        let lastScrollTop = 0;
        const navbar = document.getElementById('navbar');

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Change navbar appearance based on scroll position
            if (scrollTop > 100) {
                navbar.querySelector('div').classList.add('shadow-xl');
                navbar.querySelector('div').classList.remove('shadow-lg');
            } else {
                navbar.querySelector('div').classList.remove('shadow-xl');
                navbar.querySelector('div').classList.add('shadow-lg');
            }

            lastScrollTop = scrollTop;
        }, false);

        // Intersection Observer for Active Navigation
        const observerOptions = {
            rootMargin: '-20% 0px -70% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    if (id) {
                        updateActiveNavigation(`#${id}`);
                    }
                }
            });
        }, observerOptions);

        // Observe all sections
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('section[id]').forEach(section => {
                observer.observe(section);
            });

            // Initialize mobile menu styles
            const mobileMenuContent = document.querySelector('#mobile-menu > div');
            if (mobileMenuContent) {
                mobileMenuContent.style.transform = 'translateY(-10px)';
                mobileMenuContent.style.opacity = '0';
                mobileMenuContent.style.transition = 'all 0.2s ease-out';
            }

            // Add loading animation fade-in
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease-in';

            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Enhanced Focus Management for Accessibility
        document.addEventListener('keydown', function(e) {
            // Close mobile menu with Escape key
            if (e.key === 'Escape') {
                const mobileMenu = document.getElementById('mobile-menu');
                if (!mobileMenu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }
        });

        // Prevent body scroll when mobile menu is open
        function preventBodyScroll(prevent) {
            if (prevent) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Add click outside to close mobile menu
        document.addEventListener('click', function(e) {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = document.querySelector('[onclick="toggleMobileMenu()"]');

            if (!mobileMenu.classList.contains('hidden') &&
                !mobileMenu.contains(e.target) &&
                !menuButton.contains(e.target)) {
                toggleMobileMenu();
            }
        });
    </script>
