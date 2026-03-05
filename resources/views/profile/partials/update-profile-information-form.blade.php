<section>
    <header>
        <h2 class="text-lg font-medium text-dark">
            {{ t('profile.profile_information') ?: 'Profile Information' }}
        </h2>

        <p class="mt-1 text-sm text-grey-dark">
            {{ t('profile.update_profile_info_desc') ?: "Update your account's profile information and email address." }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-grey-dark">
                        {{ t('profile.email_unverified') ?: 'Your email address is unverified.' }}

                        <button form="send-verification" class="underline text-sm text-grey-dark hover:text-dark rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            {{ t('profile.resend_verification') ?: 'Click here to re-send the verification email.' }}
                        </button>
                    </p>


                </div>
            @endif
        </div>

        <div>
            <x-input-label for="email_confirmation" :value="__('Confirm Email')" />
            <x-text-input id="email_confirmation" name="email_confirmation" type="email" class="mt-1 block w-full" :value="old('email_confirmation', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email_confirmation')" />
        </div>

        <div>
            <x-input-label for="phone" :value="t('profile.phone') ?: 'Phone'" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="language" :value="t('profile.language') ?: 'Language'" />
            <select id="language" name="language" class="mt-1 block w-full border-grey-medium rounded-md shadow-sm">
                @foreach(config('app.locales') as $key => $label)
                    <option value="{{ $key }}" {{ old('language', $user->language ?? app()->getLocale()) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('language')" />
        </div>

        <div class="flex items-center gap-4">
            <x-default-button>{{ t('profile.save') ?: 'Save' }}</x-default-button>


        </div>
    </form>
</section>
