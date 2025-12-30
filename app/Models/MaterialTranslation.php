<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'locale',
        'name',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
