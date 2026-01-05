<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingTier;
use App\Models\Tax;
use Illuminate\Http\Request;

class ShippingTierController extends Controller
{
    public function index()
    {
        $tiers = ShippingTier::with('tax')
            ->orderBy('weight_from')
            ->get();

        return view('admin.shipping-tiers.index', compact('tiers'));
    }

    public function create()
    {
        $taxes = Tax::where('is_active', true)
            ->orderBy('percentage')
            ->get();

        return view('admin.shipping-tiers.create', compact('taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'weight_from' => 'required|integer|min:0',
            'weight_to'   => 'required|integer|min:1|gt:weight_from',
            'cost_gross'  => 'required|numeric|min:0',
            'tax_id'      => 'required|exists:taxes,id',
        ]);

        ShippingTier::create([
            'weight_from' => $request->weight_from,
            'weight_to'   => $request->weight_to,
            'cost_gross'  => $request->cost_gross,
            'tax_id'      => $request->tax_id,
            'active'      => $request->boolean('active'),
        ]);

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function edit(ShippingTier $shippingTier)
    {
        $taxes = Tax::where('is_active', true)
            ->orderBy('percentage')
            ->get();

        return view('admin.shipping-tiers.edit', compact(
            'shippingTier',
            'taxes'
        ));
    }

    public function update(Request $request, ShippingTier $shippingTier)
    {
        $request->validate([
            'weight_from' => 'required|integer|min:0',
            'weight_to'   => 'required|integer|min:1|gt:weight_from',
            'cost_gross'  => 'required|numeric|min:0',
            'tax_id'      => 'required|exists:taxes,id',
        ]);

        $shippingTier->update([
            'weight_from' => $request->weight_from,
            'weight_to'   => $request->weight_to,
            'cost_gross'  => $request->cost_gross,
            'tax_id'      => $request->tax_id,
            'active'      => $request->boolean('active'),
        ]);

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function destroy(ShippingTier $shippingTier)
    {
        $shippingTier->delete();

        return redirect()->route('admin.shipping-tiers.index');
    }
}
