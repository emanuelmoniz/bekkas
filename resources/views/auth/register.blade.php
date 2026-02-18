<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Social login -->
        <div class="mb-4 text-center">
            <a href="{{ route('login.provider', 'google') }}" class="inline-flex items-center justify-center w-full border rounded px-3 py-2 bg-white hover:bg-gray-50">
                <img src="/images/google-logo.svg" alt="Google" class="me-2 h-5 w-5">
                {{ t('auth.continue_with_google') ?: 'Continue with Google' }}
            </a>
        </div>

        @if(session('unverified_email'))
            <div class="mb-4 text-sm text-red-600">
                {{ t('auth.email_unverified_notice') ?: 'An account was previously registered with this email but not confirmed.' }}
                <div class="mt-1 text-xs text-gray-600">{{ t('auth.check_spam') ?: 'If you do not see the message, please check your spam folder.' }}</div>
            </div>

            <div class="mb-4">
                <x-primary-button form="resend-activation-form">
                    {{ t('auth.resend_activation') ?: 'Resend activation email' }}
                </x-primary-button>
            </div>
        @endif

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="t('auth.name') ?: 'Name'" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="t('auth.email') ?: 'Email'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Confirm Email Address -->
        <div class="mt-4">
            <x-input-label for="email_confirmation" :value="t('auth.confirm_email') ?: 'Confirm Email'" />
            <x-text-input id="email_confirmation" class="block mt-1 w-full" type="email" name="email_confirmation" :value="old('email_confirmation')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="t('auth.password') ?: 'Password'" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="t('auth.confirm_password') ?: 'Confirm Password'" />

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

    @if(session('unverified_email'))
        <form id="resend-activation-form" method="POST" action="{{ route('verification.resend.guest') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('unverified_email') }}">
        </form>
    @endif

</x-guest-layout>
