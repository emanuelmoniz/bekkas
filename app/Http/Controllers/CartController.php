<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShippingTier;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $products = Product::whereIn('id', array_keys($cart))
            ->where('active', true)
            ->get();

        $items = [];
        $totalWeight = 0;

        $productsGross = 0;
        $productsTax = 0;

        foreach ($products as $product) {
            $qty = $cart[$product->id];
            $unitGross = $product->promo_price ?? $product->price;

            // ✅ EXPLICIT RELATION ACCESS (avoids "tax" column collision)
            $taxPct = $product->tax()->first()->percentage;

            $lineGross = $unitGross * $qty;
            $lineNet = $lineGross / (1 + $taxPct / 100);
            $lineTax = $lineGross - $lineNet;

            $items[] = [
                'product' => $product,
                'quantity' => $qty,
                'unit_gross' => $unitGross,
                'line_gross' => round($lineGross, 2),
                'line_tax' => round($lineTax, 2),
            ];

            $productsGross += $lineGross;
            $productsTax += $lineTax;

            $totalWeight += ($product->weight * $qty);
        }

        $shipping = $this->calculateShipping($totalWeight);

        return view('cart.index', [
            'items' => $items,
            'totalWeight' => $totalWeight,

            'productsGross' => round($productsGross, 2),
            'productsTax' => round($productsTax, 2),

            'shipping' => $shipping,

            'totalGross' => round($productsGross + $shipping['gross'], 2),
            'totalTax' => round($productsTax + $shipping['tax'], 2),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if (! $product->active) {
            abort(404);
        }

        $cart = session()->get('cart', []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $request->quantity;

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = session()->get('cart', []);
        $cart[$product->id] = $request->quantity;

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    /**
     * Correct weight-based, stackable shipping calculation
     */
    private function calculateShipping(int $totalWeight): array
    {
        if ($totalWeight <= 0) {
            return ['net' => 0, 'tax' => 0, 'gross' => 0];
        }

        $tiers = ShippingTier::where('active', true)
            ->with('tax')
            ->orderBy('weight_to')
            ->get();

        $remaining = $totalWeight;
        $gross = 0;
        $net = 0;
        $tax = 0;

        while ($remaining > 0) {
            $tier = $tiers->first(fn ($t) => $remaining <= $t->weight_to)
                ?? $tiers->last();

            $tierGross = $tier->cost_gross;
            $tierTaxPct = $tier->tax->percentage;

            $tierNet = $tierGross / (1 + $tierTaxPct / 100);
            $tierTax = $tierGross - $tierNet;

            $gross += $tierGross;
            $net += $tierNet;
            $tax += $tierTax;

            $remaining -= $tier->weight_to;
        }

        return [
            'gross' => round($gross, 2),
            'net'   => round($net, 2),
            'tax'   => round($tax, 2),
        ];
    }
}
