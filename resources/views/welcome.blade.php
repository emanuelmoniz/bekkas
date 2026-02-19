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
    <body class="bg-light dark:bg-dark text-dark dark:text-grey-light overflow-hidden" data-splash-active="true">
        @include('layouts.navigation')

        <!-- HOME SPLASH INTRO -->
        <div id="home-splash" class="home-splash-overlay" role="dialog" aria-label="{{ config('app.name', 'BEKKAS') }} splash">
            <img src="{{ asset('images/hero_logo.svg') }}" alt="{{ config('app.name', 'BEKKAS') }}" class="home-splash-logo" />
        </div>

        <!-- BANNER SECTION -->
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-dark">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner.jpg') }}')">
                <div class="absolute inset-0 bg-dark/40"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-light px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ t('home.banner.tagline') ?: 'Printing Life layer by layer' }}</h1>
                <a href="#services" class="inline-block bg-accent-primary hover:bg-accent-primary/90 text-light px-8 py-3 rounded font-semibold transition-colors">
                    {{ t('home.banner.button') ?: 'OUR SERVICES' }}
                </a>
            </div>
        </section>

        <!-- SERVICES SECTION -->
        <section id="services" class="py-16 md:py-24 bg-light dark:bg-dark px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    
                    <!-- Products Card -->
                    @if(config('app.store_enabled'))
                        <a href="{{ route('store.index') }}" class="group">
                            <div class="bg-grey-light dark:bg-grey-dark rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                                <div class="w-full h-64 md:h-80 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1569163139394-de4798aa62b1?w=500&h=400&fit=crop')"></div>
                                <div class="p-6 flex flex-col flex-grow">
                                    <h3 class="text-2xl font-bold mb-3 text-dark dark:text-light">{{ t('home.services.store.title') ?: 'STORE' }}</h3>
                                    <p class="text-grey-dark dark:text-grey-medium mb-6 flex-grow">{{ t('home.services.store.description') ?: 'Day to day life objects, gifts, souvenires' }}</p>
                                    <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded font-medium transition-colors">
                                        {{ t('home.services.store.button') ?: 'Store' }}
                                    </button>
                                </div>
                            </div>
                        </a>
                    @endif

                    <!-- Architecture Card -->
                    <a href="{{ route('tickets.index') }}" class="group">
                        <div class="bg-grey-light dark:bg-grey-dark rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                            <div class="w-full h-64 md:h-80 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=400&fit=crop')"></div>
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-3 text-dark dark:text-light">{{ t('home.services.custom.title') ?: 'CUSTOM' }}</h3>
                                <p class="text-grey-dark dark:text-grey-medium mb-6 flex-grow">{{ t('home.services.custom.description') ?: 'Printing service for architects and architecture students including modeling and file preparation.' }}</p>
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
        <section id="contact" class="py-16 md:py-24 bg-light dark:bg-dark px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold mb-12 text-center text-dark dark:text-light">{{ t('home.contact.title') ?: 'Get in Touch' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    
                    <!-- Contact Info -->
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-dark dark:text-light mb-2">{{ t('home.contact.location') ?: 'Location' }}</h3>
                            <p class="text-grey-dark dark:text-grey-medium">Lisbon, Portugal</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark dark:text-light mb-2">{{ t('home.contact.phone') ?: 'Phone' }}</h3>
                            <a href="https://wa.me/351965707800" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary dark:text-accent-primary dark:hover:text-accent-primary font-medium">
                                +351 965 707 800 (WhatsApp)
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark dark:text-light mb-2">{{ t('home.contact.email') ?: 'Email' }}</h3>
                            <a href="mailto:{{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}" class="text-accent-primary hover:text-accent-primary dark:text-accent-primary dark:hover:text-accent-primary font-medium">
                                {{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark dark:text-light mb-4">{{ t('home.contact.social') ?: 'Follow Us' }}</h3>
                            <div class="flex gap-4">
                                <a href="https://instagram.com/bekkas_pt" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary dark:text-accent-primary dark:hover:text-accent-primary font-medium">
                                    Instagram
                                </a>
                                <a href="https://www.makerworld.com/en/makers/bekkas" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary dark:text-accent-primary dark:hover:text-accent-primary font-medium">
                                    Makerworld
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-light dark:bg-grey-dark rounded-lg shadow-lg p-8">
                        @include('partials.flash')

                        <form id="contact-form" method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-medium text-grey-dark dark:text-grey-medium mb-2">
                                    {{ t('contact.name') ?: 'Name' }}
                                </label>
                                <input type="text" id="name" name="name" required
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-2 rounded-lg border border-grey-medium dark:border-grey-dark dark:bg-light dark:text-dark focus:outline-none focus:border-accent-primary focus:ring-1 focus:ring-accent-primary">
                                @error('name')
                                    <p class="text-primary text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-grey-dark dark:text-grey-medium mb-2">
                                    {{ t('contact.email') ?: 'Email' }}
                                </label>
                                <input type="email" id="email" name="email" required
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-2 rounded-lg border border-grey-medium dark:border-grey-dark dark:bg-light dark:text-dark focus:outline-none focus:border-accent-primary focus:ring-1 focus:ring-accent-primary">
                                @error('email')
                                    <p class="text-primary text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-grey-dark dark:text-grey-medium mb-2">
                                    {{ t('contact.message') ?: 'Message' }}
                                </label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-2 rounded-lg border border-grey-medium dark:border-grey-dark dark:bg-light dark:text-dark focus:outline-none focus:border-accent-primary focus:ring-1 focus:ring-accent-primary">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-primary text-sm mt-1">{{ $message }}</p>
                                @enderror
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
                                    class="w-full bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-3 rounded-lg font-semibold transition-colors">
                                {{ t('contact.send') ?: 'Send Message' }}
                            </button>
                        </form>

                        <script>
                        (function(){
                            var form = document.getElementById('contact-form'); if (!form) return;

                            var emailInput = form.querySelector('input[name="email"]');
                            var nameInput = form.querySelector('input[name="name"]');
                            var messageInput = form.querySelector('textarea[name="message"]');

                            var messages = {
                                nameRequired: {!! json_encode(t('validation.name_required') ?: 'Please enter your name.') !!},
                                emailInvalid: {!! json_encode(t('validation.email_invalid') ?: 'Please enter a valid email address.') !!},
                                messageRequired: {!! json_encode(t('validation.message_required') ?: 'Please enter your message.') !!},
                                validationFailed: {!! json_encode(t('contact.validation_failed') ?: 'Please correct the errors below and try again.') !!}
                            };

                            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // simple client-side TLD check

                            function makeClientError(field, msg) {
                                field.setAttribute('aria-invalid', 'true');
                                field.classList.add('border-status-error', 'focus:border-status-error');

                                var existing = field.parentElement.querySelector('.js-client-error');
                                if (existing) { existing.textContent = msg; return; }

                                var p = document.createElement('p');
                                p.className = 'text-primary text-sm mt-1 js-client-error';
                                p.setAttribute('role','alert');
                                p.textContent = msg;
                                field.parentElement.appendChild(p);
                            }

                            function clearClientError(field) {
                                field.removeAttribute('aria-invalid');
                                field.classList.remove('border-status-error', 'focus:border-status-error');
                                var e = field.parentElement.querySelector('.js-client-error');
                                if (e) e.remove();
                            }

                            function validateClient() {
                                var ok = true;

                                if (!nameInput.value.trim()) { makeClientError(nameInput, messages.nameRequired); ok = false; } else clearClientError(nameInput);

                                var emailVal = (emailInput.value || '').trim();
                                if (!emailVal || !emailPattern.test(emailVal)) { makeClientError(emailInput, messages.emailInvalid); ok = false; } else clearClientError(emailInput);

                                if (!messageInput.value.trim()) { makeClientError(messageInput, messages.messageRequired); ok = false; } else clearClientError(messageInput);

                                return ok;
                            }

                            // on submit, validate client-side first
                            form.addEventListener('submit', function(ev){
                                if (!validateClient()) {
                                    ev.preventDefault();
                                    if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                                        Alpine.store('flash').showMessage(messages.validationFailed, 'error');
                                    } else {
                                        alert(messages.validationFailed);
                                    }

                                    // focus first invalid element
                                    var firstInvalid = form.querySelector('[aria-invalid="true"]');
                                    if (firstInvalid) firstInvalid.focus();
                                }
                            });

                            // clear client error as user types
                            [nameInput, emailInput, messageInput].forEach(function(f){
                                f.addEventListener('input', function(){
                                    // for email use pattern
                                    if (f === emailInput) {
                                        if (emailPattern.test(f.value.trim())) clearClientError(f);
                                    } else {
                                        if (f.value.trim()) clearClientError(f);
                                    }
                                });
                            });

                        })();
                        </script>
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
