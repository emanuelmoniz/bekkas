<x-guest-layout>
    <div class="text-center">
        <h1 class="mt-6 text-3xl font-extrabold text-dark">{{ t('error.500.title') }}</h1>
        <p class="mt-3 text-grey-dark">{{ t('error.500.message') }}</p>

        <div class="mt-6">
            <x-primary-cta as="a" :href="url('/')">{{ t('error.back_home') }}</x-primary-cta>
        </div>

        <p class="mt-4 text-sm text-grey-medium">{!! t('error.contact_support', ['email' => '<a href="mailto:'.e(config('mail.contact_address')).'" class="text-accent-primary hover:text-accent-primary/90 no-underline">'.e(config('mail.contact_address')).'</a>']) !!}</p>
    </div>
</x-guest-layout>
