<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BEKKAS - 3D Printing Services</title>

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#f4eee4">


        <x-favorites-init />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/css/home-splash.css', 'resources/js/app.js'])
        @endif

        <!-- Google reCAPTCHA (loaded only on pages that request it) -->
        @stack('recaptcha')
        {{-- always include loader in this standalone template so it can't miss the stack --}}
        @include('partials.recaptcha-loader')
    </head>
    <body class="bg-light text-dark overflow-hidden" data-splash-active="true">
        @include('layouts.navigation')

        <!-- HOME SPLASH INTRO -->
        <div id="home-splash" class="home-splash-overlay" role="dialog" aria-label="{{ config('app.name', 'BEKKAS') }} splash">
            <img src="{{ asset('images/hero_logo.svg') }}" alt="{{ config('app.name', 'BEKKAS') }}" class="home-splash-logo" />
        </div>

        <!-- BANNER SECTION -->
        @php
            // define slides for the homepage carousel; text/button reuse existing translation keys
            $homeTagline1 = t('home.banner.tagline1') ?: 'Printing Life layer by layer';
            $homeButton1  = t('home.banner.button1')  ?: 'OUR SERVICES';
            $homeTagline2 = t('home.banner.tagline2') ?: 'Printing Life layer by layer';
            $homeButton2  = t('home.banner.button2')  ?: 'OUR SERVICES';
            $homeTagline3 = t('home.banner.tagline3') ?: 'Printing Life layer by layer';
            $homeButton3  = t('home.banner.button3')  ?: 'OUR SERVICES';
            $slides = [
                [
                    // local placeholders copied during build
                    'image' => asset('images/slide1.jpg'),
                    'tagline' => $homeTagline1,
                    'buttonText' => $homeButton1,
                    'buttonUrl' => '#services',
                ],
                [
                    'image' => asset('images/slide2.jpg'),
                    'tagline' => $homeTagline2,
                    'buttonText' => $homeButton2,
                    'buttonUrl' => '/store',
                ],
                [
                    'image' => asset('images/slide3.jpg'),
                    'tagline' => $homeTagline3,
                    'buttonText' => $homeButton3,
                    'buttonUrl' => '/custom',
                ],
            ];
        @endphp

        <x-home-banner :slides="$slides" />

        <!-- SERVICES SECTION -->
        <section id="services" class="py-16 md:py-24 bg-white px-6 animate-sequence">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    
                    <!-- Products Card -->
                    @if(config('app.store_enabled'))
                        <a href="{{ route('store.index') }}" class="group anim-item" data-index="0">
                            <div class="bg-light rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                                {{-- scroll images for store products --}}
                                <x-image-scroller class="w-full aspect-square" :config="[
                                    'interval' => 3000,
                                    'max' => null,
                                    'products' => [
                                        'featured' => true,
                                        'active' => true,
                                        'per_item' => 1,
                                    ],
                                ]" />
                                <div class="p-6 flex flex-col flex-grow">
                                    <h3 class="text-2xl font-bold mb-3 text-dark">{{ t('home.services.store.title') ?: 'STORE' }}</h3>
                                    <p class="text-grey-dark mb-6 flex-grow">{{ t('home.services.store.description') ?: 'Day to day life objects, gifts, souvenires' }}</p>
                                    <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded font-medium transition-colors">
                                        {{ t('home.services.store.button') ?: 'Store' }}
                                    </button>
                                </div>
                            </div>
                        </a>
                    @endif

                    <!-- Custom Card -->
                    <a href="{{ route('custom.index') }}" class="group anim-item" data-index="1">
                        <div class="bg-light rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                            {{-- scroll images for custom projects --}}
                            <x-image-scroller class="w-full aspect-square" :config="[
                                'interval' => 3000,
                                'max' => null,
                                'projects' => [
                                    'active' => true,
                                    'featured' => true,
                                    'per_item' => 1,
                                ],
                            ]" />
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-3 text-dark">{{ t('home.services.custom.title') ?: 'CUSTOM' }}</h3>
                                <p class="text-grey-dark mb-6 flex-grow">{{ t('home.services.custom.description') ?: 'Printing service for architects and architecture students including modeling and file preparation.' }}</p>
                                <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded font-medium transition-colors">
                                    {{ t('home.services.custom.button') ?: 'More info' }}
                                </button>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </section>

        <!-- CONTACT SECTION -->
        <section id="contact" class="py-16 md:py-24 bg-grey-light px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold mb-12 text-center text-dark">{{ t('home.contact.title') ?: 'Get in Touch' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 animate-sequence">
                    
                    <!-- Contact Info -->
                    <div class="space-y-8 anim-item" data-index="0">
                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.location') ?: 'Location' }}</h3>
                            <p class="text-grey-dark">Lisbon, Portugal</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.phone') ?: 'Phone' }}</h3>
                            <a href="https://wa.me/351965707800" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary font-medium">
                                +351 965 707 800 (WhatsApp)
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.email') ?: 'Email' }}</h3>
                            <a href="mailto:{{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}" class="text-accent-primary hover:text-accent-primary font-medium">
                                {{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-4">{{ t('home.contact.social') ?: 'Follow Us' }}</h3>
                            <div class="flex gap-4">
                                <a href="https://instagram.com/bekkas_pt" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary font-medium">
                                    Instagram
                                </a>
                                <a href="https://www.makerworld.com/en/makers/bekkas" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary font-medium">
                                    Makerworld
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-light rounded-lg shadow-lg p-8 anim-item" data-index="1">
                        @include('partials.contact-form')
                    </div>

                </div>
            </div>
        </section>

        @include('layouts.footer')



    </body>
</html>
