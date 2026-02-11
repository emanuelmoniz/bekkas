<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EasypayPayment extends Model
{
    protected $table = 'easypay_payments';

    protected $fillable = [
        'payment_id',
        'capture_id',
        'refund_id',
        'checkout_id',
        'order_id',
        'payment_status',
        'paid_at',
        'payment_method',
        'card_type',
        'card_last_digits',
        'mb_entity',
        'mb_reference',
        'mb_expiration_time',
        'iban',
        'raw_response',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'mb_expiration_time' => 'datetime',
        'raw_response' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function checkoutSession(): BelongsTo
    {
        return $this->belongsTo(EasypayCheckoutSession::class, 'checkout_id', 'checkout_id');
    }
}
