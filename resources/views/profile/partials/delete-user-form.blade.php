<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ t('profile.delete_account') ?: 'Delete Account' }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ t('profile.delete_account_desc') ?: 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.' }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ t('profile.delete_account_button') ?: 'Delete Account' }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        {{-- If the user is social-only (no local password) show the "send deletion link" path --}}
        @if (auth()->user()->socialAccounts()->exists())
            @if (session('status') === 'deletion-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ t('profile.delete_by_email_sent') ?: 'A deletion link has been sent to your email address.' }}
                </div>
            @endif

            <form method="post" action="{{ route('profile.delete.request') }}" class="p-6">
                @csrf

                <h2 class="text-lg font-medium text-gray-900">
                    {{ t('profile.confirm_delete') ?: 'Are you sure you want to delete your account?' }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ t('profile.delete_by_email_desc') ?: "Don't have a password? We'll email you a secure link to confirm account deletion." }}
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ t('profile.cancel') ?: 'Cancel' }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ t('profile.delete_by_email_button') ?: 'Send deletion link' }}
                    </x-danger-button>
                </div>
            </form>
        @else
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    {{ t('profile.confirm_delete') ?: 'Are you sure you want to delete your account?' }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ t('profile.confirm_delete_desc') ?: 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.' }}
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ t('profile.cancel') ?: 'Cancel' }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ t('profile.delete_account_button') ?: 'Delete Account' }}
                    </x-danger-button>
                </div>
            </form>
        @endif
    </x-modal> 
</section>
