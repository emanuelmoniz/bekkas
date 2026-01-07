<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ShippingCalculator;
use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $products = Product::whereIn('id', array_keys($cart))
            ->where('active', true)
            ->with(['tax'])
            ->get();

        $items = [];
        $totalWeight = 0;

        $productsGross = 0;
        $productsTax = 0;

        foreach ($products as $product) {
            $qty = $cart[$product->id];
            $unitGross = $product->promo_price ?? $product->price;

            // Safe tax retrieval (Laravel optional helper)
            $taxPct = optional($product->tax)->percentage ?? 0;

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

        $shipping = ShippingCalculator::calculate($totalWeight);

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

    public function add(AddToCartRequest $request, Product $product)
    {
        if (! $product->active) {
            abort(404);
        }

        $cart = session()->get('cart', []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $request->quantity;

        session()->put('cart', $cart);
        
        // Store the referrer URL so user can continue shopping from where they left off
        if ($request->headers->get('referer') && !str_contains($request->headers->get('referer'), '/cart')) {
            session()->put('shopping_return_url', $request->headers->get('referer'));
        }

        return redirect()->route('cart.index');
    }

    public function update(AddToCartRequest $request, Product $product)
    {
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
}
