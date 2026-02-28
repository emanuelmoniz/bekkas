<x-app-layout>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filters --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="flex flex-wrap items-center justify-between gap-3">

                <div class="flex flex-wrap items-center gap-3">
                    {{-- Order # --}}
                    <input
                        type="text"
                        name="order_number"
                        value="{{ request('order_number') }}"
                        placeholder="{{ t('orders.order_number') ?: 'Order #' }}"
                        class="border rounded px-3 py-2 w-40"
                    >

                    {{-- Status --}}
                    <select name="status" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">{{ t('orders.status') ?: 'Status' }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->code }}" @selected(request('status') === $status->code)>
                                {{ optional($status->translation())->name ?? $status->code }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Paid --}}
                    <select name="paid" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">{{ t('orders.paid_all') ?: 'All' }}</option>
                        <option value="1" @selected(request('paid') === '1')>{{ t('orders.paid') ?: 'Paid' }}</option>
                        <option value="0" @selected(request('paid') === '0')>{{ t('orders.not_paid') ?: 'Not Paid' }}</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('orders.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase">
                        {{ t('orders.reset') ?: 'Reset' }}
                    </a>
                    <button
                        type="submit"
                        class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase"
                    >
                        {{ t('orders.filter') ?: 'Filter' }}
                    </button>
                </div>

            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ t('orders.order_number') ?: 'Order #' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.date') ?: 'Date' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.last_update') ?: 'Last Update' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.status') ?: 'Status' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.total') ?: 'Total' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.paid') ?: 'Paid' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-mono text-sm">
                                <a href="{{ route('orders.show', $order) }}" class="text-accent-secondary hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-2">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $statusObj = \App\Models\OrderStatus::where('code', $order->status)->first();
                                @endphp
                                {{ optional($statusObj?->translation())->name ?? $order->status }}
                            </td>
                            <td class="px-4 py-2">{{ number_format($order->total_gross, 2) }} €</td>
                            <td class="px-4 py-2">
                                {{ $order->is_paid ? (t('orders.paid') ?: 'Yes') : (t('orders.not_paid') ?: 'No') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-grey-medium">
                                {{ t('orders.no_orders') ?: 'No orders found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
