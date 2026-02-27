<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BEKKAS') }} - {{ t('nav.about') ?: 'About Us' }}</title>


        <x-favorites-init />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-dark">
        @include('layouts.navigation')

        <!-- BANNER SECTION -->
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-dark">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1581092160607-ee67e9e95167?w=1200&h=800&fit=crop')">
                <div class="absolute inset-0 bg-dark/40"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-white px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ t('about.banner.title') ?: 'About BEKKAS' }}</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">{{ t('about.banner.subtitle') ?: 'Making 3D printing accessible to everyone' }}</p>
            </div>
        </section>

        <!-- MISSION SECTION -->
        <section class="py-16 md:py-24 bg-white animate-sequence">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center anim-item" data-index="0">
                <h2 class="text-4xl font-bold mb-8 text-dark">{{ t('about.mission.title') ?: 'Our Mission' }}</h2>
                <p class="text-lg md:text-xl text-grey-dark mb-6 leading-relaxed">
                    {{ t('about.mission.intro') ?: 'At BEKKAS, we believe that everyone deserves access to the transformative power of 3D printing technology.' }}
                </p>
                <p class="text-lg md:text-xl text-grey-dark leading-relaxed">
                    {{ t('about.mission.purpose') ?: 'Our business is focused on making 3D printing accessible and affordable, giving everybody a chance to have something unique and personalizable that reflects their vision and creativity.' }}
                </p>
            </div>
        </section>

        <!-- VALUES SECTION -->
        <section class="py-16 md:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-4xl font-bold mb-12 text-center text-dark">{{ t('about.values.title') ?: 'What We Stand For' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-sequence">
                    <!-- Value 1 -->
                    <div class="bg-white p-8 rounded-lg anim-item" data-index="0">
                        <h3 class="text-xl font-bold mb-4 text-dark">{{ t('about.values.accessibility') ?: 'Accessibility' }}</h3>
                        <p class="text-grey-dark">{{ t('about.values.accessibility_desc') ?: 'We make 3D printing technology available to everyone, from students to professionals, with affordable solutions and expert guidance.' }}</p>
                    </div>

                    <!-- Value 2 -->
                    <div class="bg-white p-8 rounded-lg anim-item" data-index="1">
                        <h3 class="text-xl font-bold mb-4 text-dark">{{ t('about.values.uniqueness') ?: 'Uniqueness' }}</h3>
                        <p class="text-grey-dark">{{ t('about.values.uniqueness_desc') ?: 'Every project is different. We help you create something truly unique and personalizable that stands out from mass-produced items.' }}</p>
                    </div>

                    <!-- Value 3 -->
                    <div class="bg-white p-8 rounded-lg anim-item" data-index="2">
                        <h3 class="text-xl font-bold mb-4 text-dark">{{ t('about.values.quality') ?: 'Quality' }}</h3>
                        <p class="text-grey-dark">{{ t('about.values.quality_desc') ?: 'We never compromise on quality. From material selection to final delivery, every step is executed with precision and care.' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- STORY SECTION -->
        <section class="py-16 md:py-24 bg-white animate-sequence">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 anim-item" data-index="0">
                <h2 class="text-4xl font-bold mb-8 text-center text-dark">{{ t('about.story.title') ?: 'Our Story' }}</h2>
                <div class="space-y-6 text-lg text-grey-dark">
                    <p>
                        {{ t('about.story.paragraph1') ?: 'BEKKAS was founded with a simple vision: to democratize access to 3D printing technology and empower individuals to bring their ideas to life.' }}
                    </p>
                    <p>
                        {{ t('about.story.paragraph2') ?: 'We started by serving architects and students, helping them create detailed models for their projects. Today, we serve a diverse community of creators, offering personalized 3D printing solutions for any need.' }}
                    </p>
                    <p>
                        {{ t('about.story.paragraph3') ?: 'Whether you need a prototype, a custom gift, an architectural model, or a unique product, we are here to make it happen. Your imagination is the only limit.' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-16 md:py-24 bg-primary animate-sequence">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white anim-item" data-index="0">
                <h2 class="text-4xl font-bold mb-6">{{ t('about.cta.title') ?: 'Ready to Start Your Project?' }}</h2>
                <p class="text-xl mb-8">
                    {{ t('about.cta.description') ?: 'Join hundreds of satisfied customers who have brought their ideas to life with BEKKAS.' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-sequence">
                    @if(config('app.store_enabled'))
                        <a href="{{ route('store.index') }}" class="inline-block bg-white text-accent-primary hover:bg-grey-light px-8 py-3 rounded-full uppercase font-semibold transition-colors anim-item" data-index="0">
                            {{ t('about.cta.shop') ?: 'Browse Products' }}
                        </a>
                    @endif
                    <a href="{{ route('tickets.create') }}" class="inline-block bg-primary/90 hover:bg-primary/95 text-white px-8 py-3 rounded-full uppercase font-semibold transition-colors anim-item" data-index="1">
                        {{ t('about.cta.contact') ?: 'Start a Project' }}
                    </a>
                </div>
            </div>
        </section>

        @include('layouts.footer')
    </body>
</html>
