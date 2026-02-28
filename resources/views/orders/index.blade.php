<x-app-layout>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filters --}}
        <form method="GET" class="mb-6 lg:bg-white lg:p-4 lg:rounded lg:shadow" x-data="{ open: false }">

            {{-- Mobile toggle button --}}
            <button type="button" @click="open = !open"
                class="lg:hidden w-full flex items-center justify-between bg-white border border-grey-light rounded-full uppercase px-8 py-3 mb-2 font-semibold">
                <span>{{ t('orders.filters') ?: 'Filters' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Filter panel: collapsed on mobile, always visible on desktop --}}
            <div x-show="open" x-cloak
                 class="bg-white border border-grey-light rounded p-4 lg:bg-transparent lg:border-0 lg:rounded-none lg:p-0 lg:!flex lg:flex-wrap lg:items-center lg:justify-between lg:gap-3 lg:mt-0">

                <div class="flex flex-col lg:flex-row lg:flex-wrap lg:items-center gap-2 lg:gap-3">
                    {{-- Order # --}}
                    <input
                        type="text"
                        name="order_number"
                        value="{{ request('order_number') }}"
                        placeholder="{{ t('orders.order_number') ?: 'Order #' }}"
                        class="w-full lg:w-40 border rounded px-3 py-2"
                    >

                    {{-- Status --}}
                    <select name="status" class="w-full lg:w-auto border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">{{ t('orders.status') ?: 'Status' }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->code }}" @selected(request('status') === $status->code)>
                                {{ optional($status->translation())->name ?? $status->code }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Paid --}}
                    <select name="paid" class="w-full lg:w-auto border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">{{ t('orders.paid_all') ?: 'All' }}</option>
                        <option value="1" @selected(request('paid') === '1')>{{ t('orders.paid') ?: 'Paid' }}</option>
                        <option value="0" @selected(request('paid') === '0')>{{ t('orders.not_paid') ?: 'Not Paid' }}</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:shrink-0 gap-2 mt-2 lg:mt-0">
                    <a href="{{ route('orders.index') }}"
                       class="w-full lg:w-auto text-center bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase">
                        {{ t('orders.reset') ?: 'Reset' }}
                    </a>
                    <button
                        type="submit"
                        class="w-full lg:w-auto bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase"
                    >
                        {{ t('orders.filter') ?: 'Filter' }}
                    </button>
                </div>

            </div>
        </form>

        <div class="bg-white shadow rounded">

            {{-- Desktop table (md+) --}}
            <table class="hidden lg:table w-full border">
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

            {{-- Mobile cards (< md) --}}
            <div class="lg:hidden divide-y divide-grey-light">
                @forelse ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}"
                       class="block px-4 py-3 hover:bg-grey-light/40 transition-colors">

                        {{-- Order number + total --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-sm text-accent-secondary">
                                {{ $order->order_number }}
                            </span>
                            <span class="font-semibold text-sm text-grey-dark">
                                {{ number_format($order->total_gross, 2) }} €
                            </span>
                        </div>

                        {{-- Status + paid badge --}}
                        <div class="mt-1 flex flex-wrap items-center gap-1.5">
                            @php
                                $statusObj = \App\Models\OrderStatus::where('code', $order->status)->first();
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full bg-grey-light text-grey-dark">
                                {{ optional($statusObj?->translation())->name ?? $order->status }}
                            </span>
                            @if($order->is_paid)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                    {{ t('orders.paid') ?: 'Paid' }}
                                </span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                    {{ t('orders.not_paid') ?: 'Not Paid' }}
                                </span>
                            @endif
                        </div>

                        {{-- Created + updated dates --}}
                        <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-grey-medium">
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            <span>{{ t('orders.last_update') ?: 'Updated' }}: {{ $order->updated_at->format('d/m/Y H:i') }}</span>
                        </div>

                    </a>
                @empty
                    <p class="px-4 py-6 text-center text-grey-medium">
                        {{ t('orders.no_orders') ?: 'No orders found.' }}
                    </p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
