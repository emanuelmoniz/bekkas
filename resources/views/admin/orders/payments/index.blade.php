<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Payments</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <nav class="flex gap-2 text-sm" aria-label="Admin orders subnav">
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('admin.orders.payloads.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payloads*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Payloads</a>
                <a href="{{ route('admin.orders.checkouts.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/checkouts*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Checkouts</a>
                <a href="{{ route('admin.orders.payments.index') }}" class="px-3 py-2 rounded {{ request()->is('admin/orders/payments*') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">Payments</a>
            </nav>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input name="order_number" placeholder="Order Number"
                       value="{{ request('order_number') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="from_paid_date" placeholder="Paid from date"
                       value="{{ request('from_paid_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="to_paid_date" placeholder="Paid to date"
                       value="{{ request('to_paid_date') }}"
                       class="border rounded px-3 py-2">

                <select name="payment_status" class="border rounded px-3 py-2">
                    <option value="">Any status</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>paid</option>
                    <option value="failed" @selected(request('payment_status') === 'failed')>failed</option>
                    <option value="pending" @selected(request('payment_status') === 'pending')>pending</option>
                </select>

                <select name="payment_method" class="border rounded px-3 py-2">
                    <option value="">Any method</option>
                    <option value="card" @selected(request('payment_method') === 'card')>card</option>
                    <option value="mb" @selected(request('payment_method') === 'mb')>mb</option>
                </select>

                <div></div>
            </div>

            <div class="mt-4 text-right flex justify-end gap-2">
                <a href="{{ route('admin.orders.payments.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Reset
                </a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Filter
                </button>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Order Number</th>
                        <th class="px-3 py-2">Order Date</th>
                        <th class="px-3 py-2">Paid at</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Method</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $p)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ optional($p->order)->order_number ?? ('#' . $p->order_id) }}</td>
                            <td class="px-3 py-2">{{ optional($p->order)->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->payment_status ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->payment_method ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">
                                @if($p->order)
                                    <div class="flex gap-2 justify-end items-center">
                                        <a href="{{ route('admin.orders.payments.show', $p) }}" class="text-sm bg-blue-50 border-blue-200 text-blue-700 border px-3 py-1 rounded">View</a>

                                        <a href="{{ route('admin.orders.show', $p->order) }}" class="text-sm bg-gray-50 border-gray-200 text-gray-700 border px-3 py-1 rounded">Order</a>

                                        @if($p->checkoutSession)
                                            <a href="{{ route('admin.orders.checkouts.show', $p->checkoutSession) }}" class="text-sm bg-indigo-50 border-indigo-200 text-indigo-700 border px-3 py-1 rounded">Checkout</a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">No order</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
