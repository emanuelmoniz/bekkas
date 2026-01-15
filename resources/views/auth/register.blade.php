<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Confirm Email Address -->
        <div class="mt-4">
            <x-input-label for="email_confirmation" :value="__('Confirm Email')" />
            <x-text-input id="email_confirmation" class="block mt-1 w-full" type="email" name="email_confirmation" :value="old('email_confirmation')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Google reCAPTCHA -->
        <div class="mt-4">
            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
        </div>

        <script>
        (function(){
            var script = document.currentScript;
            var container = (script && script.previousElementSibling && script.previousElementSibling.classList && script.previousElementSibling.classList.contains('g-recaptcha')) ? script.previousElementSibling : (script && script.parentElement && script.parentElement.querySelector('.g-recaptcha')) || document.querySelector('.g-recaptcha[data-sitekey]');
            if (!container) { console.debug('[recaptcha] container not found (register)'); return; }

            function loadRecaptcha(){
                if (window.__recaptchaLazyLoaded) return;
                window.__recaptchaLazyLoaded = true;
                console.debug('[recaptcha] loading script (register)');
                var s = document.createElement('script');
                s.src = 'https://www.google.com/recaptcha/api.js';
                s.async = true; s.defer = true;
                s.onload = function(){
                    console.debug('[recaptcha] script loaded (register)');
                    try{
                        var key = container.getAttribute('data-sitekey');
                        if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && !container.querySelector('iframe')) {
                            window.grecaptcha.render(container, { 'sitekey': key });
                            console.debug('[recaptcha] rendered (register)');
                        }
                    } catch(e) { console.error('[recaptcha] render error (register)', e); }
                };
                s.onerror = function(e){ console.error('[recaptcha] failed to load (register)', e); };
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
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ t('auth.already_registered') ?: 'Already registered?' }}
            </a>

            <x-primary-button class="ms-4">
                {{ t('auth.register') ?: 'Register' }}
            </x-primary-button>
        </div>
    </form>


</x-guest-layout>
