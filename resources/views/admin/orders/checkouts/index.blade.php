<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Checkout sessions</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                <input name="order_number" placeholder="Order Number"
                       value="{{ request('order_number') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="from_order_date" placeholder="Order from date"
                       value="{{ request('from_order_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="to_order_date" placeholder="Order to date"
                       value="{{ request('to_order_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="from_session_date" placeholder="Session from date"
                       value="{{ request('from_session_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <input type="date" name="to_session_date" placeholder="Session to date"
                       value="{{ request('to_session_date') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex justify-end gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.orders.checkouts.index') }}'">
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
                        <th class="px-3 py-2">Session created at</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $s)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.orders.checkouts.show', $s) }}" class="font-medium text-accent-primary hover:text-accent-primary/90 no-underline">{{ optional($s->order)->order_number ?? ('#' . $s->order_id) }}</a>
                            </td>
                            <td class="px-3 py-2">{{ optional($s->order)->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2">{{ $s->status ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('admin.orders.checkouts.show', $s) }}" class="text-accent-primary hover:text-accent-primary/90 no-underline">View</a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
