<div class="border rounded p-3">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm text-gray-600">Created: {{ $s->created_at?->format('d/m/Y H:i:s') }}</p>
            <p class="text-sm">Status: <strong>{{ $s->status ?? ($s->in_error ? 'error' : 'unknown') }}</strong></p>
            <p class="text-sm">Checkout ID: <code>{{ $s->checkout_id }}</code></p>
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
