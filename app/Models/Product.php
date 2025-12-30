<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Material;
use App\Models\ProductTranslation;
use App\Models\ProductPhoto;

class Product extends Model
{
    protected $fillable = [
        'is_new','is_promo','price','promo_price','tax',
        'width','length','height','weight','stock','active'
    ];

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
