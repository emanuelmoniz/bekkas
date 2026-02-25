<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_option_id',
        'option_type_name',
        'option_name',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }
}
