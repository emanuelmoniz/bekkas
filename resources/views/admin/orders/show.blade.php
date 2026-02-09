<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Order {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="mb-4">
            <nav class="flex gap-2 text-sm" aria-label="Admin orders subnav">
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('admin.orders.payloads.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payloads*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Payloads</a>
            </nav>
        </div>

        {{-- =======================
             ORDER STATUS (READ)
        ======================= --}}
        <div class="bg-white shadow rounded p-4 grid md:grid-cols-2 gap-4">
            <div>
                <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>{{ t('orders.status') ?: 'Status' }}:</strong> 
                    @php
                        $currentStatus = $statuses->firstWhere('code', $order->status);
                    @endphp
                    {{ optional($currentStatus?->translation())->name ?? $order->status }}
                </p>
                <p><strong>{{ t('orders.paid') ?: 'Paid' }}:</strong> {{ $order->is_paid ? 'Yes' : 'No' }}</p>
                <p><strong>{{ t('orders.refunded') ?: 'Refunded' }}:</strong> {{ $order->is_refunded ? 'Yes' : 'No' }}</p>
                @if($order->shipping_tier_name)
                    <p><strong>Shipping Tier:</strong> {{ $order->shipping_tier_name }}</p>
                @endif
                @if($order->expected_delivery_date)
                    <p><strong>Expected Delivery:</strong> {{ $order->expected_delivery_date->format('d/m/Y') }}</p>
                @endif
            </div>

            <div>
                <p><strong>User:</strong> {{ $order->user->name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                <p><strong>NIF:</strong> {{ $order->address_nif }}</p>
                @if ($order->tracking_number)
                    <p><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                @endif
                @if($order->easypayPayload)
                    <div class="mt-3">
                        <a href="{{ route('admin.orders.payloads.show', $order->easypayPayload) }}" class="inline-block bg-white border px-4 py-2 rounded text-sm">View payload</a>

                        <a href="{{ route('admin.orders.checkouts.index', ['order_number' => $order->order_number]) }}" class="inline-block bg-white border px-4 py-2 rounded text-sm ms-2">View checkout sessions</a>

                        <a href="{{ route('admin.orders.payments.index', ['order_number' => $order->order_number]) }}" class="inline-block bg-white border px-4 py-2 rounded text-sm ms-2">View payments</a>
                    </div>
                @else
                    <div class="mt-3">
                        <form method="POST" action="{{ route('admin.orders.payloads.store', $order) }}" onsubmit="return confirm('Create Easypay payload for this order?');" class="inline-block">
                            @csrf
                            <button class="bg-green-50 border-green-200 text-green-700 border px-4 py-2 rounded text-sm">Create payload</button>
                        </form>

                        <a href="{{ route('admin.orders.checkouts.index', ['order_number' => $order->order_number]) }}" class="inline-block bg-indigo-50 border-indigo-200 text-indigo-700 border px-4 py-2 rounded text-sm ms-2">View checkout sessions</a>

                        <a href="{{ route('admin.orders.payments.index', ['order_number' => $order->order_number]) }}" class="inline-block bg-indigo-50 border-indigo-200 text-indigo-700 border px-4 py-2 rounded text-sm ms-2">View payments</a>
                    </div>
                @endif            </div>
        </div>

        {{-- =======================
             SHIPPING ADDRESS
        ======================= --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Shipping Address</h3>

            <p>{{ $order->address_title }}</p>
            <p>{{ $order->address_line_1 }}</p>
            @if($order->address_line_2)
                <p>{{ $order->address_line_2 }}</p>
            @endif
            <p>{{ $order->address_postal_code }} {{ $order->address_city }}</p>
            <p>{{ $order->address_country }}</p>
            <p><strong>NIF:</strong> {{ $order->address_nif }}</p>
        </div>

        {{-- =======================
             PRODUCTS
        ======================= --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Products</h3>

            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-center">Qty</th>
                        <th class="px-3 py-2 text-right">Gross</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                {{ optional($item->product->translation())->name }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ number_format($item->total_gross, 2) }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- =======================
             TOTALS
        ======================= --}}
        <div class="bg-white shadow rounded p-4 text-right space-y-1">
            <p>Products (net): {{ number_format($order->products_total_net, 2) }} €</p>
            <p>Products tax: {{ number_format($order->products_total_tax, 2) }} €</p>
            <p>Shipping (gross): {{ number_format($order->shipping_gross, 2) }} €</p>
            <p>Shipping tax: {{ number_format($order->shipping_tax, 2) }} €</p>
            <hr>
            <p class="font-semibold">
                Total: {{ number_format($order->total_gross, 2) }} €
            </p>
        </div>

        {{-- =======================
             ADMIN CONTROLS
        ======================= --}}
        <form method="POST"
              action="{{ route('admin.orders.update', $order) }}"
              class="bg-white shadow rounded p-4 space-y-4">
            @csrf
            @method('PATCH')

            <h3 class="font-semibold">Admin Actions</h3>

            <div>
                <label class="block font-medium mb-1">Status</label>
                <select name="status" class="border rounded px-3 py-2 w-full">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->code }}"
                            @selected($order->status === $status->code)>
                            {{ optional($status->translation())->name ?? $status->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Tracking number</label>
                <input name="tracking_number"
                       value="{{ $order->tracking_number }}"
                       class="border rounded px-3 py-2 w-full">
            </div>
            
            <div>
                <label class="block font-medium mb-1">Tracking URL</label>
                <input name="tracking_url"
                       value="{{ $order->tracking_url }}"
                       type="url"
                       placeholder="https://track.carrier.com/..."
                       class="border rounded px-3 py-2 w-full">
                <p class="text-sm text-gray-500 mt-1">Full URL to tracking page (optional)</p>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_paid" value="1" @checked($order->is_paid)>
                    Paid
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_refunded" value="1" @checked($order->is_refunded)>
                    Refunded
                </label>
            </div>

            <div class="pt-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                    Save changes
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
