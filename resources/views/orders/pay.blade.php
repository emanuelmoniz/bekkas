<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Order {{ $order->order_number }} — Payment</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Easypay inline container: placed BEFORE payload + sessions as requested --}}
        @if(config('easypay.enabled') && env('EASYPAY_SDK_URL'))
            <div id="easypay-inline-root" class="bg-white shadow rounded p-4" aria-live="polite">
                <h3 class="font-semibold mb-2">Payment widget</h3>

                <div id="easypay-checkout" class="min-h-[120px] flex items-center justify-center text-sm text-gray-600">
                    <span id="easypay-checkout-loading">{{ t('checkout.pay.loading_widget') ?: 'Loading payment widget…' }}</span>
                </div>

                {{-- persisted manifest for the latest active/pending session (if present) --}}
                @if(! empty($activeManifest))
                    <script id="easypay-manifest" type="application/json">@json($activeManifest)</script>
                @endif

                {{-- load SDK (async) and initialise when manifest present; JS below will no-op safely if SDK not available --}}
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

                        function startWithGlobal(manifest) {
                            // try known global entrypoints (SDK exposes window.easypayCheckout.startCheckout)
                            const candidates = [
                                ['window.startCheckout', () => window.startCheckout],
                                ['window.Easypay.startCheckout', () => window.Easypay && window.Easypay.startCheckout],
                                ['window.Easypay.checkout.startCheckout', () => window.Easypay && window.Easypay.checkout && window.Easypay.checkout.startCheckout],
                                ['window.easypayCheckout.startCheckout', () => window.easypayCheckout && window.easypayCheckout.startCheckout],
                                ['window.easypayCheckout.start', () => window.easypayCheckout && window.easypayCheckout.start]
                            ];

                            let starter = null;
                            let used = null;
                            for (const [name, fn] of candidates) {
                                try { const f = fn(); if (typeof f === 'function') { starter = f; used = name; break; } } catch (err) { /* continue */ }
                            }

                            if (!starter) return false;

                            try {
                                starter(manifest, {
                                    display: 'inline',
                                    testing: testing,
                                    container: '#easypay-checkout',
                                    showLoading: true,
                                    onSuccess: function (checkoutInfo) {
                                        console.info('Easypay SDK onSuccess', checkoutInfo);

                                        // try to notify server (best-effort) — endpoint may be handled server-side by existing verify logic
                                        fetch(orderVerifyUrl, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                            },
                                            body: JSON.stringify({ checkout: checkoutInfo })
                                        }).then(r => r.json()).then(j => {
                                            console.info('verify response', j);
                                            if (j?.ok) window.location.reload();
                                        }).catch(err => {
                                            console.warn('verify request failed', err);
                                        });
                                    },
                                    onError: function (err) {
                                        console.error('Easypay SDK error', err);
                                        mount.classList.add('border', 'border-red-200');
                                        mount.innerText = 'Payment widget failed to load — please try again.';
                                    },
                                    onPaymentError: function (err) {
                                        console.error('Easypay payment error', err);
                                        alert('Payment failed — please try another method or contact support.');
                                    },
                                    onClose: function () {
                                        console.info('Easypay widget closed');
                                    }
                                });

                                // remove our static loading placeholder if present (SDK will manage its own loading UI when showLoading=true)
                                try {
                                    const loader = document.getElementById('easypay-checkout-loading');
                                    if (loader && loader.parentNode) loader.parentNode.removeChild(loader);

                                    // fallback: if SDK appends an iframe, hide loader when iframe loads
                                    const mo = new MutationObserver((records, obs) => {
                                        const iframe = mount.querySelector('iframe');
                                        if (!iframe) return;
                                        iframe.addEventListener('load', () => {
                                            const l = document.getElementById('easypay-checkout-loading');
                                            if (l && l.parentNode) l.parentNode.removeChild(l);
                                        });
                                        obs.disconnect();
                                    });
                                    mo.observe(mount, { childList: true, subtree: true });
                                } catch (err) {
                                    console.warn('[easypay] could not remove loader element', err);
                                }

                                return true;
                            } catch (err) {
                                console.error('Failed to call Easypay startCheckout', err);
                                return false;
                            }
                        }

                        // If SDK already available call immediately, otherwise wait for global to appear
                        if (!startWithGlobal(manifest)) {
                            const maxWait = 10000; // give additional time for async script execution
                            const start = Date.now();
                            const i = setInterval(function () {
                                if (startWithGlobal(manifest)) {
                                    clearInterval(i);
                                    return;
                                }

                                if (Date.now() - start > maxWait) {
                                    clearInterval(i);

                                    // diagnostic: list easypay-related globals so we can debug naming mismatches
                                    const easypayGlobals = Object.keys(window).filter(k => /easypay/i.test(k));
                                    console.warn('[easypay] starter not found after wait; detected globals:', easypayGlobals);

                                    mount.innerHTML = '';
                                    const errP = document.createElement('div');
                                    errP.className = 'text-sm text-red-600';
                                    errP.innerText = 'Could not load payment widget — SDK did not expose the expected API.';
                                    const detail = document.createElement('pre');
                                    detail.className = 'mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded';
                                    detail.innerText = 'Detected globals: ' + (easypayGlobals.length ? easypayGlobals.join(', ') : '(none)') + '\nCheck Console for more details.';
                                    mount.appendChild(errP);
                                    mount.appendChild(detail);
                                }
                            }, 150);
                        }
                    })();
                </script>
            </div>
        @endif

        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Easypay payload</h3>

            @if($payload)
                <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm" style="max-height:420px">{{ json_encode($payload->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p class="text-gray-600">No payload found for this order.</p>
            @endif
        </div>

        <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold mb-2">Checkout sessions</h3>
                <div>
                    <button id="create-session-btn" data-url="{{ route('orders.pay.session', $order->uuid) }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
                        <svg id="create-session-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        <span id="create-session-label">Create checkout session</span>
                    </button>
                </div>
            </div>

            <div id="easypay-sessions" class="mt-4 space-y-4">
                @if($sessions->isEmpty())
                    <p class="text-gray-600">No checkout sessions created yet.</p>
                @else
                    @foreach($sessions as $s)
                        @include('orders._session', ['s' => $s, 'order' => $order])
                    @endforeach
                @endif
            </div>
        </div>

        <script>
            (function(){
                const btn = document.getElementById('create-session-btn');
                if (!btn) return;
                const spinner = document.getElementById('create-session-spinner');
                const label = document.getElementById('create-session-label');
                const container = document.getElementById('easypay-sessions');

                btn.addEventListener('click', async function () {
                    const url = btn.dataset.url;
                    btn.disabled = true;
                    spinner.classList.remove('hidden');
                    label.textContent = 'Creating...';

                    try {
                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({})
                        });

                        const json = await resp.json();
                        if (resp.status === 201 && json.ok) {
                            // Insert returned HTML at the top
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = json.html;
                            const newEl = wrapper.firstElementChild;
                            container.prepend(newEl);

                            // If the new session contains an embedded manifest, surface it to the inline widget
                            try {
                                const manifestData = newEl?.dataset?.manifest;
                                if (manifestData) {
                                    // ensure manifest script exists/updated
                                    let manifestEl = document.getElementById('easypay-manifest');
                                    if (!manifestEl) {
                                        manifestEl = document.createElement('script');
                                        manifestEl.id = 'easypay-manifest';
                                        manifestEl.type = 'application/json';
                                        document.getElementById('easypay-inline-root')?.appendChild(manifestEl);
                                    }
                                    manifestEl.textContent = manifestData;

                                    // attempt to start the SDK (best-effort)
                                    const starter = window.startCheckout || (window.Easypay && window.Easypay.startCheckout);
                                    if (starter) {
                                        try { starter(JSON.parse(manifestData), { display: 'inline', testing: {{ json_encode(config('easypay.env') === 'test') }}, container: '#easypay-checkout' }); } catch (err) { console.warn('Could not auto-start Easypay after session create', err); }
                                    }
                                }
                            } catch (err) {
                                console.warn('Could not attach manifest from created session', err);
                            }
                        } else {
                            alert(json.message || 'Failed to create checkout session');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Request failed — check console');
                    } finally {
                        btn.disabled = false;
                        spinner.classList.add('hidden');
                        label.textContent = 'Create checkout session';
                    }
                });

                // Delegated handler for "Get checkout info" buttons (works for existing + dynamic sessions)
                container.addEventListener('click', async function (ev) {
                    const btn = ev.target.closest('.get-checkout-info');
                    if (!btn) return;

                    ev.preventDefault();
                    const url = btn.dataset.url;
                    const sessionEl = btn.closest('div.border');
                    const panel = sessionEl?.querySelector('.checkout-info-panel');
                    const pre = sessionEl?.querySelector('.checkout-info-pre');

                    if (!panel || !pre) {
                        console.warn('Checkout info UI missing for session');
                        return;
                    }

                    btn.disabled = true;
                    const origHtml = btn.innerHTML;
                    btn.innerHTML = 'Loading...';

                    try {
                        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const json = await resp.json();

                        panel.classList.remove('hidden');
                        if (json.ok) {
                            pre.textContent = JSON.stringify(json.body, null, 2);
                        } else {
                            pre.textContent = JSON.stringify(json, null, 2);
                        }
                    } catch (err) {
                        panel.classList.remove('hidden');
                        pre.textContent = String(err);
                        console.error(err);
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                    }
                });
            })();
        </script>

        <div class="text-right">
            <a href="{{ route('orders.show', $order->uuid) }}" class="text-sm text-gray-600 hover:underline">Back to order</a>
        </div>
    </div>
</x-app-layout>
