@component('mail::message')
# {{ t('orders.email.greeting', ['name' => $recipientName]) ?: ('Hello ' . $recipientName . ',') }}

{{ $eventLabel }}

**{{ t('orders.email.order_label') ?: 'Order' }}:** {{ $order->order_number ?? $order->id }}

**{{ t('orders.email.status_label') ?: 'Status' }}:** {{ $statusLabel ?? (\App\Models\OrderStatus::where('code', $order->status)->first()?->translation(app()->getLocale())?->name ?? ucfirst($order->status)) }}

**{{ t('orders.email.total_label') ?: 'Total' }}:** {{ number_format($order->total_gross, 2) }}

@if($order->items && $order->items->count())
**{{ t('orders.email.items_label') ?: 'Items' }}:**

@foreach($order->items as $item)
- {{ optional($item->product->translation())->name ?? ('Product #' . $item->product_id) }} — **x{{ $item->quantity }}** — {{ number_format($item->total_gross, 2) }}
@endforeach
@endif

@component('mail::button', ['url' => route('orders.show', $order)])
{{ t('orders.email.view_button') ?: 'View Order' }}
@endcomponent

{{ t('orders.email.auto_sent') ?: 'This email was sent automatically.' }}

{{ t('orders.email.thanks') ?: 'Thanks,' }}
{{ config('app.name', 'BEKKAS') }}
@endcomponent
