<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'was_backordered',
        'unit_price_gross',
        'tax_percentage',
        'unit_weight',
        'total_net',
        'total_tax',
        'total_gross',
    ];

    protected $casts = [
        'was_backordered' => 'boolean',
        'unit_price_gross' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'unit_weight' => 'decimal:3',
        'total_net' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_gross' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItemOptions()
    {
        return $this->hasMany(OrderItemOption::class);
    }
}
