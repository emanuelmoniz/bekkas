<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'production_date',
        'execution_time',
        'width',
        'length',
        'height',
        'weight',
        'is_active',
        'is_featured',
        'client',
        'client_url',
    ];

    protected $casts = [
        'production_date' => 'date',
        'execution_time' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'uuid' => 'string',
    ];

    /**
     * Auto-generate a UUID on create; clean up image files on delete.
     */
    protected static function booted()
    {
        static::creating(function ($project) {
            if (empty($project->uuid)) {
                $project->uuid = (string) Str::uuid();
            }
        });

        static::deleting(function (Project $project) {
            $thumbnails = app(\App\Services\ImageThumbnailService::class);
            foreach ($project->photos as $photo) {
                $thumbnails->delete($photo->path, $photo->original_path);
            }
        });
    }

    /**
     * Use uuid for route model binding.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

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

}
