<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Order {{ $order->order_number }} — Payment</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Easypay inline container (SDK only) --}}
        @if(config('easypay.enabled') && env('EASYPAY_SDK_URL'))
            <div id="easypay-inline-root" class="bg-white shadow rounded p-4" aria-live="polite">
                <h3 class="font-semibold mb-2">Payment widget</h3>

                <div id="easypay-checkout" class="min-h-[120px] flex items-center justify-center text-sm text-gray-600">
                    <span id="easypay-checkout-loading">{{ t('checkout.pay.loading_widget') ?: 'Loading payment widget…' }}</span>
                </div>

                {{-- persist-ed manifest for the latest active/pending session (if present) — server still persists manifests on order placement; the SDK will use it if present --}}
                @if(! empty($activeManifest))
                    <script id="easypay-manifest" type="application/json">@json($activeManifest)</script>
                @endif

                {{-- SDK (async) + minimal bootstrap for inline mode; no dev-only payload/session UI --}}
                <script async src="{{ env('EASYPAY_SDK_URL') }}" integrity="" crossorigin="anonymous"></script>

                <script>
                    (function () {
                        const manifestEl = document.getElementById('easypay-manifest');
                        if (!manifestEl) return; // nothing to initialise

                        let manifest = null;
                        try { manifest = JSON.parse(manifestEl.textContent); } catch (err) { console.error('Invalid Easypay manifest in page', err); return; }

                        const testing = @json(config('easypay.env') === 'test');
                        const mount = document.getElementById('easypay-checkout');
                        const orderVerifyUrl = @json(url("/orders/{$order->uuid}/pay/verify"));

                        // Try known SDK globals and start inline checkout. Keep implementation minimal and robust.
                        const starterCandidates = [
                            () => window.easypayCheckout?.startCheckout,
                            () => window.easypayCheckout?.start,
                            () => window.Easypay?.startCheckout,
                            () => window.startCheckout,
                        ];

                        function tryStart() {
                            for (const cand of starterCandidates) {
                                try {
                                    const fn = cand();
                                    if (typeof fn === 'function') {
                                        fn(manifest, {
                                            display: 'inline',
                                            testing: testing,
                                            container: '#easypay-checkout',
                                            showLoading: true,
                                            onSuccess: function (checkoutInfo) {
                                                // Best-effort server verify; server side will mark paid if valid
                                                fetch(orderVerifyUrl, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                                                    body: JSON.stringify({ checkout: checkoutInfo })
                                                }).then(r => r.json()).then(j => { if (j?.ok) window.location.reload(); }).catch(() => {/* ignore */});
                                            },
                                            onError: function () { mount.classList.add('border', 'border-red-200'); mount.innerText = 'Payment widget failed to load — please try again.'; },
                                            onPaymentError: function () { alert('Payment failed — please try another method or contact support.'); }
                                        });

                                        const loader = document.getElementById('easypay-checkout-loading');
                                        if (loader && loader.parentNode) loader.parentNode.removeChild(loader);
                                        return true;
                                    }
                                } catch (err) { /* continue */ }
                            }

                            return false;
                        }

                        if (!tryStart()) {
                            const maxWait = 10000;
                            const start = Date.now();
                            const i = setInterval(() => {
                                if (tryStart() || Date.now() - start > maxWait) clearInterval(i);
                            }, 150);
                        }
                    })();
                </script>
            </div>
        @endif

        <div class="text-right">
            <a href="{{ route('orders.show', $order->uuid) }}" class="text-sm text-gray-600 hover:underline">Back to order</a>
        </div>
    </div>
</x-app-layout>
