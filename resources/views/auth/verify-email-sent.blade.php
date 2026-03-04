<x-guest-layout>
    <div class="mb-4 text-sm text-grey-dark">
        {{ t('auth.verify_sent_message') ?: 'Thanks for signing up! Please check your email for a verification link to confirm your account.' }}
    </div>

    <div class="mb-4 text-sm text-grey-dark">
        {{ t('auth.check_spam') ?: 'If you do not see the message, please check your spam folder.' }}
    </div>



    <form method="POST" action="{{ route('verification.resend.guest') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') ?: old('email') }}">

        <x-default-button>
            {{ t('auth.resend_activation') ?: 'Resend activation email' }}
        </x-default-button>
    </form>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="text-sm text-accent-primary hover:text-accent-primary/90 no-underline">{{ t('auth.login') ?: 'Log in' }}</a>
    </div>
</x-guest-layout>
