<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Easypay payment</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6 space-y-6">
            <div class="flex justify-between items-start gap-4">
                <dl class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Payment ID</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->payment_id ?? $payment->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Order</p>
                        <p class="text-sm text-grey-dark mt-1">
                            @if($payment->order)
                                <a class="text-accent-secondary hover:underline" href="{{ route('admin.orders.show', $payment->order) }}">{{ $payment->order->order_number }}</a>
                            @else
                                <span class="text-grey-medium">No order</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Checkout session</p>
                        <p class="text-sm text-grey-dark mt-1">
                            @if($payment->checkoutSession)
                                <a class="text-accent-primary hover:underline" href="{{ route('admin.orders.checkouts.show', $payment->checkoutSession) }}">Session #{{ $payment->checkoutSession->id }}</a>
                            @else
                                <span class="text-grey-medium">—</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Created</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Paid at</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Status</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->payment_status ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Method</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->payment_method ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Card last digits</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->card_last_digits ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">MB entity / reference</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->mb_entity ?? '-' }} / {{ $payment->mb_reference ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">IBAN</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->iban ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Capture</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->capture_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Refund request ID</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payment->refund_id ?? '-' }}</p>
                    </div>
                </dl>

                <div class="flex flex-col items-end gap-2">
                    <div class="w-full flex flex-wrap justify-end items-center gap-2 text-right">
                        @if($payment->order)
                            <button type="button" onclick="window.location.href='{{ route('admin.orders.show', $payment->order) }}'" class="inline-flex items-center bg-grey-light border px-2 py-2 rounded uppercase text-sm">Order</button>

                            {{-- Payload: prefer checkoutSession->payload, fall back to order->easypayPayload --}}
                            @php
                                $payload = $payment->checkoutSession?->payload ?? $payment->order?->easypayPayload ?? null;
                            @endphp
                            @if($payload)
                                <button type="button" onclick="window.location.href='{{ route('admin.orders.payloads.show', $payload) }}'" class="inline-flex items-center bg-white border px-2 py-2 rounded uppercase text-sm ms-2">Payload</button>
                            @endif
                        @endif

                        @if($payment->checkoutSession)
                            <button type="button" onclick="window.location.href='{{ route('admin.orders.checkouts.show', $payment->checkoutSession) }}'" class="inline-flex items-center bg-primary/10 border-accent-primary/30 text-accent-primary border px-2 py-2 rounded uppercase text-sm ms-2">Checkout</button>

                            {{-- Update: refresh payment details from Easypay (single payment endpoint) --}}
                            <form method="POST" action="{{ route('admin.orders.payments.refresh', $payment) }}" class="inline-block ms-2">
                                @csrf
                                <button class="bg-primary/10 border-accent-secondary/20 text-accent-secondary border px-2 py-2 rounded uppercase text-sm">Update</button>
                            </form>
                        @endif

                        @if(strtolower((string) $payment->payment_status) === 'paid' && optional($payment->order)->is_paid)
                            <form method="POST" action="{{ route('admin.orders.payments.refund', $payment) }}" onsubmit="return confirm('Confirm refund request?');" class="inline-block ms-2">
                                @csrf
                                <button type="submit" class="inline-flex items-center bg-white border px-2 py-2 rounded uppercase text-sm text-grey-dark hover:bg-grey-light">Refund</button>
                            </form>

                            @if(! empty($payment->refund_id))
                                <form method="POST" action="{{ route('admin.orders.payments.refund.refresh', $payment) }}" class="inline-block ms-2">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center bg-status-warning border-amber-200 text-status-warning border px-2 py-2 rounded uppercase text-sm">Update refund</button>
                                </form>
                            @endif
                        @endif

                        <button type="button" onclick="window.location.href='{{ route('admin.orders.payments.index') }}'" class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light ms-2">Back</button>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs text-grey-dark uppercase mb-2">Gateway raw response</h3>
                <pre class="whitespace-pre-wrap bg-white border rounded p-4 text-sm overflow-auto" style="max-height:48vh;overflow-wrap:anywhere;word-break:break-word;">{{ json_encode($payment->raw_response ?: [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
