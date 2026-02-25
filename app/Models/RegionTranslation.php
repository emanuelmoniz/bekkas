<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'region_id',
        'locale',
        'name',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
