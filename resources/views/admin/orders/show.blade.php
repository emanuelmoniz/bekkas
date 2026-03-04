<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Order {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- =======================
             ORDER INFO (READ)
        ======================= --}}
        <div class="bg-white shadow rounded p-6">
            <dl class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Date</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">{{ t('orders.status') ?: 'Status' }}</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @php $currentStatus = $statuses->firstWhere('code', $order->status); @endphp
                        {{ optional($currentStatus?->translation())->name ?? $order->status }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">{{ t('orders.paid') ?: 'Paid' }}</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($order->is_paid)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">{{ t('orders.refunded') ?: 'Refunded' }}</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($order->is_refunded)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">User</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->user->name }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Email</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->user->email }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">NIF</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->address_nif }}</p>
                </div>

                @if($order->shipping_tier_name)
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Shipping Tier</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $order->shipping_tier_name }}</p>
                    </div>
                @endif

                @if($order->expected_delivery_date)
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Expected Delivery</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $order->expected_delivery_date->format('d/m/Y') }}</p>
                    </div>
                @endif

                @if ($order->tracking_number)
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Tracking</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $order->tracking_number }}</p>
                    </div>
                @endif
            </dl>

            @if($order->easypayPayload)
                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.orders.payloads.show', $order->easypayPayload) }}'" class="inline-block bg-white border border-grey-medium px-2 py-2 rounded uppercase text-sm">Payload</button>
                    <button type="button" onclick="window.location.href='{{ route('admin.orders.checkouts.index', ['order_number' => $order->order_number]) }}'" class="inline-block bg-white border border-grey-medium px-2 py-2 rounded uppercase text-sm">Checkouts</button>
                    <button type="button" onclick="window.location.href='{{ route('admin.orders.payments.index', ['order_number' => $order->order_number]) }}'" class="inline-block bg-white border border-grey-medium px-2 py-2 rounded uppercase text-sm">Payments</button>
                </div>
            @else
                <div class="mt-4">
                    <form method="POST" action="{{ route('admin.orders.payloads.store', $order) }}" onsubmit="return confirm('Create Easypay payload for this order?');" class="inline-block">
                        @csrf
                        <button class="bg-status-success/10 border-green-200 text-status-success border px-2 py-2 rounded uppercase text-sm">Create payload</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- =======================
             SHIPPING ADDRESS
        ======================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Shipping Address</h3>
            <dl class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="lg:col-span-2">
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Name</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->address_title }}</p>
                </div>
                <div class="lg:col-span-2">
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Address</p>
                    <p class="text-sm text-grey-dark mt-1">
                        {{ $order->address_line_1 }}
                        @if($order->address_line_2)<br>{{ $order->address_line_2 }}@endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Postal Code / City</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->address_postal_code }} {{ $order->address_city }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Country</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->address_country }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">NIF</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $order->address_nif }}</p>
                </div>
            </dl>
        </div>

        {{-- =======================
             PRODUCTS
        ======================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Products</h3>

            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-center">Qty</th>
                        <th class="px-3 py-2 text-right">Gross</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                {{ optional($item->product->translation())->name }}
                                @if($item->orderItemOptions->count())
                                    <div class="text-xs text-grey-medium mt-0.5 space-y-0.5">
                                        @foreach($item->orderItemOptions as $opt)
                                            <span class="block">{{ $opt->option_type_name }}: {{ $opt->option_name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->total_gross, 2) }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- =======================
             TOTALS
        ======================= --}}
        <div class="bg-white shadow rounded p-6 text-right space-y-1">
            <p class="text-sm text-grey-dark">Products (net): {{ number_format($order->products_total_net, 2) }} €</p>

            @if($order->tax_enabled)
                <p class="text-sm text-grey-dark">Products tax: {{ number_format($order->products_total_tax, 2) }} €</p>
                <p class="text-sm text-grey-dark">Shipping (gross): {{ number_format($order->shipping_gross, 2) }} €</p>
                <p class="text-sm text-grey-dark">Shipping tax: {{ number_format($order->shipping_tax, 2) }} €</p>
            @else
                <p class="text-sm text-grey-dark">{{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}</p>
                <p class="text-sm text-grey-dark">Shipping (gross): {{ number_format($order->shipping_gross, 2) }} €</p>
            @endif

            <hr class="border-grey-medium">
            <p class="text-sm text-grey-dark">
                Total: {{ number_format($order->total_gross, 2) }} €
            </p>
        </div>

        {{-- =======================
             ADMIN CONTROLS
        ======================= --}}
        <form method="POST"
              action="{{ route('admin.orders.update', $order) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            <h3 class="text-xs text-grey-dark uppercase tracking-widest">Admin Actions</h3>

            <div>
                <x-input-label for="status" value="Status" />
                <select name="status" id="status" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm w-full mt-1">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->code }}"
                            @selected($order->status === $status->code)>
                            {{ optional($status->translation())->name ?? $status->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="tracking_number" value="Tracking number" />
                <x-text-input name="tracking_number" id="tracking_number"
                              value="{{ $order->tracking_number }}"
                              class="w-full mt-1" />
            </div>

            <div>
                <x-input-label for="tracking_url" value="Tracking URL" />
                <x-text-input name="tracking_url" id="tracking_url"
                              value="{{ $order->tracking_url }}"
                              type="url"
                              placeholder="https://track.carrier.com/..."
                              class="w-full mt-1" />
                <p class="text-sm text-grey-medium mt-1">Full URL to tracking page (optional)</p>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center gap-2 text-sm text-grey-dark">
                    <input type="checkbox" name="is_paid" value="1" @checked($order->is_paid)>
                    Paid
                </label>

                <label class="flex items-center gap-2 text-sm text-grey-dark">
                    <input type="checkbox" name="is_refunded" value="1" @checked($order->is_refunded)>
                    Refunded
                </label>
            </div>

            <div class="pt-2 flex justify-between">
                <button type="button"
                   onclick="window.location.href='{{ route('admin.orders.index') }}'"
                   class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light">
                    Back
                </button>
                <x-primary-button>
                    Save changes
                </x-primary-button>
            </div>
        </form>

    </div>
</x-app-layout>
