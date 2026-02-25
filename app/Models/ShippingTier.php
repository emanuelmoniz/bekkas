<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingTier extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function translations()
    {
        return $this->hasMany(ShippingTierTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

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
