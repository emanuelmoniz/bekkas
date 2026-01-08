<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingTier extends Model
{
    protected $fillable = [
        'name_pt',
        'name_en',
        'weight_from',
        'weight_to',
        'cost_gross',
        'shipping_days',
        'tax_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_shipping_tier');
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class, 'region_shipping_tier');
    }
}
