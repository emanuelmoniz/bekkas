<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;

    // both `description` and the newly-added `technical_info` are stored per-locale
    protected $fillable = ['product_id', 'locale', 'name', 'description', 'technical_info'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
