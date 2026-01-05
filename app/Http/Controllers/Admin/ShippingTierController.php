<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingTier;
use Illuminate\Http\Request;

class ShippingTierController extends Controller
{
    public function index()
    {
        $tiers = ShippingTier::orderBy('weight_from')->get();
        return view('admin.shipping-tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.shipping-tiers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'weight_from' => 'required|integer|min:0',
            'weight_to' => 'required|integer|min:1',
            'cost_gross' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0',
        ]);

        ShippingTier::create($request->all());

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function edit(ShippingTier $shippingTier)
    {
        return view('admin.shipping-tiers.edit', compact('shippingTier'));
    }

    public function update(Request $request, ShippingTier $shippingTier)
    {
        $request->validate([
            'weight_from' => 'required|integer|min:0',
            'weight_to' => 'required|integer|min:1',
            'cost_gross' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);

        $shippingTier->update([
            ...$request->all(),
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function destroy(ShippingTier $shippingTier)
    {
        $shippingTier->delete();
        return redirect()->route('admin.shipping-tiers.index');
    }
}
