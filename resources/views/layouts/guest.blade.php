<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Google reCAPTCHA (loaded only on pages that request it) -->
        @stack('recaptcha')
    </head>
    @php $serverFlash = session('success') ?? session('error') ?? session('warning') ?? session('info'); @endphp
    <body class="font-sans text-gray-900 antialiased" @if($serverFlash) data-server-flash="{{ e($serverFlash) }}" @endif>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <img src="{{ asset('images/hero-logo.png') }}" alt="BEKKAS" class="h-28 w-auto">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-10 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <!-- Flash Messages (canonical; supports success|error|warning|info) -->
                @include('partials.flash')

                {{ $slot }}
            </div>
        </div>

        @include('layouts.footer')
    </body>
</html>
