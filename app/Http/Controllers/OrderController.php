<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingTier;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load(['items.product', 'address']);

        return view('orders.show', compact('order'));
    }

    /**
     * Checkout page
     */
    public function checkout()
    {
        $cart = session('cart', []);

        // 🔹 CHANGE #1 — Empty cart handling
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

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
            $taxPct = $product->tax()->first()->percentage;

            $gross = $unitGross * $qty;
            $net = $gross / (1 + $taxPct / 100);
            $tax = $gross - $net;

            $items[] = [
                'product' => $product,
                'quantity' => $qty,
                'gross' => round($gross, 2),
                'tax' => round($tax, 2),
            ];

            $productsGross += $gross;
            $productsTax += $tax;
            $totalWeight += ($product->weight * $qty);
        }

        $shipping = $this->calculateShipping($totalWeight);

        $addresses = Auth::user()->addresses()->get();

        return view('checkout.index', [
            'items' => $items,
            'addresses' => $addresses,

            'productsGross' => round($productsGross, 2),
            'productsTax' => round($productsTax, 2),
            'shipping' => $shipping,
            'totalGross' => round($productsGross + $shipping['gross'], 2),
            'totalTax' => round($productsTax + $shipping['tax'], 2),
        ]);
    }

    /**
     * Place order
     */
    public function place(Request $request)
    {
        $user = Auth::user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        // 🔹 CHANGE #2 — New address always allowed
        if ($request->filled('address_line_1')) {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'nif' => 'required|string|max:50',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'city' => 'required|string|max:100',
                'country' => 'required|string|max:100',
            ]);

            $user->addresses()->update(['is_default' => false]);

            $address = $user->addresses()->create([
                ...$data,
                'is_default' => true,
            ]);
        } else {
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
            ]);

            $address = Address::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        DB::transaction(function () use ($user, $cart, $address) {

            $products = Product::whereIn('id', array_keys($cart))
                ->where('active', true)
                ->get()
                ->keyBy('id');

            $totalWeight = 0;
            $productsNet = 0;
            $productsTax = 0;
            $productsGross = 0;

            $items = [];

            foreach ($cart as $productId => $qty) {
                $product = $products[$productId] ?? null;
                if (! $product) continue;

                $unitGross = $product->promo_price ?? $product->price;
                $taxPct = $product->tax()->first()->percentage;

                $gross = $unitGross * $qty;
                $net = $gross / (1 + $taxPct / 100);
                $tax = $gross - $net;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price_gross' => $unitGross,
                    'tax_percentage' => $taxPct,
                    'unit_weight' => $product->weight,
                    'total_net' => round($net, 2),
                    'total_tax' => round($tax, 2),
                    'total_gross' => round($gross, 2),
                ];

                $productsNet += $net;
                $productsTax += $tax;
                $productsGross += $gross;
                $totalWeight += ($product->weight * $qty);
            }

            $shipping = $this->calculateShipping($totalWeight);

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => 'PROCESSING',

                'products_total_net' => round($productsNet, 2),
                'products_total_tax' => round($productsTax, 2),
                'products_total_gross' => round($productsGross, 2),

                'shipping_net' => $shipping['net'],
                'shipping_tax' => $shipping['tax'],
                'shipping_gross' => $shipping['gross'],

                'total_net' => round($productsNet + $shipping['net'], 2),
                'total_tax' => round($productsTax + $shipping['tax'], 2),
                'total_gross' => round($productsGross + $shipping['gross'], 2),
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }
        });

        session()->forget('cart');

        return redirect()->route('orders.index');
    }

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
            'net' => round($net, 2),
            'tax' => round($tax, 2),
        ];
    }
}
