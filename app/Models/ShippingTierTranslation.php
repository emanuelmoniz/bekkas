<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingTierTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'shipping_tier_id',
        'locale',
        'name',
    ];

    public function shippingTier()
    {
        return $this->belongsTo(ShippingTier::class);
    }
}
