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

    public function payments()
    {
        return $this->hasMany(EasypayPayment::class, 'checkout_id', 'checkout_id');
    }

    /**
     * Return a canonical SDK manifest derived from authoritative DB fields.
     *
     * The SDK expects a manifest shaped like:
     *  [ 'id' => <checkout_id>, 'session' => <session_id>, 'config' => null ]
     *
     * IMPORTANT: do NOT build the manifest from the stored `message` field
     * — the message payload may include extra keys or different shapes. Use
     * DB columns (`checkout_id`, `session_id`) as the single source of truth.
     */
    public function toManifest(): ?array
    {
        if (empty($this->checkout_id) || empty($this->session_id)) {
            return null;
        }

        return [
            'id' => (string) $this->checkout_id,
            'session' => (string) $this->session_id,
            'config' => null,
        ];
    }

    /**
     * Blade-friendly accessor: `$session->manifest`.
     */
    public function getManifestAttribute(): ?array
    {
        return $this->toManifest();
    }
}
