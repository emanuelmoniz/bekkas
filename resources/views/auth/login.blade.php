<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('unverified_email'))
        <div class="mb-4 text-sm text-red-600">
            {{ t('auth.email_unverified_notice') ?: 'Your account has not been confirmed. Check your email for the verification link.' }}
            <div class="mt-2 text-xs text-gray-600">{{ t('auth.check_spam') ?: 'If you do not see the message, please check your spam folder.' }}</div>
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
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ t('auth.remember_me') ?: 'Remember me' }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ t('auth.forgot_password') ?: 'Forgot your password?' }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ t('auth.login') ?: 'Log in' }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">{{ t('auth.not_a_user') ?: 'Not a user?' }}</span>
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                {{ t('auth.please_register') ?: 'Please register' }}
            </a>
        </div>
    </form>
</x-guest-layout>
