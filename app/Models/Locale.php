<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $primaryKey = 'code';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'code',
        'name',
        'flag_emoji',
        'country_id',
        'is_active',
        'is_default',
    ];

    /*************************************************************************
     * Scopes
     *************************************************************************/

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /*************************************************************************
     * Helpers
     *************************************************************************/

    /** Returns [code => name] array for form selects. */
    public static function activeList(): array
    {
        return self::active()->orderBy('name')->pluck('name', 'code')->toArray();
    }

    /** Returns a collection of active locale code strings. */
    public static function activeCodes()
    {
        return self::active()->pluck('code');
    }

    /*************************************************************************
     * Relationships
     *************************************************************************/

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
