<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Social login -->
        <div class="mb-4 text-center space-y-2">
            @if(config('services.google.enabled') && \Illuminate\Support\Facades\Route::has('login.provider'))
                <a href="{{ route('login.provider', 'google') }}" class="inline-flex items-center justify-center w-full border rounded px-3 py-2 bg-light hover:bg-light">
                    <img src="/images/google-logo.svg" alt="Google" class="me-2 h-5 w-5">
                    {{ t('auth.continue_with_google') ?: 'Continue with Google' }}
                </a>
            @endif

            @if(config('services.microsoft.enabled') && \Illuminate\Support\Facades\Route::has('login.provider'))
                <a href="{{ route('login.provider', 'microsoft') }}" class="inline-flex items-center justify-center w-full border rounded px-3 py-2 bg-light hover:bg-light">
                    <img src="/images/microsoft-logo.svg" alt="Microsoft" class="me-2 h-5 w-5">
                    {{ t('auth.continue_with_microsoft') ?: 'Continue with Microsoft' }}
                </a>
            @endif
        </div>

        {{-- Show social login errors (e.g. provider callback errors) --}}
        @if($errors->has('social'))
            <div class="mb-4 text-sm text-status-error">
                {{ $errors->first('social') }}
            </div>
        @endif

        @if(session('unverified_email'))
            <div class="mb-4 text-sm text-status-error">
                {{ t('auth.email_unverified_notice') ?: 'An account was previously registered with this email but not confirmed.' }}
                <div class="mt-1 text-xs text-grey-dark">{{ t('auth.check_spam') ?: 'If you do not see the message, please check your spam folder.' }}</div>
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

        @include('partials.recaptcha-loader')

        <!-- Terms & Privacy acceptance -->
        <div class="mt-4">
            <label class="flex items-start space-x-3">
                <input type="checkbox" name="accept_terms" value="1" {{ old('accept_terms') ? 'checked' : '' }} class="mt-1">
                <span class="text-sm text-grey-dark">
                    {!! t('auth.accept_terms_label', ['terms_url' => route('terms')]) ?: 'I accept the <a href="'.route('terms').'" target="_blank" rel="noopener">Terms of Service</a>.' !!}
                </span>
            </label>
            <x-input-error :messages="$errors->get('accept_terms')" class="mt-2" />
        </div>

        <div class="mt-3">
            <label class="flex items-start space-x-3">
                <input type="checkbox" name="accept_privacy" value="1" {{ old('accept_privacy') ? 'checked' : '' }} class="mt-1">
                <span class="text-sm text-grey-dark">
                    {!! t('auth.accept_privacy_label', ['privacy_url' => route('privacy')]) ?: 'I accept the <a href="'.route('privacy').'" target="_blank" rel="noopener">Privacy Policy</a>.' !!}
                </span>
            </label>
            <x-input-error :messages="$errors->get('accept_privacy')" class="mt-2" />
        </div>

            <a class="underline text-sm text-grey-dark hover:text-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-primary" href="{{ route('login') }}">
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
