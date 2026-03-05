<x-app-layout>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">

            {{-- STATUS --}}
            <div class="bg-white shadow rounded p-4">

                @php
                    // custom messages for certain order states override any paymentStatusMessage
                    $customStatusMessage = null;
                    if($order->status === 'DISPATCHED') {
                        $customStatusMessage = t('orders.status.dispatched_message') ?: 'Our order is on the way. Check tracking information below';
                    } elseif($order->status === 'DELIVERED') {
                        $customStatusMessage = t('orders.status.delivered_message') ?: 'Your order was delivered.';
                    } elseif($order->status === 'REFUNDED') {
                        $customStatusMessage = t('orders.status.refunded_message') ?: 'Your order was refunded.';
                    } elseif($order->status === 'CANCELED') {
                        $customStatusMessage = t('orders.status.canceled_message') ?: 'Your order was canceled. For more information, please contact us.';
                    }
                @endphp

                @if($customStatusMessage)
                    <div class="my-3 p-3 rounded border border-status-info border-l-4 bg-status-info/10 text-status-info text-sm">
                        {{ $customStatusMessage }}
                    </div>
                @elseif(! empty($paymentStatusMessage))
                    <div class="my-3 p-3 rounded border border-status-info border-l-4 bg-status-info/10 text-status-info text-sm">
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
                            <a href="{{ $order->tracking_url }}" target="_blank" class="text-accent-primary hover:underline">
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
                                    <x-primary-cta as="a" :href="route('orders.pay', $order->uuid)">{{ t('orders.change_payment') ?: 'Change payment' }}</x-primary-cta>
                                </div>
                            </div>

                        {{-- Otherwise: show the regular pay button unless DB indicates pending/authorised (suppress) --}}
                        @else
                            @unless(in_array($ps, ['pending','authorised'], true))
                                @if(config('easypay.enabled'))
                                    <x-primary-cta as="a" :href="route('orders.pay', $order->uuid)">{{ t('orders.pay_now') ?: 'Pay now' }}</x-primary-cta>
                                @else
                                    <div class="p-3 rounded bg-primary/10 border border-yellow-100 text-sm text-yellow-800">
                                        {{ t('checkout.gateways.disabled') ?: (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.') }}
                                    </div>
                                @endif
                            @endunless
                        @endif
                    </div>
                @endif

            </div>

            {{-- ADDRESS --}}
            <div class="bg-white shadow rounded p-4 flex flex-col justify-end">
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
        </div>

        

        {{-- PRODUCTS --}}
        <div class="bg-white shadow rounded p-4">
            <!--<h3 class="font-semibold mb-2">{{ t('orders.products') ?: 'Products' }}</h3>-->
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
                                @if($item->orderItemOptions->count())
                                    <div class="text-xs text-gray-500 mt-0.5 space-y-0.5">
                                        @foreach($item->orderItemOptions as $opt)
                                            <span class="block">{{ $opt->option_type_name }}: {{ $opt->option_name }}</span>
                                        @endforeach
                                    </div>
                                @endif
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
                <p class="text-sm text-grey-medium">{{ t('orders.products_tax') ?: 'Products tax' }}: {{ number_format($order->products_total_tax, 2) }} €</p>
                <p>{{ t('orders.shipping') ?: 'Shipping' }}: {{ number_format($order->shipping_gross - $order->shipping_tax, 2) }} €</p>
                <p class="text-sm text-grey-medium">{{ t('orders.shipping_tax') ?: 'Shipping tax' }}: {{ number_format($order->shipping_tax, 2) }} €</p>
            @else
                <p>{{ t('orders.shipping') ?: 'Shipping' }}: {{ number_format($order->shipping_gross, 2) }} €</p>
                <p class="text-sm text-grey-medium">{{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}</p>
            @endif

            <hr>
            <p class="font-semibold">
                {{ t('orders.total') ?: 'Total' }}: {{ number_format($order->total_gross, 2) }} €
            </p>
        </div>

    </div>
</x-app-layout>
