<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'postal_code_from',
        'postal_code_to',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function defaultShippingTier()
    {
        return $this->belongsTo(ShippingTier::class, 'id', 'region_id')
            ->join('region_default_shipping_tiers', 'shipping_tiers.id', '=', 'region_default_shipping_tiers.shipping_tier_id')
            ->where('region_default_shipping_tiers.region_id', $this->id);
    }

    public function getDefaultShippingTier()
    {
        return ShippingTier::whereHas('defaultForRegions', function ($query) {
            $query->where('region_id', $this->id);
        })->first();
    }
}
