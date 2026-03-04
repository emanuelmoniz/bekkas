<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            {{ t('profile.delete_by_email_subject') ?: 'Confirm account deletion' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-dark">{{ t('profile.delete_by_email_intro') ?: 'Click the button below to confirm deletion of your account.' }}</h3>

                <form method="post" action="{{ url()->full() }}" class="mt-6">
                    @csrf

                    <div class="flex justify-end">
                        <x-optional-cta as="a" :href="route('profile.edit')">{{ t('profile.cancel') ?: 'Cancel' }}</x-optional-cta>

                        <x-optional-cta type="submit">{{ t('profile.delete_by_email_action') ?: 'Delete my account' }}</x-optional-cta>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
