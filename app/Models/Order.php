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
        'expected_delivery_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            // Generate unique order number like: ORD-A3F9-2B7E
            do {
                $orderNumber = 'ORD-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            } while (self::where('order_number', $orderNumber)->exists());
            
            $order->order_number = $orderNumber;
        });
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
}
