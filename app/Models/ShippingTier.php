<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingTier extends Model
{
    protected $fillable = [
        'weight_from',
        'weight_to',
        'cost_gross',
        'tax_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
