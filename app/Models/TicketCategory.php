<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(TicketCategoryTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
