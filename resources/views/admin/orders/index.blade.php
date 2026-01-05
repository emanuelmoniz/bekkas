<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Orders</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="GET" class="mb-4 grid grid-cols-6 gap-2">
            <input name="user" placeholder="User"
                   value="{{ request('user') }}"
                   class="border rounded px-2 py-1">

            <input name="email" placeholder="Email"
                   value="{{ request('email') }}"
                   class="border rounded px-2 py-1">

            <input name="nif" placeholder="NIF"
                   value="{{ request('nif') }}"
                   class="border rounded px-2 py-1">

            <select name="status" class="border rounded px-2 py-1">
                <option value="">All statuses</option>
                @foreach (['PROCESSING','DISPATCHED','DELIVERED','CANCELED'] as $status)
                    <option value="{{ $status }}"
                        @selected(request('status') === $status)>
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            <select name="is_paid" class="border rounded px-2 py-1">
                <option value="">Paid (all)</option>
                <option value="1" @selected(request('is_paid') === '1')>Yes</option>
                <option value="0" @selected(request('is_paid') === '0')>No</option>
            </select>

            <button class="bg-blue-600 text-white rounded px-3">
                Filter
            </button>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">#</th>
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
                            <td class="px-3 py-2">#{{ $order->id }}</td>
                            <td class="px-3 py-2">
                                {{ $order->user->name }}<br>
                                <span class="text-sm text-gray-500">
                                    {{ $order->user->email }}
                                </span>
                            </td>
                            <td class="px-3 py-2">{{ $order->status }}</td>
                            <td class="px-3 py-2">{{ $order->is_paid ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2">
                                {{ number_format($order->total_gross, 2) }} €
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-blue-600 hover:underline">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
