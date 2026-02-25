<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Order {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- STATUS --}}
        <div class="bg-white shadow rounded p-4">

            @if(! empty($paymentStatusMessage))
                <div class="my-3 p-3 rounded border border-grey-light border-l-4 bg-accent-primary/10 text-accent-primary text-sm">
                    {{ $paymentStatusMessage }}
                </div>
            @endif
            
            <p><strong>{{ t('orders.date') ?: 'Date' }}:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>{{ t('orders.status') ?: 'Status' }}:</strong> 
                @php
                    $statusObj = \App\Models\OrderStatus::where('code', $order->status)->first();
                @endphp
                {{ optional($statusObj?->translation())->name ?? $order->status }}
            </p>
            <p><strong>{{ t('orders.paid') ?: 'Paid' }}:</strong> {{ $order->is_paid ? (t('orders.yes') ?: 'Yes') : (t('orders.no') ?: 'No') }}</p>
            <p><strong>{{ t('orders.refunded') ?: 'Refunded' }}:</strong> {{ $order->is_refunded ? (t('orders.yes') ?: 'Yes') : (t('orders.no') ?: 'No') }}</p>

            @php
                $trackingStatuses = json_decode(\App\Models\ShippingConfig::get('tracking_statuses', '["shipped","delivered"]'), true);
            @endphp
            @if(in_array($order->status, $trackingStatuses ?? []))
                @if ($order->tracking_number)
                    <p><strong>{{ t('orders.tracking') ?: 'Tracking' }}:</strong> {{ $order->tracking_number }}</p>
                @endif
                @if($order->tracking_url)
                    <p>
                        <a href="{{ $order->tracking_url }}" target="_blank" class="text-accent-secondary hover:underline">
                            {{ t('orders.track_shipment') ?: 'Track Your Shipment' }}
                        </a>
                    </p>
                @else
                    <p class="text-grey-dark italic">{{ t('orders.no_tracking') ?: 'Your order does not have tracking information yet' }}</p>
                @endif
            @endif

            @if($order->expected_delivery_date)
                <p><strong>{{ t('orders.expected_delivery') ?: 'Expected Delivery' }}:</strong> {{ $order->expected_delivery_date->format('d/m/Y') }}</p>
            @endif

            @if($order->status === 'WAITING_PAYMENT' && auth()->check() && auth()->id() === $order->user_id && ! $order->is_paid)
                <div class="mt-3">
                    @php $ps = $paymentStatus ?? null; @endphp

                    {{-- If a persisted payment is pending, show payment information and a link to the pay page (change/payment) --}}
                    @if(isset($paymentInfo) && $paymentInfo?->payment_status === 'pending')
                        <div class="mb-4 bg-white border rounded p-4 text-sm">
                            <h3 class="font-semibold mb-2">{{ t('checkout.pay.payment_info_title') ?: 'Payment information' }}</h3>
                            <div class="space-y-2 text-grey-dark">
                                @if($paymentInfo->mb_entity)
                                    <div><strong>{{ t('checkout.pay.mb_entity') ?: 'MB entity' }}:</strong> {{ $paymentInfo->mb_entity }}</div>
                                @endif
                                @if($paymentInfo->mb_reference)
                                    <div><strong>{{ t('checkout.pay.mb_reference') ?: 'MB reference' }}:</strong> {{ $paymentInfo->mb_reference }}</div>
                                @endif
                                @if($paymentInfo->mb_expiration_time)
                                    <div><strong>{{ t('checkout.pay.mb_expires') ?: 'MB expiration time' }}:</strong> {{ $paymentInfo->mb_expiration_time->toDayDateTimeString() }}</div>
                                @endif
                                @if($paymentInfo->iban)
                                    <div><strong>{{ t('checkout.pay.iban') ?: 'IBAN' }}:</strong> {{ $paymentInfo->iban }}</div>
                                @endif
                            </div>

                            <div class="mt-4 text-right">
                                <a href="{{ route('orders.pay', $order->uuid) }}" class="inline-block bg-accent-secondary hover:bg-accent-secondary/90 text-light px-4 py-2 rounded-lg font-semibold">{{ t('orders.change_payment') ?: 'Change payment' }}</a>
                            </div>
                        </div>

                    {{-- Otherwise: show the regular pay button unless DB indicates pending/authorised (suppress) --}}
                    @else
                        @unless(in_array($ps, ['pending','authorised'], true))
                            @if(config('easypay.enabled'))
                                <a href="{{ route('orders.pay', $order->uuid) }}" class="inline-block bg-accent-secondary hover:bg-accent-secondary/90 text-light px-4 py-2 rounded-lg font-semibold">{{ t('orders.pay_now') ?: 'Pay now' }}</a>
                            @else
                                <div class="p-3 rounded bg-accent-secondary/10 border border-yellow-100 text-sm text-yellow-800">
                                    {{ t('checkout.gateways.disabled') ?: (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.') }}
                                </div>
                            @endif
                        @endunless
                    @endif
                </div>
            @endif

        </div>

        {{-- ADDRESS --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">{{ t('orders.shipping_address') ?: 'Shipping Address' }}</h3>
            <p>{{ $order->address_title }}</p>
            <p>{{ $order->address_line_1 }}</p>
            @if($order->address_line_2)
                <p>{{ $order->address_line_2 }}</p>
            @endif
            <p>{{ $order->address_postal_code }} {{ $order->address_city }}</p>
            <p>{{ $order->address_country }}</p>
            <p><strong>{{ t('orders.nif') ?: 'NIF' }}:</strong> {{ $order->address_nif }}</p>
        </div>

        {{-- PRODUCTS --}}
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">{{ t('orders.products') ?: 'Products' }}</h3>
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-3 py-2 text-left">{{ t('orders.product') ?: 'Product' }}</th>
                        <th class="px-3 py-2">{{ t('orders.qty') ?: 'Qty' }}</th>
                        <th class="px-3 py-2">{{ t('orders.gross') ?: 'Gross' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                {{ optional($item->product->translation())->name }}
                            </td>
                            <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right">
                                {{ number_format($item->total_gross, 2) }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TOTALS --}}
        <div class="bg-white shadow rounded p-4 text-right space-y-1">
            <p>{{ t('orders.products_net') ?: 'Products (net)' }}: {{ number_format($order->products_total_net, 2) }} €</p>

            @if($order->tax_enabled)
                <p>{{ t('orders.products_tax') ?: 'Products tax' }}: {{ number_format($order->products_total_tax, 2) }} €</p>
                <p>{{ t('orders.shipping') ?: 'Shipping' }}: {{ number_format($order->shipping_gross, 2) }} €</p>
                <p>{{ t('orders.shipping_tax') ?: 'Shipping tax' }}: {{ number_format($order->shipping_tax, 2) }} €</p>
            @else
                <p>{{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}</p>
                <p>{{ t('orders.shipping') ?: 'Shipping' }}: {{ number_format($order->shipping_gross, 2) }} €</p>
            @endif

            <hr>
            <p class="font-semibold">
                {{ t('orders.total') ?: 'Total' }}: {{ number_format($order->total_gross, 2) }} €
            </p>
        </div>

    </div>
</x-app-layout>
