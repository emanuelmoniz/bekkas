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
        <meta name="theme-color" content="#ffffff">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <x-favorites-init />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/css/home-splash.css', 'resources/js/app.js'])
        @endif

        <!-- Google reCAPTCHA (loaded only on pages that request it) -->
        @stack('recaptcha')
    </head>
    <body class="bg-white dark:bg-[#161615] text-gray-900 dark:text-gray-100 overflow-hidden" data-splash-active="true">
        @include('layouts.navigation')

        <!-- HOME SPLASH INTRO -->
        <div id="home-splash" class="home-splash-overlay" role="dialog" aria-label="{{ config('app.name', 'BEKKAS') }} splash">
            <img src="{{ asset('images/hero_logo.svg') }}" alt="{{ config('app.name', 'BEKKAS') }}" class="home-splash-logo" />
        </div>

        <!-- BANNER SECTION -->
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-gray-900">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner.jpg') }}')">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-white px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ t('home.banner.tagline') ?: 'Printing Life layer by layer' }}</h1>
                <a href="#services" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded font-semibold transition-colors">
                    {{ t('home.banner.button') ?: 'OUR SERVICES' }}
                </a>
            </div>
        </section>

        <!-- SERVICES SECTION -->
        <section id="services" class="py-16 md:py-24 bg-white dark:bg-[#161615] px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    
                    <!-- Products Card -->
                    @if(config('app.store_enabled'))
                        <a href="{{ route('store.index') }}" class="group">
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                                <div class="w-full h-64 md:h-80 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1569163139394-de4798aa62b1?w=500&h=400&fit=crop')"></div>
                                <div class="p-6 flex flex-col flex-grow">
                                    <h3 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">{{ t('home.services.store.title') ?: 'STORE' }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6 flex-grow">{{ t('home.services.store.description') ?: 'Day to day life objects, gifts, souvenires' }}</p>
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium transition-colors">
                                        {{ t('home.services.store.button') ?: 'Store' }}
                                    </button>
                                </div>
                            </div>
                        </a>
                    @endif

                    <!-- Architecture Card -->
                    <a href="{{ route('tickets.index') }}" class="group">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                            <div class="w-full h-64 md:h-80 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=400&fit=crop')"></div>
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">{{ t('home.services.custom.title') ?: 'CUSTOM' }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6 flex-grow">{{ t('home.services.custom.description') ?: 'Printing service for architects and architecture students including modeling and file preparation.' }}</p>
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium transition-colors">
                                    {{ t('home.services.custom.button') ?: 'More info' }}
                                </button>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </section>

        <!-- CONTACT SECTION -->
        <section id="contact" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900 px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold mb-12 text-center text-gray-900 dark:text-white">{{ t('home.contact.title') ?: 'Get in Touch' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    
                    <!-- Contact Info -->
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('home.contact.location') ?: 'Location' }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">Lisbon, Portugal</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('home.contact.phone') ?: 'Phone' }}</h3>
                            <a href="https://wa.me/351965707800" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                +351 965 707 800 (WhatsApp)
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('home.contact.email') ?: 'Email' }}</h3>
                            <a href="mailto:{{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                {{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ t('home.contact.social') ?: 'Follow Us' }}</h3>
                            <div class="flex gap-4">
                                <a href="https://instagram.com/bekkas_pt" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                    Instagram
                                </a>
                                <a href="https://www.makerworld.com/en/makers/bekkas" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                    Makerworld
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                        @include('partials.flash')

                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('contact.name') ?: 'Name' }}
                                </label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('contact.email') ?: 'Email' }}
                                </label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('contact.message') ?: 'Message' }}
                                </label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                            </div>

                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="text-primary text-sm mt-1">{{ $message }}</p>
                            @enderror

                            <script>
                            (function(){
                                var script = document.currentScript;
                                var container = (script && script.previousElementSibling && script.previousElementSibling.classList && script.previousElementSibling.classList.contains('g-recaptcha')) ? script.previousElementSibling : (script && script.parentElement && script.parentElement.querySelector('.g-recaptcha')) || document.querySelector('.g-recaptcha[data-sitekey]');
                                if (!container) { console.debug('[recaptcha] container not found (welcome)'); return; }

                                function loadRecaptcha(){
                                    if (window.__recaptchaLazyLoaded) return;
                                    window.__recaptchaLazyLoaded = true;
                                    console.debug('[recaptcha] loading script (welcome)');
                                    var s = document.createElement('script');
                                    s.src = 'https://www.google.com/recaptcha/api.js';
                                    s.async = true; s.defer = true;
                                    s.onload = function(){
                                        console.debug('[recaptcha] script loaded (welcome)');
                                        try{
                                            var key = container.getAttribute('data-sitekey');
                                            if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && !container.querySelector('iframe')) {
                                                window.grecaptcha.render(container, { 'sitekey': key });
                                                console.debug('[recaptcha] rendered (welcome)');
                                            }
                                        } catch(e) { console.error('[recaptcha] render error (welcome)', e); }
                                    };
                                    s.onerror = function(e){ console.error('[recaptcha] failed to load (welcome)', e); };
                                    document.head.appendChild(s);
                                }

                                container.addEventListener('click', loadRecaptcha, {once:true});
                                container.addEventListener('mouseenter', loadRecaptcha, {once:true});
                                var f = container.closest('form'); if (f){
                                    f.addEventListener('submit', loadRecaptcha, {once:true});
                                    f.addEventListener('focusin', loadRecaptcha, {once:true});
                                    f.querySelectorAll('input, textarea, button, select').forEach(function(el){ el.addEventListener('focus', loadRecaptcha, {once:true}); });
                                }

                                if ('IntersectionObserver' in window) {
                                    var io = new IntersectionObserver(function(entries){
                                        entries.forEach(function(entry){ if (entry.isIntersecting) { loadRecaptcha(); io.disconnect(); } });
                                    }, {rootMargin: '200px'});
                                    io.observe(container);
                                }
                            })();
                            </script>

                            <button type="submit" 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                                {{ t('contact.send') ?: 'Send Message' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </section>

        @include('layouts.footer')

        <!-- Home splash dismiss logic (scroll/click/keypress to reveal) -->
        <script>
        (function(){
            var splash = document.getElementById('home-splash');
            if (!splash) return;
            var dismissed = false;

            // If the page was opened with the contact anchor, mark for auto-dismissal
            var __autoDismissSplashForContact = (window.location && window.location.hash === '#contact');


            function hideSplash() {
                if (dismissed) return;
                dismissed = true;
                splash.classList.add('home-splash-hidden');
                document.body.classList.remove('overflow-hidden');

                // cleanup listeners
                try {
                    window.removeEventListener('wheel', onFirstIntent, {passive:true});
                    window.removeEventListener('touchstart', onFirstIntent, {passive:true});
                } catch(e) {}
                window.removeEventListener('keydown', onKeyDown);
                splash.removeEventListener('click', onFirstIntent);

                // remove from DOM after transition
                setTimeout(function(){ if (splash && splash.parentNode) splash.parentNode.removeChild(splash); }, 650);
            }

            function onFirstIntent() { hideSplash(); }
            function onKeyDown(e) {
                var keys = ['ArrowDown','PageDown',' ','Enter'];
                if (keys.indexOf(e.key) !== -1) hideSplash();
            }

            // Dismiss when user intends to scroll / touch / press keys or clicks the splash
            window.addEventListener('wheel', onFirstIntent, {passive:true, once:true});
            window.addEventListener('touchstart', onFirstIntent, {passive:true, once:true});
            window.addEventListener('keydown', onKeyDown, {once:true});
            splash.addEventListener('click', onFirstIntent, {once:true});

            // Hide splash if the fragment is set while already on the page (e.g. clicking nav Contact)
            window.addEventListener('hashchange', function(){ if (window.location.hash === '#contact') { hideSplash(); var t = document.getElementById('contact'); if (t) t.scrollIntoView(); } });

            // If requested via URL fragment #contact, dismiss immediately and scroll to section
            if (typeof __autoDismissSplashForContact !== 'undefined' && __autoDismissSplashForContact) {
                hideSplash();
                var target = document.getElementById('contact');
                if (target) { target.scrollIntoView(); }
                return;
            }

            // Safety auto-dismiss after 3s so the site becomes reachable for keyboard-only users
            setTimeout(hideSplash, 3000);
        })();
        </script>

@push('recaptcha')
<script>
(function(){
    if (window.__recaptchaLazyLoaded) return;

    function init() {
        if (window.__recaptchaLazyLoaded) return;

        function loadRecaptcha(){
            if (window.__recaptchaLazyLoaded) return;
            window.__recaptchaLazyLoaded = true;
            console.debug('[recaptcha] loading script');
            var s = document.createElement('script');
            s.src = 'https://www.google.com/recaptcha/api.js';
            s.async = true; s.defer = true;
            s.onload = function() {
                console.debug('[recaptcha] script loaded, attempting to render widgets');
                containers.forEach(function(c){
                    var key = c.getAttribute('data-sitekey');
                    if (!key) {
                        console.warn('[recaptcha] missing data-sitekey on container', c);
                        return;
                    }

                    try {
                        if (window.grecaptcha && typeof window.grecaptcha.render === 'function') {
                            if (!c.querySelector('iframe')) {
                                window.grecaptcha.render(c, { 'sitekey': key });
                                console.debug('[recaptcha] rendered widget for container', c);
                            }
                        }
                    } catch (e) {
                        console.error('[recaptcha] render error', e);
                    }
                });
            };
            s.onerror = function(e){ console.error('[recaptcha] failed to load', e); };
            document.head.appendChild(s);
        }

        var containers = document.querySelectorAll('.g-recaptcha');
        if (!containers.length) return;

        var forms = new Set();
        containers.forEach(function(c){
            c.addEventListener('click', loadRecaptcha, {once:true});
            c.addEventListener('mouseenter', loadRecaptcha, {once:true});
            var f = c.closest('form'); if (f) forms.add(f);
        });

        forms.forEach(function(f){
            f.addEventListener('submit', loadRecaptcha, {once:true});
            f.addEventListener('focusin', loadRecaptcha, {once:true});
            f.querySelectorAll('input, textarea, button, select').forEach(function(el){
                el.addEventListener('focus', loadRecaptcha, {once:true});
            });
        });

        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if (entry.isIntersecting) {
                        loadRecaptcha();
                        io.disconnect();
                    }
                });
            }, {rootMargin: '200px'});
            containers.forEach(function(c){ io.observe(c); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush
    </body>
</html>
