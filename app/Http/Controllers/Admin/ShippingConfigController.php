<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingConfig;
use Illuminate\Http\Request;

class ShippingConfigController extends Controller
{
    public function index()
    {
        $freeShippingOver = ShippingConfig::get('free_shipping_over', 0);
        $defaultShippingTierId = ShippingConfig::get('default_shipping_tier_id');
        $shippingTiers = \App\Models\ShippingTier::orderBy('name_en')->get();
        
        return view('admin.shipping-config.index', compact('freeShippingOver', 'defaultShippingTierId', 'shippingTiers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'free_shipping_over' => 'required|numeric|min:0',
            'default_shipping_tier_id' => 'nullable|exists:shipping_tiers,id',
        ]);

        ShippingConfig::set('free_shipping_over', $request->free_shipping_over);
        ShippingConfig::set('default_shipping_tier_id', $request->default_shipping_tier_id);

        return redirect()->route('admin.shipping-config.index')
            ->with('success', 'Shipping configuration updated successfully.');
    }
}
