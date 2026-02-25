<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionType extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'is_active', 'have_stock', 'have_price'];

    protected $casts = [
        'is_active'  => 'boolean',
        'have_stock' => 'boolean',
        'have_price' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductOptionTypeTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }
}
