<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Easypay checkout session</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <nav class="flex gap-2 text-sm" aria-label="Admin orders subnav">
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('admin.orders.payloads.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payloads*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Payloads</a>
                <a href="{{ route('admin.orders.checkouts.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/checkouts*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Checkouts</a>
            </nav>
        </div>

        <div class="bg-white shadow rounded p-4 space-y-4">
            <div class="flex justify-between items-start gap-4">
                <div>
                    <p><strong>Session ID:</strong> {{ $session->id }}</p>
                    <p><strong>Order:</strong>
                        @if($session->order)
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.orders.show', $session->order) }}">{{ $session->order->order_number }}</a>
                        @else
                            <span class="text-sm text-gray-500">No order</span>
                        @endif
                    </p>

                    <p><strong>Payload:</strong>
                        @if($session->payload)
                            <a class="text-indigo-600 hover:underline" href="{{ route('admin.orders.payloads.show', $session->payload) }}">Payload #{{ $session->payload->id }}</a>
                        @else
                            <span class="text-sm text-gray-500">—</span>
                        @endif
                    </p>

                    <p><strong>Created:</strong> {{ $session->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Status:</strong> {{ $session->status ?? '-' }}</p>
                    <p><strong>Checkout ID:</strong> {{ $session->checkout_id ?? '-' }}</p>
                    <p><strong>Session token:</strong> {{ $session->session_id ?? '-' }}</p>
                    <p><strong>Error code:</strong> {{ $session->error_code ?? '-' }}</p>
                </div>

                <div class="text-right">
                    @if($session->order)
                        <a href="{{ route('admin.orders.show', $session->order) }}" class="inline-block bg-gray-100 border px-4 py-2 rounded text-sm">View order</a>
                    @endif

                    @if($session->payload)
                        <a href="{{ route('admin.orders.payloads.show', $session->payload) }}" class="inline-block bg-indigo-50 border-indigo-200 text-indigo-700 border px-4 py-2 rounded text-sm ms-2">View payload</a>
                    @endif

                    <a href="{{ route('admin.orders.checkouts.index') }}" class="inline-block bg-white border px-4 py-2 rounded text-sm ms-2">Back</a>
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-2">Gateway response / message</h3>
                <pre class="whitespace-pre-wrap bg-gray-50 border rounded p-4 text-sm overflow-auto" style="max-height:48vh">{{ json_encode(json_decode($session->message ?: '{}'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
