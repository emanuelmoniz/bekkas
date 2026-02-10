<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingConfig extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a config value by key
     */
    public static function get(string $key, $default = null)
    {
        $config = static::where('key', $key)->first();

        return $config ? $config->value : $default;
    }

    /**
     * Set a config value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
