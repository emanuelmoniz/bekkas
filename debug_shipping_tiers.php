<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShippingTier;

echo "=== SHIPPING TIERS ===\n\n";

$tiers = ShippingTier::with('regions')->get();

foreach ($tiers as $tier) {
    echo "Tier ID: {$tier->id}\n";
    echo "Name: {$tier->name_pt} / {$tier->name_en}\n";
    echo "Weight: {$tier->weight_from} - {$tier->weight_to} g\n";
    echo "Shipping Days: {$tier->shipping_days}\n";
    echo "Cost: €{$tier->cost_gross}\n";
    echo "Active: " . ($tier->active ? 'Yes' : 'No') . "\n";
    
    if ($tier->regions->count() > 0) {
        echo "Regions:\n";
        foreach ($tier->regions as $region) {
            echo "  - {$region->name}: {$region->postal_code_from} to {$region->postal_code_to} (Active: " . ($region->is_active ? 'Yes' : 'No') . ")\n";
        }
    } else {
        echo "Regions: None configured\n";
    }
    
    echo "\n" . str_repeat('-', 60) . "\n\n";
}

echo "\n=== TEST POSTAL CODE 9950-322 ===\n\n";

$postalCode = '9950-322';
$weight = 150;

$matchingTier = ShippingTier::where('active', true)
    ->where('weight_from', '<=', $weight)
    ->where('weight_to', '>=', $weight)
    ->whereHas('regions', function ($query) use ($postalCode) {
        $query->where('postal_code_from', '<=', $postalCode)
              ->where('postal_code_to', '>=', $postalCode);
    })
    ->orderBy('shipping_days', 'asc')
    ->with('regions')
    ->first();

if ($matchingTier) {
    echo "✅ Found matching tier: {$matchingTier->name_pt} (ID: {$matchingTier->id})\n";
    echo "Shipping Days: {$matchingTier->shipping_days}\n";
} else {
    echo "❌ No tier found for postal code {$postalCode} with weight {$weight}g\n";
    echo "\nFalling back to weight-only lookup...\n";
    
    $weightOnlyTier = ShippingTier::where('active', true)
        ->where('weight_from', '<=', $weight)
        ->where('weight_to', '>=', $weight)
        ->orderBy('shipping_days', 'asc')
        ->first();
    
    if ($weightOnlyTier) {
        echo "✅ Weight-only tier: {$weightOnlyTier->name_pt} (ID: {$weightOnlyTier->id})\n";
        echo "Shipping Days: {$weightOnlyTier->shipping_days}\n";
    }
}
