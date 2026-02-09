<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Orders</h2>
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

                <input name="user" placeholder="User"
                       value="{{ request('user') }}"
                       class="border rounded px-3 py-2">

                <input name="email" placeholder="Email"
                       value="{{ request('email') }}"
                       class="border rounded px-3 py-2">

                <input name="nif" placeholder="NIF"
                       value="{{ request('nif') }}"
                       class="border rounded px-3 py-2">

                <select name="status" class="border rounded px-3 py-2">
                    <option value="">All statuses</option>
                    @php
                        $statuses = \App\Models\OrderStatus::with('translations')->orderBy('sort_order')->get();
                    @endphp
                    @foreach ($statuses as $statusObj)
                        <option value="{{ $statusObj->code }}"
                            @selected(request('status') === $statusObj->code)>
                            {{ optional($statusObj->translation())->name ?? $statusObj->code }}
                        </option>
                    @endforeach
                </select>

                <select name="is_paid" class="border rounded px-3 py-2">
                    <option value="">Paid (all)</option>
                    <option value="1" @selected(request('is_paid') === '1')>Yes</option>
                    <option value="0" @selected(request('is_paid') === '0')>No</option>
                </select>

                <input type="date" name="from_date" placeholder="From Date"
                       value="{{ request('from_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="to_date" placeholder="To Date"
                       value="{{ request('to_date') }}"
                       class="border rounded px-3 py-2">
            </div>

            <div class="mt-4 text-right flex justify-end gap-2">
                <a href="{{ route('admin.orders.index') }}" 
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
                        <th class="px-3 py-2">User</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Paid</th>
                        <th class="px-3 py-2">Total</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $order->order_number }}</td>
                            <td class="px-3 py-2">
                                {{ $order->user->name }}<br>
                                <span class="text-sm text-gray-500">
                                    {{ $order->user->email }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $statusObj = \App\Models\OrderStatus::where('code', $order->status)->first();
                                @endphp
                                {{ optional($statusObj?->translation())->name ?? $order->status }}
                            </td>
                            <td class="px-3 py-2">{{ $order->is_paid ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2">
                                {{ number_format($order->total_gross, 2) }} €
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex gap-2 justify-end items-center">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm bg-blue-50 border-blue-200 text-blue-700 border px-3 py-1 rounded">View</a>

                                    <a href="{{ route('admin.orders.checkouts.index', ['order_number' => $order->order_number]) }}" class="text-sm bg-indigo-50 border-indigo-200 text-indigo-700 border px-3 py-1 rounded">Checkouts</a>

                                    <a href="{{ route('admin.orders.payments.index', ['order_number' => $order->order_number]) }}" class="text-sm bg-indigo-50 border-indigo-200 text-indigo-700 border px-3 py-1 rounded">View payments</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
