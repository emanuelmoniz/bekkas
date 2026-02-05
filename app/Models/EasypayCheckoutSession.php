<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EasypayCheckoutSession extends Model
{
    protected $table = 'easypay_checkout_sessions';

    protected $fillable = [
        'order_id',
        'checkout_id',
        'session_id',
        'is_active',
        'payload_id',
        'in_error',
        'error_code',
        'status',
        'message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'in_error' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payload(): BelongsTo
    {
        return $this->belongsTo(EasypayPayload::class, 'payload_id');
    }
}
