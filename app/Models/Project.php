<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_date',
        'execution_time',
        'width',
        'length',
        'height',
        'weight',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'production_date' => 'date',
        'execution_time' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(ProjectTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class);
    }

    public function photos()
    {
        return $this->hasMany(ProjectPhoto::class);
    }

    public function primaryPhoto()
    {
        return $this->hasOne(ProjectPhoto::class)->where('is_primary', true);
    }

    /**
     * When a project is deleted we need to remove any associated
     * image files (thumbnail + original) from storage.  The HTTP
     * controllers already call the thumbnail service when individual
     * photos are removed, but the model event guarantees cleanup
     * regardless of how the project is deleted (artisanal scripts,
     * factories, seeds, etc.).
     */
    protected static function booted()
    {
        static::deleting(function (Project $project) {
            $thumbnails = app(\App\Services\ImageThumbnailService::class);
            foreach ($project->photos as $photo) {
                $thumbnails->delete($photo->path, $photo->original_path);
            }
        });
    }
}
