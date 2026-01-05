<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'tax_id',        // ✅ REQUIRED
        'price',
        'promo_price',
        'stock',
        'is_new',
        'is_promo',
        'active',
        'width',
        'length',
        'height',
        'weight',
    ];

    protected $casts = [
        'is_new'   => 'boolean',
        'is_promo' => 'boolean',
        'active'   => 'boolean',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class);
    }

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function primaryPhoto()
    {
        return $this->hasOne(ProductPhoto::class)->where('is_primary', true);
    }
}
