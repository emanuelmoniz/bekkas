<section>
    <header>
        <h2 class="text-lg font-medium text-dark">
            @if(auth()->user()->hasPassword())
                {{ t('profile.update_password') ?: 'Update Password' }}
            @else
                {{ t('profile.set_password') ?: 'Set Password' }}
            @endif
        </h2>

        <p class="mt-1 text-sm text-grey-dark">
            @if(auth()->user()->hasPassword())
                {{ t('profile.update_password_desc') ?: 'Ensure your account is using a long, random password to stay secure.' }}
            @else
                {{ t('profile.set_password_desc') ?: 'You signed in using a social provider. Add a password to also be able to sign in with your email.' }}
            @endif
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <input type="email" name="email" value="{{ auth()->user()->email }}" autocomplete="username" class="sr-only" tabindex="-1">

        @if(auth()->user()->hasPassword())
        <div>
            <x-input-label for="update_password_current_password" :value="t('profile.current_password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
        </div>
        @endif

        <div>
            <x-input-label for="update_password_password" :value="t('profile.new_password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="t('profile.confirm_password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-cta>{{ t('profile.save') ?: 'Save' }}</x-primary-cta>


        </div>
    </form>
</section>
