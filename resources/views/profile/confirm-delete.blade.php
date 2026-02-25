<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            {{ t('profile.delete_by_email_subject') ?: 'Confirm account deletion' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-dark">{{ t('profile.delete_by_email_intro') ?: 'Click the button below to confirm deletion of your account.' }}</h3>

                <form method="post" action="{{ url()->full() }}" class="mt-6">
                    @csrf

                    <div class="flex justify-end">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-grey-light border border-transparent rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest hover:bg-grey-medium">{{ t('profile.cancel') ?: 'Cancel' }}</a>

                        <button type="submit" class="ms-3 inline-flex items-center px-4 py-2 bg-grey-light border border-transparent rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest hover:bg-grey-light/90">{{ t('profile.delete_by_email_action') ?: 'Delete my account' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
