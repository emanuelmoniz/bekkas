<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_option_id', 'locale', 'name', 'description'];

    public function option()
    {
        return $this->belongsTo(ProductOption::class);
    }
}
