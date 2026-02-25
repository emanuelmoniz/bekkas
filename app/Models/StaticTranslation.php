<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticTranslation extends Model
{
    protected $fillable = [
        'key',
        'locale',
        'context',
        'value',
    ];

    /**
     * The logical group this key belongs to — derived from the first dot-segment of the key.
     * E.g. "nav.shop" → "nav", "checkout.pay.success" → "checkout".
     */
    public function getGroupAttribute(): string
    {
        return explode('.', $this->key)[0];
    }

    public static function forKeyLocale(string $key, string $locale)
    {
        return static::where('key', $key)->where('locale', $locale)->first();
    }
}
