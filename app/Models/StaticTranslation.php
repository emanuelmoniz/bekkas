<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticTranslation extends Model
{
    protected $fillable = [
        'key',
        'locale',
        'value',
    ];

    public static function forKeyLocale(string $key, string $locale)
    {
        return static::where('key', $key)->where('locale', $locale)->first();
    }
}
