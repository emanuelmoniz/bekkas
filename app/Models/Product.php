<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_id',        // ✅ REQUIRED
        'price',
        'promo_price',
        'stock',
        'production_time',
        'is_backorder',
        'is_featured',
        'is_promo',
        'active',
        'width',
        'length',
        'height',
        'weight',
    ];

    /**
     * Use UUIDs for public URL generation and route model binding.
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_promo' => 'boolean',
        'is_backorder' => 'boolean',
        'active' => 'boolean',
        'price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'width' => 'decimal:2',
        'length' => 'decimal:2',
        'height' => 'decimal:2',
        'uuid' => 'string',
    ];

    /**
     * Ensure a UUID is set when creating new products.
     */
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Tell Laravel to use the `uuid` column for route model binding
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Ensure `$product->tax` returns a model-like object even if a
     * legacy `tax` attribute (string/percentage) exists in the row.
     * This preserves backward compatibility where the database stores
     * a denormalized `tax` column alongside the `tax_id` foreign key.
     */
    public function getTaxAttribute($value)
    {
        // If a real relation exists or can be loaded, return it
        $relation = $this->getRelationValue('tax');
        if ($relation instanceof \Illuminate\Database\Eloquent\Model) {
            return $relation;
        }

        // If row contains a denormalized tax percentage (string/number),
        // return a small object that mimics the Tax model interface used
        // by the rest of the app (`->percentage`, `->id`, `->is_active`).
        if (! is_null($value) && $value !== '') {
            $obj = new \stdClass;
            $obj->id = $this->tax_id ?? null;
            $obj->percentage = is_numeric($value) ? (float) $value : null;
            $obj->is_active = null;

            return $obj;
        }

        return null;
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

    /**
     * Option types attached to this product (e.g. colour, size, etc).
     * A single product can have zero or more types, and each type in
     * turn owns multiple concrete options.
     */
    public function optionTypes()
    {
        return $this->hasMany(ProductOptionType::class);
    }
}
