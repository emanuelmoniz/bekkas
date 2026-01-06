<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'is_paid',
        'is_canceled',
        'is_refunded',
        'tracking_number',

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
