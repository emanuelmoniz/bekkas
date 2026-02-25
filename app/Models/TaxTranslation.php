<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tax_id',
        'locale',
        'name',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
