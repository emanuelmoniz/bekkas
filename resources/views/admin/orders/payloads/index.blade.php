<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Payloads</h2>
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

                <input type="date" name="from_order_date" placeholder="Order from date"
                       value="{{ request('from_order_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="to_order_date" placeholder="Order to date"
                       value="{{ request('to_order_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="from_payload_date" placeholder="Payload from date"
                       value="{{ request('from_payload_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="to_payload_date" placeholder="Payload to date"
                       value="{{ request('to_payload_date') }}"
                       class="border rounded px-3 py-2">

                <div></div>
            </div>

            <div class="mt-4 text-right flex justify-end gap-2">
                <a href="{{ route('admin.orders.payloads.index') }}" 
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
                        <th class="px-3 py-2">Payload created at</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payloads as $p)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ optional($p->order)->order_number ?? ('#' . $p->order_id) }}</td>
                            <td class="px-3 py-2">{{ optional($p->order)->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2 text-right">
                                @if($p->order)
                                    <div class="flex gap-2 justify-end items-center">
                                        <a href="{{ route('admin.orders.payloads.show', $p) }}" class="text-sm bg-blue-50 border-blue-200 text-blue-700 border px-3 py-1 rounded">View</a>
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
