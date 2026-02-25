<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Payloads</h2>
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

                <input type="date" name="from_payload_date" placeholder="Payload from date"
                       value="{{ request('from_payload_date') }}"
                       class="border rounded px-3 py-2">

                <input type="date" name="to_payload_date" placeholder="Payload to date"
                       value="{{ request('to_payload_date') }}"
                       class="border rounded px-3 py-2">

                <div class="mt-4 text-right flex justify-end gap-2">
                    <a href="{{ route('admin.orders.payloads.index') }}" 
                    class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                        Reset
                    </a>
                    <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
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
                        <th class="px-3 py-2">Order Date</th>
                        <th class="px-3 py-2">Payload created at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payloads as $p)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.orders.payloads.show', $p) }}" class="text-accent-secondary hover:underline font-medium">{{ optional($p->order)->order_number ?? ('#' . $p->order_id) }}</a>
                            </td>
                            <td class="px-3 py-2">{{ optional($p->order)->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->created_at->format('d/m/Y H:i') }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
