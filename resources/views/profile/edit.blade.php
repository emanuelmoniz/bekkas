<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    @include('profile.partials.social-accounts')
                </div>
            </div>

            <div id="password-section" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    @include('profile.partials.update-password-form')

                    <div class="mt-6 border-t pt-4">
                        <p class="text-sm text-gray-dark">{{ t('profile.password_reset_hint') }}</p>
                        <div class="flex items-center gap-4">
                            <x-optional-cta as="a" :href="route('password.request')" class="mt-2">
                                {{ t('profile.password_reset_cta') }}
                            </x-optional-cta>
                        </div>
                    </div>
                </div>
            </div>

        <div id="addresses-section" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    		<div class="max-w-full">
        	    @include('profile.partials.addresses', ['addresses' => auth()->user()->addresses()->orderByDesc('is_default')->get()])
    	    	</div>
	    </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
