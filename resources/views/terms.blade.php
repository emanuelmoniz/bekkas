@section('title', t('legal.terms.title') ?: 'Service Terms | Termos de Serviço')

<x-app-layout>

        <section class="py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold mb-6">{{ t('legal.terms.title') ?: 'Service Terms | Termos de Serviço' }}</h1>

                <p>{!! nl2br(e(t('legal.terms.content', ['company' => config('app.name')] ) ?: 'These Terms of Service govern your use of the site. By creating an account and using our services you agree to comply with these terms.')) !!}</p>

                <h2 id="shipping" class="mt-8 text-2xl font-semibold">{{ t('footer.shipping_policy') ?: 'Shipping Policy' }}</h2>
                <div class="prose mt-4">{!! nl2br(e(t('legal.shipping_policy', ['company' => config('app.name')]) ?: 'Shipping policy text.')) !!}</div>

                <h2 id="returns" class="mt-8 text-2xl font-semibold">{{ t('footer.return_refunds') ?: 'Return and Refunds Policy' }}</h2>
                <div class="prose mt-4">{!! nl2br(e(t('legal.returns_policy', ['company' => config('app.name')]) ?: 'Returns policy text.')) !!}</div>

                <p class="mt-8 text-sm text-grey-dark">{{ t('legal.terms.last_updated') ?: 'Last updated: February 2026' }}</p>
            </div>
            </div>
        </section>

</x-app-layout>
