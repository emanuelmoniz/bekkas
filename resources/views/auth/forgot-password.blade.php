<x-guest-layout>
    <div class="mb-4 text-sm text-grey-dark mt-3">
        {{ t('auth.forgot_password_desc') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}"
          novalidate
          data-auth-validation="true"
            data-has-server-errors="{{ $errors->any() ? '1' : '0' }}"
          data-msg-email-invalid="{{ t('validation.email_invalid') ?: 'Please enter a valid email address.' }}"
          data-msg-validation-failed="{{ t('contact.validation_failed') ?: 'Please correct the errors below and try again.' }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="t('auth.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-primary-cta fullWidth>
                {{ t('auth.email_reset_link') }}
            </x-primary-cta>
        </div>
    </form>
</x-guest-layout>
