<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $fillable = ['product_option_type_id', 'is_active', 'stock'];

    protected $casts = [
        'is_active' => 'boolean',
        'stock' => 'integer',
    ];

    public function optionType()
    {
        return $this->belongsTo(ProductOptionType::class, 'product_option_type_id');
    }

    public function translations()
    {
        return $this->hasMany(ProductOptionTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }
}
