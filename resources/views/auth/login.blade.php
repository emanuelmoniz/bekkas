<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Social login -->
    <div class="mb-4 text-center space-y-2">
        @if(config('services.google.enabled') && \Illuminate\Support\Facades\Route::has('login.provider'))
            <a href="{{ route('login.provider', 'google') }}" class="inline-flex items-center justify-center w-full border rounded px-3 py-2 bg-white hover:bg-white">
                <img src="/images/google-logo.svg" alt="Google" class="me-2 h-5 w-5">
                {{ t('auth.continue_with_google') ?: 'Continue with Google' }}
            </a>
        @endif

        @if(config('services.microsoft.enabled') && \Illuminate\Support\Facades\Route::has('login.provider'))
            <a href="{{ route('login.provider', 'microsoft') }}" class="inline-flex items-center justify-center w-full border rounded px-3 py-2 bg-white hover:bg-white">
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
            {{ t('auth.email_unverified_notice') ?: 'Your account has not been confirmed. Check your email for the verification link.' }}
            <div class="mt-2 text-xs text-grey-dark">{{ t('auth.check_spam') ?: 'If you do not see the message, please check your spam folder.' }}</div>
        </div>

        <form method="POST" action="{{ route('verification.resend.guest') }}" class="mb-6">
            @csrf
            <input type="hidden" name="email" value="{{ session('unverified_email') }}">
            <x-primary-button>
                {{ t('auth.resend_activation') ?: 'Resend activation email' }}
            </x-primary-button>
        </form>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="t('auth.email') ?: 'Email'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="t('auth.password') ?: 'Password'" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-grey-medium text-accent-primary shadow-sm focus:ring-accent-primary" name="remember">
                <span class="ms-2 text-sm text-grey-dark">{{ t('auth.remember_me') ?: 'Remember me' }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-grey-dark hover:text-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-primary" href="{{ route('password.request') }}">
                    {{ t('auth.forgot_password') ?: 'Forgot your password?' }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ t('auth.login') ?: 'Log in' }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <span class="text-sm text-grey-dark">{{ t('auth.not_a_user') ?: 'Not a user?' }}</span>
            <a class="underline text-sm text-grey-dark hover:text-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-primary" href="{{ route('register') }}">
                {{ t('auth.please_register') ?: 'Please register' }}
            </a>
        </div>
    </form>
</x-guest-layout>
