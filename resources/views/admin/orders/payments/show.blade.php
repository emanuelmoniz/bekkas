<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Easypay payment</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <nav class="flex gap-2 text-sm" aria-label="Admin orders subnav">
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('admin.orders.payloads.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payloads*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Payloads</a>
                <a href="{{ route('admin.orders.checkouts.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/checkouts*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Checkouts</a>
                <a href="{{ route('admin.orders.payments.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payments*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Payments</a>
            </nav>
        </div>

        <div class="bg-white shadow rounded p-4 space-y-4">
            <div class="flex justify-end items-center gap-2 mb-2">
                @if($payment->order)
                    <a href="{{ route('admin.orders.show', $payment->order) }}" class="inline-flex items-center bg-gray-100 border px-4 py-2 rounded text-sm">View order</a>
                @endif

                @if($payment->checkoutSession)
                    <a href="{{ route('admin.orders.checkouts.show', $payment->checkoutSession) }}" class="inline-flex items-center bg-indigo-50 border-indigo-200 text-indigo-700 border px-4 py-2 rounded text-sm">View checkout</a>
                @endif

                <a href="{{ route('admin.orders.payments.index') }}" class="inline-flex items-center bg-white border px-4 py-2 rounded text-sm">Back</a>
            </div>

            <div class="flex justify-between items-start gap-4">
                <div>
                    <p><strong>Payment ID:</strong> {{ $payment->payment_id ?? $payment->id }}</p>
                    <p><strong>Order:</strong>
                        @if($payment->order)
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.orders.show', $payment->order) }}">{{ $payment->order->order_number }}</a>
                        @else
                            <span class="text-sm text-gray-500">No order</span>
                        @endif
                    </p>

                    <p><strong>Checkout session:</strong>
                        @if($payment->checkoutSession)
                            <a class="text-indigo-600 hover:underline" href="{{ route('admin.orders.checkouts.show', $payment->checkoutSession) }}">Session #{{ $payment->checkoutSession->id }}</a>
                        @else
                            <span class="text-sm text-gray-500">—</span>
                        @endif
                    </p>

                    <p><strong>Created:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Paid at:</strong> {{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    <p><strong>Status:</strong> {{ $payment->payment_status ?? '-' }}</p>
                    <p><strong>Method:</strong> {{ $payment->payment_method ?? '-' }}</p>

                    <p><strong>Card last digits:</strong> {{ $payment->card_last_digits ?? '-' }}</p>
                    <p><strong>MB entity / reference:</strong> {{ $payment->mb_entity ?? '-' }} / {{ $payment->mb_reference ?? '-' }}</p>
                    <p><strong>IBAN:</strong> {{ $payment->iban ?? '-' }}</p>
                </div>

            </div>

            <div>
                <h3 class="font-semibold mb-2">Gateway raw response</h3>
                <pre class="whitespace-pre-wrap bg-gray-50 border rounded p-4 text-sm overflow-auto" style="max-height:48vh;overflow-wrap:anywhere;word-break:break-word;">{{ json_encode($payment->raw_response ?: [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
