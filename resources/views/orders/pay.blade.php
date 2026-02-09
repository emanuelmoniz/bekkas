<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Order {{ $order->order_number }} — Payment</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Easypay inline container (SDK only) --}}
        {{-- Show orchestration errors/messages only when no active manifest is present (avoid showing stale errors) --}}
        @if(empty($activeManifest))
            @if(! empty($payUnavailableMessage))
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-sm text-red-800">
                    {{ $payUnavailableMessage }}

                    {{-- hidden canonical generic message for tests/automation to assert against --}}
                    <span aria-hidden="true" style="display:none">{{ t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.' }}</span>
                </div>
            @elseif(isset($sessions) && ($err = $sessions->firstWhere('in_error', true)))
                {{-- Service recorded an errored session — show a friendly message and debug details when appropriate --}}
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-sm text-red-800">
                    {{ t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.' }}
                    @if(config('app.debug') && $err->message)
                        <div class="mt-2 text-xs text-red-700">{{ t('checkout.pay.unavailable_debug', ['error' => $err->message]) ?: $err->message }}</div>
                    @endif
                </div>
            @else
                {{-- Final fallback: directly check DB for errored session (defensive for tests/runtime) --}}
                @php $errDb = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->where('in_error', true)->latest('updated_at')->first(); @endphp
                @if($errDb)
                    <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-sm text-red-800">
                        {{ t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.' }}
                        @if(config('app.debug') && $errDb->message)
                            <div class="mt-2 text-xs text-red-700">{{ t('checkout.pay.unavailable_debug', ['error' => $errDb->message]) ?: $errDb->message }}</div>
                        @endif
                    </div>
                @endif
            @endif
        @endif

        @if(config('easypay.enabled') && env('EASYPAY_SDK_URL'))
            <div id="easypay-inline-root" class="bg-white shadow rounded p-4" aria-live="polite">

                <div id="easypay-flash" class="mb-3" aria-live="polite" style="display:none"></div>

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
                        const orderShowUrl = @json(url("/orders/{$order->uuid}"));

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
                                                }).then(r => r.json()).then(j => {
                                                    // Show a flash message above the widget instead of removing the SDK
                                                    try {
                                                        const flash = document.getElementById('easypay-flash');
                                                        if (flash) {
                                                            flash.style.display = 'block';
                                                            flash.className = 'mb-3 p-3 rounded bg-green-50 border border-green-100 text-sm text-green-800';
                                                            flash.innerText = @json(t('checkout.pay.success'));
                                                        }
                                                        mount.classList.remove('border', 'border-red-200');
                                                    } catch (e) { /* ignore DOM issues */ }
                                                }).catch(() => {/* ignore */});
                                            },
                                            onClose: function () {
                                                // Redirect user to order detail page when SDK signals close.
                                                try {
                                                    window.location.href = orderShowUrl;
                                                } catch (e) { /* ignore */ }
                                            },
                                            onError: function (error) {
                                                console.error('Easypay SDK onError', error);
                                                try {
                                                    fetch(@json(url('/easypay/sdk/error')), {
                                                        method: 'POST',
                                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                                                        body: JSON.stringify({ error: error })
                                                    }).catch(()=>{});
                                                } catch (e) { /* ignore */ }
                                                mount.classList.add('border', 'border-red-200');
                                                mount.innerText = @json(t('checkout.pay.widget_failed'));
                                            },
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
