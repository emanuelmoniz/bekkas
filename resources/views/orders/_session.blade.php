<div class="border rounded p-3" @if($s->is_active && ($s->status ?? '') === 'pending' && $s->manifest) data-manifest='@json($s->manifest)' @endif>
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm text-grey-dark">Created: {{ $s->created_at?->format('d/m/Y H:i:s') }}</p>
            <p class="text-sm">Status: <strong>{{ $s->status ?? ($s->in_error ? 'error' : 'unknown') }}</strong></p>
            <p class="text-sm">Checkout ID: <span class="inline-block align-baseline font-mono" style="overflow-wrap:anywhere;word-break:break-word;max-width:100%;"><code>{{ $s->checkout_id }}</code></span></p>


        </div>
        <div class="text-right">
            <p class="text-sm">In error: {{ $s->in_error ? 'yes' : 'no' }}</p>
            <p class="text-sm">Error code: {{ $s->error_code ?? '-' }}</p>
            <p class="text-sm">Active: {{ $s->is_active ? 'yes' : 'no' }}</p>
        </div>
    </div>

    <details class="mt-3 bg-light p-3 rounded">
        <summary class="cursor-pointer">Raw response / message</summary>
        <pre class="mt-2 text-sm">{{ $s->message }}</pre>
    </details>

</div>
