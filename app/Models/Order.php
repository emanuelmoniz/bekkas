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
