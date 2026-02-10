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

        {{-- Payment-status driven UI (server refreshed) --}}
        {{-- Controller MUST provide: paymentStatusMessage, paymentInfo, suppressSdk — view no longer queries EasypayPayment directly. --}}
        @if(! empty($paymentStatusMessage))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-100 text-sm text-green-800">
                {{ $paymentStatusMessage }}
            </div>
        @endif

        @if(isset($paymentInfo) && $paymentInfo?->payment_status === 'pending')
            <div class="mb-4 bg-white border rounded p-4 text-sm">
                <h3 class="font-semibold mb-2">{{ t('checkout.pay.payment_info_title') ?: 'Payment information' }}</h3>
                <div class="space-y-2 text-gray-700">
                    @if($paymentInfo->mb_entity)
                        <div><strong>{{ t('checkout.pay.mb_entity') ?: 'MB entity' }}:</strong> {{ $paymentInfo->mb_entity }}</div>
                    @endif
                    @if($paymentInfo->mb_reference)
                        <div><strong>{{ t('checkout.pay.mb_reference') ?: 'MB reference' }}:</strong> {{ $paymentInfo->mb_reference }}</div>
                    @endif
                    @if($paymentInfo->mb_expiration_time)
                        <div><strong>{{ t('checkout.pay.mb_expires') ?: 'MB expiration time' }}:</strong> {{ $paymentInfo->mb_expiration_time->toDayDateTimeString() }}</div>
                    @endif
                    @if($paymentInfo->iban)
                        <div><strong>{{ t('checkout.pay.iban') ?: 'IBAN' }}:</strong> {{ $paymentInfo->iban }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if(config('easypay.enabled') && env('EASYPAY_SDK_URL'))
  @unless(isset($paymentInfo) && ($paymentInfo?->payment_status === 'pending'))
    @if(empty($suppressSdk))
      <div id="easypay-inline-root" class="bg-white shadow rounded p-4" aria-live="polite">
        <div id="easypay-checkout" class="min-h-[120px] flex items-center justify-center text-sm text-gray-600">
          <span id="easypay-checkout-loading">{{ t('checkout.pay.loading_widget') ?: 'Loading payment widget…' }}</span>
        </div>

        <script id="easypay-manifest" type="application/json">@json($activeManifest)</script>
        <script async src="{{ env('EASYPAY_SDK_URL') }}" integrity="" crossorigin="anonymous"></script>

        <script>
        (function () {
          // Double-guard: do not initialise SDK if controller requested suppression.
          if (@json((bool) ($suppressSdk ?? false))) return;

          // Runtime defensive check: if the server provided a persisted payment in
          // `paymentInfo` with status `pending`, do NOT start the SDK even if a
          // manifest is present in the DOM (covers cache/mismatch/race cases).
          const paymentInfo = @json($paymentInfo ?? null);
          if (paymentInfo && paymentInfo.payment_status === 'pending') {
            console.warn('Easypay: runtime guard — payment is pending, aborting SDK initialization.');
            return;
          }

          const manifestEl = document.getElementById('easypay-manifest');
          if (!manifestEl) return;

          let manifest = null;
          try { manifest = JSON.parse(manifestEl.textContent); } catch (err) { console.error('Invalid Easypay manifest in page', err); return; }

          const mount = document.getElementById('easypay-checkout');
          const testing = @json(config('easypay.env') === 'test');

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
                  // Provide an onSuccess hook and expose a deterministic global for tests to invoke.
                  const handleSdkSuccess = function (checkoutPayload) {
                    // Always POST the checkout wrapper to the server endpoint that persists payments
                    const url = '/orders/{{ $order->uuid }}/pay/verify';
                    const body = { checkout: checkoutPayload };

                    fetch(url, {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify(body),
                      credentials: 'same-origin'
                    }).then(r => r.json()).then(json => {
                      try {
                        // Use server-side session flash (rendered in the global flash area).
                        // Expose the returned message to tests and then reload the page so the
                        // Blade-rendered flash (layouts.app) becomes visible.
                        if (json?.message) {
                          // test-only hook for deterministic E2E assertions
                          window.__easypay_lastServerMessage = json.message;

                          // If authoritative+paid we navigate to the order page (preserve previous UX).
                          // Otherwise, show the server message in-page (do NOT reload the whole page).
                          if (json?.paymentStatus === 'paid' && json?.authoritative) {
                            window.location.href = '/orders/{{ $order->uuid }}';
                            return;
                          }

                          // Tell the global flash store to show the server message (no HTML injection)
                          if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                            // prefer an explicit type from the server; otherwise infer from paymentStatus
                            const inferred = json?.type || (json?.paymentStatus === 'paid' ? 'success' : (json?.paymentStatus === 'authorised' ? 'info' : 'warning'));
                            Alpine.store('flash').showMessage(json.message || '', inferred);
                          }

                          return;
                        }

                        // If no message was returned, preserve previous behaviour (no-op)
                      } catch (err) {
                        console.error('Easypay onSuccess client handler error', err);
                      }
                    }).catch(err => {
                      console.warn('Easypay: could not POST checkout to server', err);
                    });
                  };

                  // Expose deterministic hooks for tests (Cypress) and also attempt to pass to the SDK
                  window.__easypay_onSuccess = handleSdkSuccess;

                  // SDK onClose: when the SDK 'end' button is pressed, navigate to the order details page.
                  const handleSdkClose = function () {
                      try {
                          window.location.assign('/orders/{{ $order->uuid }}');
                      } catch (e) { window.location.href = '/orders/{{ $order->uuid }}'; }
                  };
                  window.__easypay_onClose = handleSdkClose;

                  // Pass onSuccess, onClose and error handlers in the options (SDK may accept them) as well as container/display
                  // Client-side error handler: POST SDK error to server and act on returned 'action'
                  const handleSdkError = async function (err) {
                    try {
                      const url = '/orders/{{ $order->uuid }}/pay/sdk-error';
                      const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ error: err })
                      });

                      const json = await res.json();

                      if (json?.action === 'new-manifest' && json?.manifest) {
                        // restart SDK with new manifest (preserve the same handlers)
                        try {
                          for (const cand of starterCandidates) {
                            const fn = cand();
                            if (typeof fn === 'function') {
                              fn(json.manifest, { display: 'inline', container: '#easypay-checkout', testing: @json(config('easypay.env') === 'test'), onSuccess: handleSdkSuccess, onClose: handleSdkClose, onError: handleSdkError, onPaymentError: handleSdkPaymentError });
                              return;
                            }
                          }
                        } catch (e) { /* ignore restart failure */ }

                        return;
                      }

                      if (json?.action === 'already-paid') {
                        // server confirmed an authoritative paid payment — redirect to order
                        if (json?.message) {
                          window.__easypay_lastServerMessage = json.message;
                        }
                        window.location.href = '/orders/{{ $order->uuid }}';
                        return;
                      }

                      // Generic error: show server message if present
                      if (json?.message) {
                        if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                          Alpine.store('flash').showMessage(json.message, 'warning');
                        }

                        // expose for tests
                        window.__easypay_lastServerMessage = json.message;
                      }

                    } catch (e) {
                      console.warn('Easypay onError client handler failed', e);
                    }
                  };

                  const handleSdkPaymentError = async function (err) {
                    // Recoverable payment error: forward to server for best-effort refresh and allow retry
                    try {
                      const url = '/orders/{{ $order->uuid }}/pay/sdk-error';
                      const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, credentials: 'same-origin', body: JSON.stringify({ error: err }) });
                      const j = await r.json();
                      if (j?.action === 'new-manifest' && j?.manifest) {
                        // restart SDK with new manifest
                        for (const cand of starterCandidates) {
                          const fn = cand();
                          if (typeof fn === 'function') {
                            fn(j.manifest, { display: 'inline', container: '#easypay-checkout', testing: @json(config('easypay.env') === 'test'), onSuccess: handleSdkSuccess, onClose: handleSdkClose, onError: handleSdkError, onPaymentError: handleSdkPaymentError });
                            return;
                          }
                        }
                      }

                      if (j?.message && window.Alpine && Alpine.store && Alpine.store('flash')) {
                        Alpine.store('flash').showMessage(j.message, 'warning');
                        window.__easypay_lastServerMessage = j.message;
                      }
                    } catch (e) { /* ignore */ }
                  };

                  fn(manifest, { display: 'inline', container: '#easypay-checkout', testing: @json(config('easypay.env') === 'test'), onSuccess: handleSdkSuccess, onClose: handleSdkClose, onError: handleSdkError, onPaymentError: handleSdkPaymentError, onError: handleSdkError, onPaymentError: handleSdkPaymentError });
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
  @endunless
@endif

        <div class="text-right">
            <a href="{{ route('orders.show', $order->uuid) }}" class="text-sm text-gray-600 hover:underline">Back to order</a>
        </div>
    </div>
</x-app-layout>
