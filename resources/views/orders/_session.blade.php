<div class="border rounded p-3">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm text-gray-600">Created: {{ $s->created_at?->format('d/m/Y H:i:s') }}</p>
            <p class="text-sm">Status: <strong>{{ $s->status ?? ($s->in_error ? 'error' : 'unknown') }}</strong></p>
            <p class="text-sm">Checkout ID: <code>{{ $s->checkout_id }}</code></p>

            @if(! empty($s->checkout_id))
                <p class="mt-2">
                    <button class="get-checkout-info inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-800 px-2 py-1 rounded text-sm" data-url="{{ route('orders.pay.checkout_info', ['order' => $order->uuid, 'session' => $s->id]) }}">
                        <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                        Get checkout info
                    </button>
                </p>
                <div class="mt-3 bg-white border rounded p-3 hidden checkout-info-panel">
                    <p class="text-sm text-gray-600">Response:</p>
                    <pre class="mt-2 text-sm checkout-info-pre" style="max-height:260px;overflow:auto"></pre>
                </div>
            @endif
        </div>
        <div class="text-right">
            <p class="text-sm">In error: {{ $s->in_error ? 'yes' : 'no' }}</p>
            <p class="text-sm">Error code: {{ $s->error_code ?? '-' }}</p>
            <p class="text-sm">Active: {{ $s->is_active ? 'yes' : 'no' }}</p>
        </div>
    </div>

    <details class="mt-3 bg-gray-50 p-3 rounded">
        <summary class="cursor-pointer">Raw response / message</summary>
        <pre class="mt-2 text-sm">{{ $s->message }}</pre>
    </details>

</div>
