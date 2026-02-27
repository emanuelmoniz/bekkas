<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [];

    public function translations()
    {
        return $this->hasMany(MaterialTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->where('locale', $locale)->first();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
