<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionTypeTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_option_type_id', 'locale', 'name', 'description'];

    public function productOptionType()
    {
        return $this->belongsTo(ProductOptionType::class);
    }
}
