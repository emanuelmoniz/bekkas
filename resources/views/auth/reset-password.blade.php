<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}"
          novalidate
          data-auth-validation="true"
            data-has-server-errors="{{ $errors->any() ? '1' : '0' }}"
          data-msg-email-invalid="{{ t('validation.email_invalid') ?: 'Please enter a valid email address.' }}"
          data-msg-validation-failed="{{ t('contact.validation_failed') ?: 'Please correct the errors below and try again.' }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mt-3">
            <x-input-label for="email" :value="t('auth.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="t('auth.password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="t('auth.confirm_password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-primary-cta fullWidth>
                {{ t('auth.reset_password_button') }}
            </x-primary-cta>
        </div>
    </form>
</x-guest-layout>
