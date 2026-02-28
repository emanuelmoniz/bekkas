<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Orders</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 pb-4">
                <input name="order_number" placeholder="Order Number"
                       value="{{ request('order_number') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input name="user" placeholder="User"
                       value="{{ request('user') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input name="email" placeholder="Email"
                       value="{{ request('email') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input name="nif" placeholder="NIF"
                       value="{{ request('nif') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <select name="status" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

                <select name="is_paid" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Paid (all)</option>
                    <option value="1" @selected(request('is_paid') === '1')>Yes</option>
                    <option value="0" @selected(request('is_paid') === '0')>No</option>
                </select>

                <input type="date" name="from_date" placeholder="From Date"
                       value="{{ request('from_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="to_date" placeholder="To Date"
                       value="{{ request('to_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
            
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.orders.index') }}'"
        class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">
                        Reset
                    </button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                        Filter
                    </button>
                </div>       
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-3 py-2">Order Number</th>
                        <th class="px-3 py-2">User</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Paid</th>
                        <th class="px-3 py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-accent-secondary hover:underline font-medium">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-3 py-2">
                                {{ $order->user->name }}<br>
                                <span class="text-sm text-grey-medium">
                                    {{ $order->user->email }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                    $statusObj = \App\Models\OrderStatus::where('code', $order->status)->first();
                                @endphp
                                {{ optional($statusObj?->translation())->name ?? $order->status }}
                            </td>
                            <td class="px-3 py-2">
                                @if($order->is_paid)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                {{ number_format($order->total_gross, 2) }} €
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
