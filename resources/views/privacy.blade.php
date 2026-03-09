@section('title', config('app.name', 'BEKKAS') . ' - ' . (t('legal.privacy.title') ?: 'Privacy Policy'))

<x-app-layout>

        <section class="py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold mb-6">{{ t('legal.privacy.title') ?: 'Privacy Policy' }}</h1>

                <p>{{ t('legal.privacy.content') ?: 'We collect and use personal information to provide our services. We respect your privacy and process data according to applicable law.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.privacy.section_data_title') ?: 'Data We Collect' }}</h2>
                <p>{{ t('legal.privacy.section_data') ?: 'We collect data you provide (account details, order information) and technical data (cookies, analytics) to operate the service.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.privacy.section_usage_title') ?: 'How We Use Data' }}</h2>
                <p>{{ t('legal.privacy.section_usage') ?: 'Personal data is used to process orders, communicate with you, and improve the service. We do not sell personal data to third parties.' }}</p>

                <p class="mt-8 text-sm text-grey-dark">{{ t('legal.privacy.last_updated') ?: 'Last updated: February 2026' }}</p>
            </div>
            </div>
        </section>

</x-app-layout>
