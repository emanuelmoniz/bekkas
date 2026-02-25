<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'iso_alpha2',
        'country_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function translations()
    {
        return $this->hasMany(CountryTranslation::class);
    }

    public function translation(?string $locale = null): ?CountryTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

    /** Convenience accessor: $country->name returns the current-locale name. */
    public function getNameAttribute(): ?string
    {
        return $this->translation()?->name;
    }

    /**
     * Order by the translated name for the given locale,
     * falling back to the app fallback locale.
     */
    public function scopeOrderByTranslatedName(Builder $query, ?string $locale = null): Builder
    {
        $locale   = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en-UK');

        return $query
            ->leftJoin('country_translations as _ct_order', function ($join) use ($locale) {
                $join->on('_ct_order.country_id', '=', 'countries.id')
                     ->where('_ct_order.locale', '=', $locale);
            })
            ->leftJoin('country_translations as _ct_fallback', function ($join) use ($fallback) {
                $join->on('_ct_fallback.country_id', '=', 'countries.id')
                     ->where('_ct_fallback.locale', '=', $fallback);
            })
            ->orderByRaw('COALESCE(_ct_order.name, _ct_fallback.name)')
            ->addSelect('countries.*');
    }
}
