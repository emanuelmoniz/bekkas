<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;

    public string $eventLabel;

    public string $recipientName;

    public ?string $statusLabel;

    /**
     * @var array|null Translation parameters when a translation key is provided
     */
    public ?array $eventParams = null;

    /**
     * Optional explicit action URL for the primary button in the mail. If not set
     * the template falls back to the public `orders.show` route.
     */
    public ?string $actionUrl = null;

    /**
     * Accept either a translation key or a literal label for backwards compatibility.
     * When queued/sent the Mailable's locale will be honoured and the label will be
     * translated at build time using the provided params.
     *
     * Backwards-compatible signature: the new `$actionUrl` is optional and comes last.
     */
    public function __construct(Order $order, string $event, string $recipientName, ?string $statusLabel = null, ?array $eventParams = null, ?string $actionUrl = null)
    {
        $this->order = $order->load(['items.product', 'user']);
        $this->eventLabel = $event; // may be key or literal; resolved in build()
        $this->recipientName = $recipientName;
        $this->statusLabel = $statusLabel;
        $this->eventParams = $eventParams;
        $this->actionUrl = $actionUrl;
    }

    public function build(): self
    {
        // Resolve event label at build time so the Mailable's locale is respected.
        $label = t($this->eventLabel, $this->eventParams ?? []) ?: $this->eventLabel;
        $this->eventLabel = $label;

        return $this
            ->from(config('mail.from.address'), config('mail.from.name', config('app.name', 'BEKKAS')))
            ->subject(t('orders.email.subject', ['order_number' => $this->order->order_number, 'event' => $this->eventLabel]) ?: ("Order #{$this->order->order_number} - {$this->eventLabel}"))
            ->markdown('emails.order-notification');
    }
}
