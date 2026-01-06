<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BEKKAS') }} - Architecture Services</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white dark:bg-[#161615] text-gray-900 dark:text-gray-100">
        @include('layouts.navigation')

        <!-- BANNER SECTION -->
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-gray-900">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&h=800&fit=crop')">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-white px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ t('architecture.banner.title') ?: 'Architecture Services' }}</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">{{ t('architecture.banner.subtitle') ?: 'Professional 3D printing solutions for architects and designers' }}</p>
                <a href="#request" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded font-semibold transition-colors">
                    {{ t('architecture.banner.button') ?: 'Request Service' }}
                </a>
            </div>
        </section>

        <!-- FEATURES SECTION -->
        <section class="py-16 md:py-24 bg-white dark:bg-[#161615] px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold mb-12 text-center text-gray-900 dark:text-white">{{ t('architecture.features.title') ?: 'Our Services' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-lg">
                        <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ t('architecture.features.modeling') ?: '3D Modeling' }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ t('architecture.features.modeling_desc') ?: 'Professional 3D model preparation and optimization for printing.' }}</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-lg">
                        <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ t('architecture.features.materials') ?: 'Multiple Materials' }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ t('architecture.features.materials_desc') ?: 'Choose from various materials and finishes to suit your project needs.' }}</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-lg">
                        <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ t('architecture.features.support') ?: 'Expert Support' }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ t('architecture.features.support_desc') ?: 'Dedicated support from design consultation to final delivery.' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- REQUEST SECTION -->
        <section id="request" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900 px-6">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-4xl font-bold mb-6 text-center text-gray-900 dark:text-white">{{ t('architecture.request.title') ?: 'Request a Quote' }}</h2>
                <p class="text-center text-lg text-gray-700 dark:text-gray-300 mb-10">
                    {{ t('architecture.request.cta') ?: 'Want more info or a quote for a specific project? Please send us a ticket and we will follow up.' }}
                </p>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        {{ t('architecture.request.ticket_button') ?: 'Create new ticket' }}
                    </a>
                </div>
            </div>
        </section>

        @include('layouts.footer')
    </body>
</html>
