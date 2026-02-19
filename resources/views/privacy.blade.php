<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BEKKAS') }} - {{ t('legal.privacy.title') ?: 'Privacy Policy' }}</title>

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
                <h1 class="text-4xl font-bold mb-6">{{ t('legal.privacy.title') ?: 'Privacy Policy' }}</h1>

                <p>{{ t('legal.privacy.content') ?: 'We collect and use personal information to provide our services. We respect your privacy and process data according to applicable law.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.privacy.section_data_title') ?: 'Data We Collect' }}</h2>
                <p>{{ t('legal.privacy.section_data') ?: 'We collect data you provide (account details, order information) and technical data (cookies, analytics) to operate the service.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.privacy.section_usage_title') ?: 'How We Use Data' }}</h2>
                <p>{{ t('legal.privacy.section_usage') ?: 'Personal data is used to process orders, communicate with you, and improve the service. We do not sell personal data to third parties.' }}</p>

                <p class="mt-8 text-sm text-grey-dark">{{ t('legal.privacy.last_updated') ?: 'Last updated: February 2026' }}</p>
            </div>
        </section>

        @include('layouts.footer')
    </body>
</html>
