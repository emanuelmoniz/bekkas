<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected ?string $legacyNamePt = null;

    protected ?string $legacyNameEn = null;

    protected $fillable = [
        'iso_alpha2',
        'country_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function (Country $country) {
            if ($country->legacyNamePt !== null) {
                CountryTranslation::updateOrCreate(
                    ['country_id' => $country->id, 'locale' => 'pt-PT'],
                    ['name' => $country->legacyNamePt]
                );
            }

            if ($country->legacyNameEn !== null) {
                CountryTranslation::updateOrCreate(
                    ['country_id' => $country->id, 'locale' => 'en-UK'],
                    ['name' => $country->legacyNameEn]
                );
            }
        });
    }

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

    public function getNamePtAttribute(): ?string
    {
        return $this->translations->where('locale', 'pt-PT')->first()?->name;
    }

    public function getNameEnAttribute(): ?string
    {
        return $this->translations->where('locale', 'en-UK')->first()?->name;
    }

    public function setNamePtAttribute($value): void
    {
        $this->legacyNamePt = is_string($value) ? $value : null;
        unset($this->attributes['name_pt']);
    }

    public function setNameEnAttribute($value): void
    {
        $this->legacyNameEn = is_string($value) ? $value : null;
        unset($this->attributes['name_en']);
    }

    /**
     * Order by the translated name for the given locale,
     * falling back to the app fallback locale.
     */
    public function scopeOrderByTranslatedName(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();
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
