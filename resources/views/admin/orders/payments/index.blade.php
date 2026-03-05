<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Payments</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <input name="order_number" placeholder="Order Number"
                       value="{{ request('order_number') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="from_paid_date" placeholder="Paid from date"
                       value="{{ request('from_paid_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="to_paid_date" placeholder="Paid to date"
                       value="{{ request('to_paid_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex justify-end gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.orders.payments.index') }}'">
                        Reset
                    </x-default-button>
                    <x-default-button type="submit">
                        Filter
                    </x-default-button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-3 py-2">Order Number</th>
                        <th class="px-3 py-2">Order Date</th>
                        <th class="px-3 py-2">Paid at</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Method</th>
                        <th class="px-3 py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $p)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.orders.payments.show', $p) }}" class="font-medium text-accent-primary hover:text-accent-primary/90 no-underline">{{ optional($p->order)->order_number ?? ('#' . $p->order_id) }}</a>
                            </td>
                            <td class="px-3 py-2">{{ optional($p->order)->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->payment_status ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->payment_method ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('admin.orders.payments.show', $p) }}" class="text-accent-primary hover:text-accent-primary/90 no-underline">View</a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
