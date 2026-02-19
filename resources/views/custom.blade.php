<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BEKKAS') }} - Architecture Services</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <x-favorites-init />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-light text-dark">
        @include('layouts.navigation')

        <!-- BANNER SECTION -->
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-dark">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&h=800&fit=crop')">
                <div class="absolute inset-0 bg-dark/40"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-light px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ t('custom.banner.title') ?: 'Custom Services' }}</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">{{ t('custom.banner.subtitle') ?: 'Professional 3D printing solutions for architects and designers' }}</p>
                <a href="#request" class="inline-block bg-accent-primary hover:bg-accent-primary/90 text-light px-8 py-3 rounded font-semibold transition-colors">
                    {{ t('custom.banner.button') ?: 'Request Service' }}
                </a>
            </div>
        </section>

        <!-- FEATURES SECTION -->
        <section class="py-16 md:py-24 bg-light px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold mb-12 text-center text-dark">{{ t('custom.features.title') ?: 'Our Services' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-light p-8 rounded-lg">
                        <h3 class="text-xl font-bold mb-4 text-dark">{{ t('custom.features.modeling') ?: '3D Modeling' }}</h3>
                        <p class="text-grey-dark">{{ t('custom.features.modeling_desc') ?: 'Professional 3D model preparation and optimization for printing.' }}</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-light p-8 rounded-lg">
                        <h3 class="text-xl font-bold mb-4 text-dark">{{ t('custom.features.materials') ?: 'Multiple Materials' }}</h3>
                        <p class="text-grey-dark">{{ t('custom.features.materials_desc') ?: 'Choose from various materials and finishes to suit your project needs.' }}</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-light p-8 rounded-lg">
                        <h3 class="text-xl font-bold mb-4 text-dark">{{ t('custom.features.support') ?: 'Expert Support' }}</h3>
                        <p class="text-grey-dark">{{ t('custom.features.support_desc') ?: 'Dedicated support from design consultation to final delivery.' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- REQUEST SECTION -->
        <section id="request" class="py-16 md:py-24 bg-light px-6">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-4xl font-bold mb-6 text-center text-dark">{{ t('custom.request.title') ?: 'Request a Quote' }}</h2>
                <p class="text-center text-lg text-grey-dark mb-10">
                    {{ t('custom.request.cta') ?: 'Want more info or a quote for a specific project? Please send us a ticket and we will follow up.' }}
                </p>

                <div class="bg-light rounded-lg shadow-lg p-8 text-center">
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-3 rounded-lg font-semibold transition-colors">
                        {{ t('custom.request.ticket_button') ?: 'Create new ticket' }}
                    </a>
                </div>
            </div>
        </section>

        @include('layouts.footer')
    </body>
</html>
