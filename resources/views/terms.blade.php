<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BEKKAS') }} - {{ t('legal.terms.title') ?: 'Terms of Service' }}</title>

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

        <section class="py-16 md:py-24 px-6">
            <div class="max-w-4xl mx-auto prose">
                <h1 class="text-4xl font-bold mb-6">{{ t('legal.terms.title') ?: 'Terms of Service' }}</h1>

                <p>{{ t('legal.terms.content') ?: 'These Terms of Service govern your use of the site. By creating an account and using our services you agree to comply with these terms.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.terms.section_usage_title') ?: 'Use of the Service' }}</h2>
                <p>{{ t('legal.terms.section_usage') ?: 'You agree to use the service in compliance with applicable laws and not to misuse the platform. We may suspend or terminate accounts that violate these terms.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.terms.section_limitation_title') ?: 'Limitation of Liability' }}</h2>
                <p>{{ t('legal.terms.section_limitation') ?: 'Our liability is limited as permitted by law. We provide services on an as-is basis and disclaim certain warranties.' }}</p>

                <p class="mt-8 text-sm text-grey-dark">{{ t('legal.terms.last_updated') ?: 'Last updated: February 2026' }}</p>
            </div>
        </section>

        @include('layouts.footer')
    </body>
</html>
