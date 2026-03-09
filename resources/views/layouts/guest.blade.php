<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.meta')
        @include('partials.title')
        @include('partials.favicons')


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Per-page head (styles/scripts) -->
        @stack('head')

        <!-- Google reCAPTCHA (loaded only on pages that request it) -->
        @stack('recaptcha')
    </head>
    @php $serverFlash = session('success') ?? session('error') ?? session('warning') ?? session('info'); @endphp
    <body class="font-sans text-dark antialiased" @if($serverFlash) data-server-flash="{{ e($serverFlash) }}" @endif>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grey-light">
            <div>
                <a href="/" class="text-accent-primary hover:text-accent-primary/90 no-underline">
                    <img src="{{ asset('images/hero_logo.svg') }}" alt="BEKKAS" class="h-28 w-auto">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-10 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <!-- Flash Messages (canonical; supports success|error|warning|info) -->
                @include('partials.flash', ['forceInline' => true])

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
