<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- STATUS --}}
        <div class="bg-white shadow rounded p-4">
            <p><strong>Status:</strong> {{ $order->status }}</p>
            <p><strong>Paid:</strong> {{ $order->is_paid ? 'Yes' : 'No' }}</p>
            <p><strong>Canceled:</strong> {{ $order->is_canceled ? 'Yes' : 'No' }}</p>
            <p><strong>Refunded:</strong> {{ $order->is_refunded ? 'Yes' : 'No' }}</p>
            @if ($order->tracking_number)
                <p><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
            @endif
        </div>

        {{-- ADDRESS --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Shipping Address</h3>
            <p>{{ $order->address->title }}</p>
            <p>{{ $order->address->address_line_1 }}</p>
            <p>{{ $order->address->address_line_2 }}</p>
            <p>{{ $order->address->postal_code }} {{ $order->address->city }}</p>
            <p>{{ $order->address->country }}</p>
            <p><strong>NIF:</strong> {{ $order->address->nif }}</p>
        </div>

        {{-- PRODUCTS --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Products</h3>
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2">Qty</th>
                        <th class="px-3 py-2">Gross</th>
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
            <p>Products (net): {{ number_format($order->products_total_net, 2) }} €</p>
            <p>Products tax: {{ number_format($order->products_total_tax, 2) }} €</p>
            <p>Shipping: {{ number_format($order->shipping_gross, 2) }} €</p>
            <p>Shipping tax: {{ number_format($order->shipping_tax, 2) }} €</p>
            <hr>
            <p class="font-semibold">
                Total: {{ number_format($order->total_gross, 2) }} €
            </p>
        </div>

    </div>
</x-app-layout>
