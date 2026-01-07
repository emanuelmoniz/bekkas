<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Order {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- STATUS --}}
        <div class="bg-white shadow rounded p-4">
            <p><strong>{{ t('orders.date') ?: 'Date' }}:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>{{ t('orders.status') ?: 'Status' }}:</strong> {{ $order->status }}</p>
            <p><strong>{{ t('orders.paid') ?: 'Paid' }}:</strong> {{ $order->is_paid ? 'Yes' : 'No' }}</p>
            <p><strong>{{ t('orders.canceled') ?: 'Canceled' }}:</strong> {{ $order->is_canceled ? 'Yes' : 'No' }}</p>
            <p><strong>{{ t('orders.refunded') ?: 'Refunded' }}:</strong> {{ $order->is_refunded ? 'Yes' : 'No' }}</p>
            @if ($order->tracking_number)
                <p><strong>{{ t('orders.tracking') ?: 'Tracking' }}:</strong> {{ $order->tracking_number }}</p>
            @endif
        </div>

        {{-- ADDRESS --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">{{ t('orders.shipping_address') ?: 'Shipping Address' }}</h3>
            <p>{{ $order->address_title }}</p>
            <p>{{ $order->address_line_1 }}</p>
            @if($order->address_line_2)
                <p>{{ $order->address_line_2 }}</p>
            @endif
            <p>{{ $order->address_postal_code }} {{ $order->address_city }}</p>
            <p>{{ $order->address_country }}</p>
            <p><strong>{{ t('orders.nif') ?: 'NIF' }}:</strong> {{ $order->address_nif }}</p>
        </div>

        {{-- PRODUCTS --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">{{ t('orders.products') ?: 'Products' }}</h3>
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">{{ t('orders.product') ?: 'Product' }}</th>
                        <th class="px-3 py-2">{{ t('orders.qty') ?: 'Qty' }}</th>
                        <th class="px-3 py-2">{{ t('orders.gross') ?: 'Gross' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                {{ optional($item->product->translation())->name }}
                            </td>
                            <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right">
                                {{ number_format($item->total_gross, 2) }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TOTALS --}}
        <div class="bg-white shadow rounded p-4 text-right space-y-1">
            <p>{{ t('orders.products_net') ?: 'Products (net)' }}: {{ number_format($order->products_total_net, 2) }} €</p>
            <p>{{ t('orders.products_tax') ?: 'Products tax' }}: {{ number_format($order->products_total_tax, 2) }} €</p>
            <p>{{ t('orders.shipping') ?: 'Shipping' }}: {{ number_format($order->shipping_gross, 2) }} €</p>
            <p>{{ t('orders.shipping_tax') ?: 'Shipping tax' }}: {{ number_format($order->shipping_tax, 2) }} €</p>
            <hr>
            <p class="font-semibold">
                {{ t('orders.total') ?: 'Total' }}: {{ number_format($order->total_gross, 2) }} €
            </p>
        </div>

    </div>
</x-app-layout>
