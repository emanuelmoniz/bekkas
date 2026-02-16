<?php

namespace App\Services;

use App\Models\ShippingTier;

class ShippingCalculator
{
    /**
     * Calculate shipping cost based on total weight
     *
     * @param  float  $weight  Total weight in kg
     * @return array ['gross' => float, 'net' => float, 'tax' => float]
     */
    public static function calculate(float $weight): array
    {
        if ($weight <= 0) {
            return ['gross' => 0, 'tax' => 0, 'net' => 0];
        }

        // Find the appropriate shipping tier using actual column names
        $tier = ShippingTier::where('active', true)
            ->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight)
            ->with('tax')
            ->first();

        // Fallback: use most expensive tier if weight exceeds all defined tiers
        if (! $tier) {
            $tier = ShippingTier::where('active', true)
                ->orderBy('cost_gross', 'desc')
                ->with('tax')
                ->first();
        }

        // If no tier exists at all, return zero shipping
        if (! $tier) {
            return ['gross' => 0, 'tax' => 0, 'net' => 0];
        }

        return self::calculateFromTier($tier);
    }

    /**
     * Calculate shipping amounts from a tier
     *
     * @return array ['gross' => float, 'net' => float, 'tax' => float]
     */
    private static function calculateFromTier(ShippingTier $tier): array
    {
        $gross = $tier->cost_gross;

        // Respect global tax feature toggle
        $taxEnabled = (bool) config('app.tax_enabled', env('APP_TAX_ENABLED', true));

        // Safe tax retrieval (compatible with older PHP versions)
        $taxPct = $taxEnabled ? (optional($tier->tax)->percentage ?? 0) : 0;

        // Avoid division by zero; when taxes are disabled net == gross and tax == 0
        $net = $taxPct > 0
            ? $gross / (1 + $taxPct / 100)
            : $gross;

        $tax = $taxPct > 0 ? $gross - $net : 0;

        return [
            'gross' => round($gross, 2),
            'net' => round($net, 2),
            'tax' => round($tax, 2),
        ];
    }
}
