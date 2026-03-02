<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#f4eee4">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Google reCAPTCHA (loaded only on pages that request it) -->
        @stack('recaptcha')
    </head>
    @php $serverFlash = session('success') ?? session('error') ?? session('warning') ?? session('info'); @endphp
    <body class="font-sans text-dark antialiased" @if($serverFlash) data-server-flash="{{ e($serverFlash) }}" @endif>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grey-light">
            <div>
                <a href="/">
                    <img src="{{ asset('images/hero_logo.svg') }}" alt="BEKKAS" class="h-28 w-auto">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-10 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <!-- Flash Messages (canonical; supports success|error|warning|info) -->
                @include('partials.flash')

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
