<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
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

    public function translations()
    {
        return $this->hasMany(RegionTranslation::class);
    }

    public function translation(?string $locale = null): ?RegionTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

    /** Convenience accessor: $region->name returns the current-locale name. */
    public function getNameAttribute(): ?string
    {
        return $this->translation()?->name;
    }

    public function getDefaultShippingTier()
    {
        return ShippingTier::whereHas('regions', function ($query) {
            $query->where('regions.id', $this->id)
                ->where('region_shipping_tier.is_default', true);
        })->first();
    }
}
