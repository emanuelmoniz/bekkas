<?php

namespace App\Services;

use App\Models\ShippingTier;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DeliveryDateCalculator
{
    /**
     * Calculate the expected delivery date for a product
     *
     * @param Product $product
     * @return array ['date' => Carbon, 'formatted' => string, 'tier' => ShippingTier|null]
     */
    public function calculateDeliveryDate(Product $product): array
    {
        // If product has 0 stock and doesn't allow backorder, don't show delivery info
        if ($product->stock <= 0 && !$product->is_backorder) {
            return [
                'date' => null,
                'formatted' => null,
                'tier' => null,
            ];
        }
        
        // Get the appropriate shipping tier
        $tier = $this->getShippingTier($product);
        
        if (!$tier) {
            return [
                'date' => null,
                'formatted' => null,
                'tier' => null,
            ];
        }
        
        // Calculate total working days
        // Only add production days if product is in backorder AND has 0 stock
        $productionDays = ($product->stock <= 0 && $product->is_backorder) ? ($product->production_time ?? 0) : 0;
        $totalWorkingDays = $productionDays + ($tier->shipping_days ?? 0);
        
        // Calculate the delivery date
        $deliveryDate = $this->addWorkingDays(Carbon::now(), $totalWorkingDays);
        
        return [
            'date' => $deliveryDate,
            'formatted' => $deliveryDate->format('d/m/Y'),
            'tier' => $tier,
        ];
    }
    
    /**
     * Get the appropriate shipping tier for a product based on user state and weight
     *
     * @param Product $product
     * @return ShippingTier|null
     */
    protected function getShippingTier(Product $product): ?ShippingTier
    {
        $weight = $product->weight ?? 0;
        
        // Three-tier logic:
        // 1. If logged in with default address: use region-based default by postal code + weight
        // 2. If logged in without default address (or not logged in): use weight only, select lowest shipping days
        // 3. If no tiers found: use global default tier (even if inactive)
        
        if (Auth::check()) {
            $user = Auth::user();
            $defaultAddress = $user->addresses()->where('is_default', true)->first();
            
            if ($defaultAddress && $defaultAddress->postal_code) {
                // Tier 1: Use region-based default shipping tier
                $tier = DefaultShippingTierResolver::resolve($defaultAddress->postal_code, $weight);
                if ($tier) {
                    return $tier;
                }
            }
        }
        
        // Tier 2: Use weight only (for logged in without default address OR not logged in)
        $tier = $this->getTierByWeight($weight);
        if ($tier) {
            return $tier;
        }
        
        // Tier 3: Fallback to global default tier from config (even if inactive)
        $defaultTierId = \App\Models\ShippingConfig::get('default_shipping_tier_id');
        if ($defaultTierId) {
            return ShippingTier::find($defaultTierId);
        }
        
        return null;
    }
    
    /**
     * Get shipping tier by postal code and weight (active tiers only)
     *
     * @param string $postalCode
     * @param int $weight
     * @return ShippingTier|null
     */
    protected function getTierByPostalCodeAndWeight(string $postalCode, int $weight): ?ShippingTier
    {
        return ShippingTier::where('active', true)
            ->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight)
            ->whereHas('regions', function ($query) use ($postalCode) {
                $query->where('postal_code_from', '<=', $postalCode)
                      ->where('postal_code_to', '>=', $postalCode);
            })
            ->orderBy('shipping_days', 'asc')
            ->first();
    }
    
    /**
     * Get shipping tier by weight only (active tiers only)
     *
     * @param int $weight
     * @return ShippingTier|null
     */
    protected function getTierByWeight(int $weight): ?ShippingTier
    {
        return ShippingTier::where('active', true)
            ->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight)
            ->orderBy('shipping_days', 'asc')
            ->first();
    }
    
    /**
     * Add working days (excluding weekends) to a date
     *
     * @param Carbon $startDate
     * @param int $workingDays
     * @return Carbon
     */
    protected function addWorkingDays(Carbon $startDate, int $workingDays): Carbon
    {
        $date = $startDate->copy();
        $daysAdded = 0;
        
        while ($daysAdded < $workingDays) {
            $date->addDay();
            
            // Skip weekends (Saturday = 6, Sunday = 0)
            if (!$date->isWeekend()) {
                $daysAdded++;
            }
        }
        
        return $date;
    }
}
