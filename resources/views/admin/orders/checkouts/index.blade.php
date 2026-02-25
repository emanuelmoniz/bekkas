<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Checkout sessions</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                <input type="date" name="from_session_date" placeholder="Session from date"
                       value="{{ request('from_session_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="to_session_date" placeholder="Session to date"
                       value="{{ request('to_session_date') }}"
                       class="border rounded px-3 py-2">
            </div>

            <div class="mt-4 text-right flex justify-end gap-2">
                <a href="{{ route('admin.orders.checkouts.index') }}" 
                   class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                    Reset
                </a>
                <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    Filter
                </button>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $s)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.orders.checkouts.show', $s) }}" class="text-accent-secondary hover:underline font-medium">{{ optional($s->order)->order_number ?? ('#' . $s->order_id) }}</a>
                            </td>
                            <td class="px-3 py-2">{{ optional($s->order)->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2">{{ $s->status ?? '-' }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
