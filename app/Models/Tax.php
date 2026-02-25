<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'percentage',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shippingTiers()
    {
        return $this->hasMany(ShippingTier::class);
    }

    public function translations()
    {
        return $this->hasMany(TaxTranslation::class);
    }

    public function translation(?string $locale = null): ?TaxTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

    /** Convenience accessor: $tax->name returns the current-locale name. */
    public function getNameAttribute(): ?string
    {
        return $this->translation()?->name;
    }
}
