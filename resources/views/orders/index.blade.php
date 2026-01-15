<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ t('orders.my_orders') ?: 'My Orders' }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ t('orders.order_number') ?: 'Order #' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.date') ?: 'Date' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.last_update') ?: 'Last Update' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.status') ?: 'Status' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.total') ?: 'Total' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('orders.paid') ?: 'Paid' }}</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $order->order_number }}</td>
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
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="text-blue-600 hover:underline">
                                    {{ t('orders.view') ?: 'View' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                {{ t('orders.no_orders') ?: 'No orders found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
