<x-guest-layout>
    <div class="text-center">
        <h1 class="mt-6 text-3xl font-extrabold text-dark">{{ t('error.404.title') }}</h1>
        <p class="mt-3 text-grey-dark">{{ t('error.404.message') }}</p>

        <div class="mt-6">
            <a href="{{ url('/') }}" class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-full uppercase text-white bg-primary hover:bg-primary/90">{{ t('error.back_home') }}</a>
        </div>

        <p class="mt-4 text-sm text-grey-medium">{!! t('error.contact_support', ['email' => '<a href="mailto:'.e(config('mail.contact_address')).'">'.e(config('mail.contact_address')).'</a>']) !!}</p>
    </div>
</x-guest-layout>
