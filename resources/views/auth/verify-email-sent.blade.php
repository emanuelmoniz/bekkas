<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ t('auth.verify_sent_message') ?: 'Thanks for signing up! Please check your email for a verification link to confirm your account.' }}
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ t('auth.check_spam') ?: 'If you do not see the message, please check your spam folder.' }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ t('auth.activation_sent') ?: 'A new activation email has been sent to your email address.' }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend.guest') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') ?: old('email') }}">

        <x-primary-button>
            {{ t('auth.resend_activation') ?: 'Resend activation email' }}
        </x-primary-button>
    </form>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">{{ t('auth.login') ?: 'Log in' }}</a>
    </div>
</x-guest-layout>
