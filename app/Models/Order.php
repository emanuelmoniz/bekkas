<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'status',
        'is_paid',
        'is_canceled',
        'is_refunded',
        'tracking_number',
        'tracking_url',
        'expected_delivery_date',

        // Address snapshot
        'address_title',
        'address_nif',
        'address_line_1',
        'address_line_2',
        'address_postal_code',
        'address_city',
        'address_country',

        'products_total_net',
        'products_total_tax',
        'products_total_gross',

        'shipping_net',
        'shipping_tax',
        'shipping_gross',
        'shipping_tier_name',
        'tax_enabled',

        'total_net',
        'total_tax',
        'total_gross',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_canceled' => 'boolean',
        'is_refunded' => 'boolean',
        'products_total_net' => 'decimal:2',
        'products_total_tax' => 'decimal:2',
        'products_total_gross' => 'decimal:2',
        'shipping_net' => 'decimal:2',
        'shipping_tax' => 'decimal:2',
        'shipping_gross' => 'decimal:2',
        'total_net' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'tax_enabled' => 'boolean',
        'expected_delivery_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'uuid' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            // Ensure a UUID is set for the order
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }

            // Generate unique order number like: ORD-A3F9-2B7E
            do {
                $orderNumber = 'ORD-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
            } while (self::where('order_number', $orderNumber)->exists());

            $order->order_number = $orderNumber;
        });

        // Defensive cleanup: if Easypay is disabled ensure no payloads/sessions are left around
        static::created(function (Order $order) {
            if (! config('easypay.enabled', false)) {
                \App\Models\EasypayPayload::where('order_id', $order->id)->delete();
                \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->delete();
                \App\Models\EasypayPayment::where('order_id', $order->id)->delete();
            }
        });
    }

    /**
     * Use UUID as route model binding key so orders are addressed by UUIDs in URLs
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /* =======================
     * Relationships
     * ======================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Easypay (1:1) payload for this order
     */
    public function easypayPayload()
    {
        return $this->hasOne(\App\Models\EasypayPayload::class);
    }

    /**
     * Easypay checkout sessions (1:N)
     */
    public function easypayCheckoutSessions()
    {
        return $this->hasMany(\App\Models\EasypayCheckoutSession::class);
    }

    /**
     * Easypay payments (1:N)
     */
    public function easypayPayments()
    {
        return $this->hasMany(\App\Models\EasypayPayment::class);
    }

    /**
     * Idempotent: mark this order as paid. Use when authoritative payment confirmation
     * is available (e.g. Easypay single-payment returned `payment_status === 'paid'`).
     *
     * @param  string|null  $source  source of the confirmation (for logging)
     * @param  array  $meta  optional metadata (payment_id, raw_response, actor)
     * @return bool true when transition occurred, false when already paid
     */
    public function markAsPaid(?string $source = null, array $meta = []): bool
    {
        if ($this->is_paid) {
            return false;
        }

        $this->is_paid = true;
        $this->status = 'PROCESSING';
        $this->save();

        try {
            \Log::info('order.marked_paid', array_merge(['order_id' => $this->id, 'source' => $source], $meta));
        } catch (\Throwable $e) {
            // do not break flow if logging fails
        }

        // Send transactional email to customer when the payment source is authoritative (Easypay)
        // Keep this quiet on failures so payment processing is not impacted.
        if ($source === 'easypay' && $this->user && filter_var($this->user->email ?? '', FILTER_VALIDATE_EMAIL)) {
            try {
                $locale = $this->user->language ?? app()->getLocale();

                $statusObj = \App\Models\OrderStatus::where('code', $this->status)->first();
                $statusLabel = $statusObj?->translation($locale)?->name ?? $this->status;

                \Illuminate\Support\Facades\Mail::to($this->user->email)
                    ->locale($locale)
                    ->queue(new \App\Mail\OrderNotification(
                        $this,
                        'orders.email.event.paid',
                        $this->user->name,
                        $statusLabel,
                        ['status' => $statusLabel]
                    ));
            } catch (\Throwable $e) {
                \Log::warning('order.payment_email_failed', ['order_id' => $this->id, 'err' => $e->getMessage()]);
            }
        }

        return true;
    }

    /**
     * Administrative/manual marking. Kept as a separate helper to make intent explicit
     * and to allow adding audit hooks for manual interventions later.
     */
    public function markAsPaidManually(?int $adminUserId = null): bool
    {
        $ok = $this->markAsPaid('admin', ['admin_id' => $adminUserId]);
        if ($ok) {
            try {
                \Log::warning('order.marked_paid_manual', ['order_id' => $this->id, 'admin_id' => $adminUserId]);
            } catch (\Throwable $e) { /* ignore */
            }
        }

        return $ok;
    }

    /**
     * Idempotent: mark this order as refunded. Use when an authoritative refund
     * confirmation is available (e.g. Easypay webhook). When the $source is
     * authoritative we send the transactional email to the customer.
     *
     * @param  string|null  $source
     * @param  array  $meta
     * @return bool
     */
    public function markAsRefunded(?string $source = null, array $meta = []): bool
    {
        if ($this->is_refunded) {
            return false;
        }

        $prevStatus = $this->status;

        // If currently PROCESSING, transition to CANCELED and set the cancellation flag.
        // Otherwise keep the current status unchanged (per requirements).
        if ($prevStatus === 'PROCESSING') {
            $this->status = 'CANCELED';
            $this->is_canceled = true;

            // Restore stock for non-backordered items (mirror admin behaviour)
            try {
                foreach ($this->items()->with(['product', 'orderItemOptions.productOption.optionType'])->get() as $item) {
                    if ($item->was_backordered) {
                        continue;
                    }
                    $stockOpt = $item->orderItemOptions
                        ->filter(fn ($oi) => $oi->productOption && $oi->productOption->optionType?->have_stock)
                        ->first();
                    if ($stockOpt && $stockOpt->productOption) {
                        $stockOpt->productOption->increment('stock', $item->quantity);
                    } elseif ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal: do not prevent refund marking if stock restore fails
                \Log::warning('order.refund_stock_restore_failed', ['order_id' => $this->id, 'err' => $e->getMessage()]);
            }
        }

        $this->is_refunded = true;
        $this->save();

        try {
            \Log::info('order.marked_refunded', array_merge(['order_id' => $this->id, 'source' => $source, 'prev_status' => $prevStatus, 'status' => $this->status], $meta));
        } catch (\Throwable $e) {
            // do not break flow if logging fails
        }

        // Send transactional email to customer for authoritative sources (Easypay webhook)
        if ($source === 'easypay' && $this->user && filter_var($this->user->email ?? '', FILTER_VALIDATE_EMAIL)) {
            try {
                $locale = $this->user->language ?? app()->getLocale();

                // Use a friendly label for the "refunded" status in the mail's status block
                $statusLabel = t('orders.refunded') ?: 'Refunded';

                \Illuminate\Support\Facades\Mail::to($this->user->email)
                    ->locale($locale)
                    ->queue(new \App\Mail\OrderNotification(
                        $this,
                        'orders.email.event.refunded',
                        $this->user->name,
                        $statusLabel,
                        ['status' => $statusLabel]
                    ));
            } catch (\Throwable $e) {
                \Log::warning('order.refund_email_failed', ['order_id' => $this->id, 'err' => $e->getMessage()]);
            }
        }

        return true;
    }
}
