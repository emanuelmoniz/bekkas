<x-guest-layout>
    <div class="text-center">
        <h1 class="mt-6 text-3xl font-extrabold text-gray-900">{{ t('error.500.title') }}</h1>
        <p class="mt-3 text-gray-600">{{ t('error.500.message') }}</p>

        <div class="mt-6">
            <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">{{ t('error.back_home') }}</a>
        </div>

        <p class="mt-4 text-sm text-gray-500">{!! t('error.contact_support', ['email' => '<a href="mailto:'.e(config('mail.contact_address')).'">'.e(config('mail.contact_address')).'</a>']) !!}</p>
    </div>
</x-guest-layout>
