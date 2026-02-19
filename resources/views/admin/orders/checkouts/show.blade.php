<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Easypay checkout session</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <nav class="flex gap-2 text-sm" aria-label="Admin orders subnav">
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders') ? 'bg-grey-light' : 'hover:bg-light' }}">Orders</a>
                <a href="{{ route('admin.orders.payloads.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payloads*') ? 'bg-grey-light' : 'hover:bg-light' }}">Payloads</a>
                <a href="{{ route('admin.orders.checkouts.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/checkouts*') ? 'bg-grey-light' : 'hover:bg-light' }}">Checkouts</a>
                <a href="{{ route('admin.orders.payments.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payments*') ? 'bg-grey-light' : 'hover:bg-light' }}">Payments</a>
            </nav>
        </div>

        <div class="bg-light shadow rounded p-4 space-y-4">


            <div class="flex justify-between items-start gap-4">
                <div>
                    <p><strong>Session ID:</strong> {{ $session->id }}</p>
                    <p><strong>Order:</strong>
                        @if($session->order)
                            <a class="text-accent-secondary hover:underline" href="{{ route('admin.orders.show', $session->order) }}">{{ $session->order->order_number }}</a>
                        @else
                            <span class="text-sm text-grey-medium">No order</span>
                        @endif
                    </p>

                    <p><strong>Payload:</strong>
                        @if($session->payload)
                            <a class="text-accent-primary hover:underline" href="{{ route('admin.orders.payloads.show', $session->payload) }}">Payload #{{ $session->payload->id }}</a>
                        @else
                            <span class="text-sm text-grey-medium">—</span>
                        @endif
                    </p>

                    <p><strong>Created:</strong> {{ $session->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Status:</strong> {{ $session->status ?? '-' }}</p>
                    <p><strong>Checkout ID:</strong>
                        <span class="block mt-1 text-sm font-mono" style="overflow-wrap:anywhere;word-break:break-word;max-width:100%;">{{ $session->checkout_id ?? '-' }}</span>
                    </p>
                    <p><strong>Session token:</strong>
                        @php
                            $token = $session->session_id ?? null;
                            $display = '-';
                            if (! empty($token)) {
                                $display = strlen($token) > 20
                                    ? substr($token, 0, 10)."…".substr($token, -10)
                                    : $token;
                            }
                        @endphp
                        <span title="{{ $token ?? '' }}" class="block mt-1 text-sm font-mono" style="overflow-wrap:anywhere;word-break:break-word;max-width:100%;">{{ $display }}</span>
                    </p>
                    <p><strong>Error code:</strong> {{ $session->error_code ?? '-' }}</p>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <div class="w-full flex flex-wrap justify-end items-center gap-2 text-right">
                        @if($session->order)
                            <a href="{{ route('admin.orders.show', $session->order) }}" class="inline-flex items-center bg-grey-light border px-4 py-2 rounded text-sm">View order</a>
                        @endif

                        @if($session->payload)
                            <a href="{{ route('admin.orders.payloads.show', $session->payload) }}" class="inline-flex items-center bg-accent-primary/10 border-accent-primary/30 text-accent-primary border px-4 py-2 rounded text-sm ms-2">View payload</a>
                        @endif

                        <a href="{{ route('admin.orders.payments.index', ['order_number' => optional($session->order)->order_number]) }}" class="inline-flex items-center bg-light border px-4 py-2 rounded text-sm ms-2">View payments</a>

                        <form method="POST" action="{{ route('admin.orders.checkouts.refresh', $session) }}" class="inline-block ms-2">
                            @csrf
                            <button class="bg-accent-secondary/10 border-accent-secondary/20 text-accent-secondary border px-4 py-2 rounded text-sm" title="Refresh / fetch checkout details">Update</button>
                        </form>

                        <form method="POST" action="{{ route('admin.orders.checkouts.cancel', $session) }}" class="inline-block ms-2" onsubmit="return confirm('Cancel this checkout at Easypay?');">
                            @csrf
                            <button class="bg-status-error/10 border-status-error text-status-error border px-4 py-2 rounded text-sm" title="Cancel checkout">Cancel</button>
                        </form>

                        <a href="{{ route('admin.orders.checkouts.index') }}" class="inline-flex items-center bg-light border px-4 py-2 rounded text-sm ms-2">Back</a>
                    </div>
                </div>

            </div>

            <div>
                <h3 class="font-semibold mb-2">Gateway response / message</h3>
                <pre class="whitespace-pre-wrap bg-light border rounded p-4 text-sm overflow-auto" style="max-height:48vh;overflow-wrap:anywhere;word-break:break-word;">{{ json_encode(json_decode($session->message ?: '{}'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
