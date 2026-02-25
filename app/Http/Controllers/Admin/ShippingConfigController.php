<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use Illuminate\Http\Request;

class ShippingConfigController extends Controller
{
    public function index()
    {
        $freeShippingOver = ShippingConfig::get('free_shipping_over', 0);
        $defaultShippingTierId = ShippingConfig::get('default_shipping_tier_id');
        $shippingTiers = ShippingTier::where('active', true)->with('translations')->orderBy('shipping_days')->get();
        $trackingStatuses = json_decode(ShippingConfig::get('tracking_statuses', '["shipped","delivered"]'), true);
        $allStatuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        return view('admin.shipping-config.index', compact(
            'freeShippingOver',
            'defaultShippingTierId',
            'shippingTiers',
            'trackingStatuses',
            'allStatuses',
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'free_shipping_over' => 'required|numeric|min:0',
            'default_shipping_tier_id' => 'nullable|exists:shipping_tiers,id',
            'tracking_statuses' => 'array',
            'tracking_statuses.*' => 'string',
        ]);

        ShippingConfig::set('free_shipping_over', $request->free_shipping_over);
        ShippingConfig::set('default_shipping_tier_id', $request->default_shipping_tier_id);
        ShippingConfig::set('tracking_statuses', json_encode($request->tracking_statuses ?? []));

        return redirect()->route('admin.shipping-config.index')
            ->with('success', 'Shipping configuration updated successfully.');
    }
}
