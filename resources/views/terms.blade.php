@section('title', t('legal.terms.title') ?: 'Service Terms | Termos de Serviço')

<x-app-layout>

        <section class="py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold mb-6">{{ t('legal.terms.title') ?: 'Service Terms | Termos de Serviço' }}</h1>

                <p>{{ t('legal.terms.content') ?: 'These Terms of Service govern your use of the site. By creating an account and using our services you agree to comply with these terms.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.terms.section_usage_title') ?: 'Use of the Service' }}</h2>
                <p>{{ t('legal.terms.section_usage') ?: 'You agree to use the service in compliance with applicable laws and not to misuse the platform. We may suspend or terminate accounts that violate these terms.' }}</p>

                <h2 class="mt-8 text-2xl font-semibold">{{ t('legal.terms.section_limitation_title') ?: 'Limitation of Liability' }}</h2>
                <p>{{ t('legal.terms.section_limitation') ?: 'Our liability is limited as permitted by law. We provide services on an as-is basis and disclaim certain warranties.' }}</p>

                <h2 id="returns" class="mt-8 text-2xl font-semibold">{{ t('legal.terms.section_returns_title') ?: 'Return and Refunds Policy | Política de Devoluções e Reembolsos' }}</h2>
                <p>{{ t('legal.terms.section_returns') ?: 'Description of the return and refunds policy.' }}</p>

                <h2 id="shipping" class="mt-8 text-2xl font-semibold">{{ t('legal.terms.section_shipping_title') ?: 'Shipping Policy | Politica de Envios' }}</h2>
                <p>{{ t('legal.terms.section_shipping') ?: 'Description of the shipping policy.' }}</p>

                <p class="mt-8 text-sm text-grey-dark">{{ t('legal.terms.last_updated') ?: 'Last updated: February 2026' }}</p>
            </div>
            </div>
        </section>

</x-app-layout>
