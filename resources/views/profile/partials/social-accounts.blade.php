<div class="p-6 bg-light border rounded shadow-sm">
    <h2 class="text-lg font-medium text-dark">{{ t('profile.social_accounts') ?: 'Social accounts' }}</h2>
    <p class="mt-1 text-sm text-grey-dark">{{ t('profile.social_accounts_desc') ?: 'Link external accounts (Google) to sign in quickly.' }}</p>

    <div class="mt-6 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <img src="/images/google-logo.svg" alt="Google" class="h-6 w-6 me-3">
                <div>
                    <div class="font-medium">Google</div>
                    <div class="text-xs text-grey-medium">{{ t('profile.social_google_desc') ?: 'Use your Google account to sign in.' }}</div>
                </div>
            </div>

            <div>
                @if(auth()->user()->socialAccounts()->where('provider','google')->exists())
                    <form method="POST" action="{{ route('profile.social.unlink', 'google') }}" onsubmit="return confirm('{{ t('profile.confirm_unlink') ?: 'Are you sure you want to unlink Google?' }}')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ t('profile.unlink_account') ?: 'Unlink' }}</x-danger-button>
                    </form>
                @else
                    @if(config('services.google.enabled'))
                        <a href="{{ route('profile.social.link', 'google') }}" class="inline-flex items-center">
                            <x-primary-button>{{ t('profile.link_account') ?: 'Link account' }}</x-primary-button>
                        </a>
                    @else
                        <div class="text-xs text-grey-medium">{{ t('profile.provider_disabled') ?: 'Google sign-in is currently disabled.' }}</div>
                    @endif
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <img src="/images/microsoft-logo.svg" alt="Microsoft" class="h-6 w-6 me-3">
                <div>
                    <div class="font-medium">Microsoft</div>
                    <div class="text-xs text-grey-medium">{{ t('profile.social_microsoft_desc') ?: 'Use your Microsoft account to sign in.' }}</div>
                </div>
            </div>

            <div>
                @if(auth()->user()->socialAccounts()->where('provider','microsoft')->exists())
                    <form method="POST" action="{{ route('profile.social.unlink', 'microsoft') }}" onsubmit="return confirm('{{ t('profile.confirm_unlink') ?: 'Are you sure you want to unlink this social account?' }}')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ t('profile.unlink_account') ?: 'Unlink' }}</x-danger-button>
                    </form>
                @else
                    @if(config('services.microsoft.enabled'))
                        <a href="{{ route('profile.social.link', 'microsoft') }}" class="inline-flex items-center">
                            <x-primary-button>{{ t('profile.link_account') ?: 'Link account' }}</x-primary-button>
                        </a>
                    @else
                        <div class="text-xs text-grey-medium">{{ t('profile.provider_disabled') ?: 'Microsoft sign-in is currently disabled.' }}</div>
                    @endif
                @endif
            </div>
        </div>

    </div>


</div>