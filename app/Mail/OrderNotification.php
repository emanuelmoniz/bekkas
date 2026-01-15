<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $eventLabel;
    public string $recipientName;
    public ?string $statusLabel;

    public function __construct(Order $order, string $eventLabel, string $recipientName, ?string $statusLabel = null)
    {
        $this->order = $order->load(['items.product', 'user']);
        $this->eventLabel = $eventLabel;
        $this->recipientName = $recipientName;
        $this->statusLabel = $statusLabel;
    }

    public function build(): self
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name', config('app.name', 'BEKKAS')))
            ->subject(t('orders.email.subject', ['order_number' => $this->order->order_number, 'event' => $this->eventLabel]) ?: ("Order #{$this->order->order_number} - {$this->eventLabel}"))
            ->markdown('emails.order-notification');
    }
}
