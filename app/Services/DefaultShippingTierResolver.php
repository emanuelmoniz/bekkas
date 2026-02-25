<?php

namespace App\Services;

use App\Models\Region;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use Illuminate\Support\Facades\DB;

class DefaultShippingTierResolver
{
    /**
     * Get the default shipping tier for a postal code and weight
     *
     * @param  int  $weight  Weight in grams
     */
    public static function resolve(string $postalCode, int $weight = 0): ?ShippingTier
    {
        // Find region by postal code — prefer the most specific (narrowest) range
        $region = Region::where('is_active', true)
            ->where('postal_code_from', '<=', $postalCode)
            ->where('postal_code_to', '>=', $postalCode)
            ->orderByRaw('(postal_code_to - postal_code_from) ASC')
            ->first();

        if ($region) {
            // Try to get region-specific default shipping tier (even if inactive - for free shipping)
            $defaultTier = DB::table('region_default_shipping_tiers')
                ->where('region_id', $region->id)
                ->join('shipping_tiers', 'region_default_shipping_tiers.shipping_tier_id', '=', 'shipping_tiers.id')
                ->select('shipping_tiers.*')
                ->first();

            if ($defaultTier) {
                // Always respect region configuration, regardless of active status
                return ShippingTier::find($defaultTier->id);
            }
        }

        // Fallback to global default shipping tier
        $defaultTierId = ShippingConfig::get('default_shipping_tier_id');
        if ($defaultTierId) {
            $tier = ShippingTier::find($defaultTierId);

            // Verify weight if specified
            if ($tier && $weight > 0) {
                if ($tier->weight_from <= $weight && $tier->weight_to >= $weight) {
                    return $tier;
                }

                // Weight doesn't match, find any tier matching weight
                return ShippingTier::where('active', true)
                    ->where('weight_from', '<=', $weight)
                    ->where('weight_to', '>=', $weight)
                    ->orderBy('shipping_days', 'asc')
                    ->first();
            }

            return $tier;
        }

        return null;
    }

    /**
     * Find a shipping tier for a specific region and weight
     */
    private static function findTierForRegionAndWeight(Region $region, int $weight): ?ShippingTier
    {
        // Get tiers for this region matching weight
        $tier = ShippingTier::where('active', true)
            ->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight)
            ->whereHas('regions', function ($query) use ($region) {
                $query->where('regions.id', $region->id);
            })
            ->orderBy('shipping_days', 'asc')
            ->first();

        if ($tier) {
            return $tier;
        }

        // Fallback to any tier matching weight
        return ShippingTier::where('active', true)
            ->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight)
            ->orderBy('shipping_days', 'asc')
            ->first();
    }
}
