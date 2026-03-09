<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.meta')
        @include('partials.title')
        @include('partials.favicons')

        <x-favorites-init />
        <x-cart-init />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Per-page head (styles/scripts) -->
        @stack('head')

        <!-- Google reCAPTCHA (loaded only on pages that request it) -->
        @stack('recaptcha')
    </head>
    @php $serverFlash = session('success') ?? session('error') ?? session('warning') ?? session('info'); @endphp
    <body class="font-sans antialiased {{ request()->is('admin*') ? 'admin-view' : 'client-view' }}" @if($serverFlash) data-server-flash="{{ e($serverFlash) }}" @endif>
        <div class="min-h-screen bg-light flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages (single canonical source; supports success|error|warning|info) -->
            @include('partials.flash')

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
        @stack('scripts')
    </body>
</html>
