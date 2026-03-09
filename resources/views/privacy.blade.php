@section('title', config('app.name', 'BEKKAS') . ' - ' . (t('legal.privacy.title') ?: 'Privacy Policy'))

<x-app-layout>

        <section class="py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold mb-6">{{ t('legal.privacy.title') ?: 'Privacy Policy' }}</h1>

                <p class="prose">{!! nl2br(e(t('legal.privacy_policy') ?: 'Privacy policy text.')) !!}</p>

                <h2 id="cookies" class="mt-8 text-2xl font-semibold">{{ t('legal.cookies.title') ?: 'Cookies Policy' }}</h2>
                <div class="prose mt-4">{!! nl2br(e(t('legal.cookies_policy') ?: 'Cookies policy text.')) !!}</div>

                <p class="mt-8 text-sm text-grey-dark">{{ t('legal.privacy.last_updated') ?: 'Last updated: February 2026' }}</p>
            </div>
            </div>
        </section>

</x-app-layout>
