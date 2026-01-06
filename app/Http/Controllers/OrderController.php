<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Address;
use App\Services\ShippingCalculator;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->with(['items', 'address'])
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

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
            
            // Safe tax retrieval (Laravel optional helper)
            $taxPct = optional($product->tax)->percentage ?? 0;

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

        $shipping = ShippingCalculator::calculate($totalWeight);

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
    public function place(StoreOrderRequest $request)
    {
        $user = Auth::user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $validated = $request->validated();

        if ($request->filled('address_line_1')) {
            $user->addresses()->update(['is_default' => false]);

            $address = $user->addresses()->create($validated);
        } else {
            $address = Address::where('id', $validated['address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        try {
            DB::transaction(function () use ($user, $cart, $address) {

                $products = Product::whereIn('id', array_keys($cart))
                    ->where('active', true)
                    ->with(['tax'])
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
                    
                    // Safe tax retrieval (Laravel optional helper)
                    $taxPct = optional($product->tax)->percentage ?? 0;

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

                    $productsGross += $gross;
                    $productsTax += $tax;
                    $productsNet += $net;

                    $totalWeight += ($product->weight * $qty);
                }

                $shipping = ShippingCalculator::calculate($totalWeight);

                $order = $user->orders()->create([
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

                // Log successful order
                Log::info('Order created successfully', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'total_gross' => $order->total_gross,
                ]);

                session()->forget('cart');
            });

            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    private function calculateShipping(int $totalWeight): array
    {
        return ShippingCalculator::calculate($totalWeight);
    }
}

