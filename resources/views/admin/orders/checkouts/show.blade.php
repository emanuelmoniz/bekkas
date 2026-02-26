<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Easypay checkout session</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6 space-y-6">

            <div class="flex justify-between items-start gap-4">
                <dl class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Session ID</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $session->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Order</p>
                        <p class="text-sm text-grey-dark mt-1">
                            @if($session->order)
                                <a class="text-accent-secondary hover:underline" href="{{ route('admin.orders.show', $session->order) }}">{{ $session->order->order_number }}</a>
                            @else
                                <span class="text-grey-medium">No order</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Payload</p>
                        <p class="text-sm text-grey-dark mt-1">
                            @if($session->payload)
                                <a class="text-accent-primary hover:underline" href="{{ route('admin.orders.payloads.show', $session->payload) }}">Payload #{{ $session->payload->id }}</a>
                            @else
                                <span class="text-grey-medium">—</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Created</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Status</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $session->status ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Checkout ID</p>
                        <p class="text-sm text-grey-dark mt-1 font-mono break-all">{{ $session->checkout_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Session token</p>
                        @php
                            $token = $session->session_id ?? null;
                            $display = '-';
                            if (! empty($token)) {
                                $display = strlen($token) > 20
                                    ? substr($token, 0, 10)."…".substr($token, -10)
                                    : $token;
                            }
                        @endphp
                        <p class="text-sm text-grey-dark mt-1 font-mono" title="{{ $token ?? '' }}">{{ $display }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Error code</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $session->error_code ?? '-' }}</p>
                    </div>
                </dl>

                <div class="flex flex-col items-end gap-2">
                    <div class="w-full flex flex-wrap justify-end items-center gap-2 text-right">
                        @if($session->order)
                            <button type="button" onclick="window.location.href='{{ route('admin.orders.show', $session->order) }}'" class="inline-flex items-center bg-grey-light border px-2 py-2 rounded uppercase text-sm">View order</button>
                        @endif

                        @if($session->payload)
                            <button type="button" onclick="window.location.href='{{ route('admin.orders.payloads.show', $session->payload) }}'" class="inline-flex items-center bg-primary/10 border-accent-primary/30 text-accent-primary border px-2 py-2 rounded uppercase text-sm ms-2">View payload</button>
                        @endif

                        <button type="button" onclick="window.location.href='{{ route('admin.orders.payments.index', ['order_number' => optional($session->order)->order_number]) }}'" class="inline-flex items-center bg-white border px-2 py-2 rounded uppercase text-sm ms-2">View payments</button>

                        <form method="POST" action="{{ route('admin.orders.checkouts.refresh', $session) }}" class="inline-block ms-2">
                            @csrf
                            <button class="bg-primary/10 border-accent-secondary/20 text-accent-secondary border px-2 py-2 rounded uppercase text-sm" title="Refresh / fetch checkout details">Update</button>
                        </form>

                        <form method="POST" action="{{ route('admin.orders.checkouts.cancel', $session) }}" class="inline-block ms-2" onsubmit="return confirm('Cancel this checkout at Easypay?');">
                            @csrf
                            <button class="bg-status-error/10 border-status-error text-status-error border px-2 py-2 rounded uppercase text-sm" title="Cancel checkout">Cancel</button>
                        </form>

                        <button type="button" onclick="window.location.href='{{ route('admin.orders.checkouts.index') }}'" class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light transition ease-in-out duration-150 ms-2">Back</button>
                    </div>
                </div>

            </div>

            <div>
                <h3 class="text-xs text-grey-dark uppercase mb-2">Gateway response / message</h3>
                <pre class="whitespace-pre-wrap bg-white border rounded p-4 text-sm overflow-auto" style="max-height:48vh;overflow-wrap:anywhere;word-break:break-word;">{{ json_encode(json_decode($session->message ?: '{}'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
