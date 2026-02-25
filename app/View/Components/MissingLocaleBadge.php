<?php

namespace App\View\Components;

use App\Models\Locale;
use Illuminate\View\Component;

/**
 * Displays a small warning badge when a model does not have translations for
 * all active locales.
 *
 * Usage:  <x-missing-locale-badge :model="$product" />
 *
 * The model is expected to implement a `translations` relationship whose items
 * each have a `locale` attribute (the standard <Model>Translation pattern).
 */
class MissingLocaleBadge extends Component
{
    /** @var \Illuminate\Support\Collection  Locale codes that are missing */
    public $missingLocales;

    public function __construct(public $model)
    {
        $activeCodes = Locale::activeCodes();

        $haveCodes = collect();
        if ($model && method_exists($model, 'getRelationValue')) {
            // Use already-loaded relation to avoid extra queries
            $relation = $model->getRelationValue('translations');
            if ($relation !== null) {
                $haveCodes = $relation->pluck('locale');
            } elseif ($model->relationLoaded('translations')) {
                $haveCodes = $model->translations->pluck('locale');
            }
        }

        $this->missingLocales = $activeCodes->diff($haveCodes)->values();
    }

    public function hasMissing(): bool
    {
        return $this->missingLocales->isNotEmpty();
    }

    public function render()
    {
        return view('components.missing-locale-badge');
    }
}
