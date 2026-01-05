<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingTier;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List authenticated user's orders
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show order details (read-only)
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'address']);

        return view('orders.show', compact('order'));
    }

    /**
     * Place order (controller-driven, no cart UI yet)
     *
     * Expected payload:
     * - address_id
     * - products: [product_id => quantity]
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array|min:1',
            'products.*' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        DB::transaction(function () use ($request, $user, $address) {

            $products = Product::whereIn('id', array_keys($request->products))
                ->where('active', true)
                ->get()
                ->keyBy('id');

            $items = [];
            $totalWeight = 0;

            $productsNet = 0;
            $productsTax = 0;
            $productsGross = 0;

            foreach ($request->products as $productId => $quantity) {
                $product = $products[$productId] ?? null;
                if (! $product) {
                    continue;
                }

                $unitPrice = $product->promo_price ?? $product->price;
                $taxPct = $product->tax->percentage;

                $gross = $unitPrice * $quantity;
                $net = $gross / (1 + $taxPct / 100);
                $tax = $gross - $net;

                $weight = $product->weight * $quantity;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price_gross' => $unitPrice,
                    'tax_percentage' => $taxPct,
                    'unit_weight' => $product->weight,
                    'total_net' => round($net, 2),
                    'total_tax' => round($tax, 2),
                    'total_gross' => round($gross, 2),
                ];

                $productsNet += $net;
                $productsTax += $tax;
                $productsGross += $gross;
                $totalWeight += $weight;
            }

            // SHIPPING (stackable tiers)
            $tiers = ShippingTier::where('active', true)
                ->orderBy('weight_to')
                ->get();

            $remainingWeight = $totalWeight;
            $shippingGross = 0;
            $shippingTax = 0;
            $shippingNet = 0;

            $maxTier = $tiers->last();

            while ($remainingWeight > 0 && $maxTier) {
                $shippingGross += $maxTier->cost_gross;
		$shippingTaxPct = $maxTier->tax->percentage;
		$net = $maxTier->cost_gross / (1 + $shippingTaxPct / 100);
                $tax = $maxTier->cost_gross - $net;

                $shippingNet += $net;
                $shippingTax += $tax;

                $remainingWeight -= $maxTier->weight_to;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => 'PROCESSING',
                'is_paid' => false,
                'is_canceled' => false,
                'is_refunded' => false,

                'products_total_net' => round($productsNet, 2),
                'products_total_tax' => round($productsTax, 2),
                'products_total_gross' => round($productsGross, 2),

                'shipping_net' => round($shippingNet, 2),
                'shipping_tax' => round($shippingTax, 2),
                'shipping_gross' => round($shippingGross, 2),

                'total_net' => round($productsNet + $shippingNet, 2),
                'total_tax' => round($productsTax + $shippingTax, 2),
                'total_gross' => round($productsGross + $shippingGross, 2),
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }
        });

        return redirect()->route('orders.index');
    }
}
