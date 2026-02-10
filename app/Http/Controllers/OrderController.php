<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\ShippingTier;
use App\Services\DefaultShippingTierResolver;
use App\Services\EasypayOrchestrationService;
use App\Services\EasypayService;
use App\Services\ShippingCalculator;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Defensive: when Easypay is disabled ensure no Easypay rows are created/left behind
        if (! config('easypay.enabled', false)) {
            \App\Models\EasypayPayload::where('order_id', $order->id)->delete();
            \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->delete();
            \App\Models\EasypayPayment::where('order_id', $order->id)->delete();
        }

        $order->load(['items.product', 'address']);

        return view('orders.show', compact('order'));
    }

    /**
     * Get available shipping tiers based on postal code and weight
     */
    public function getShippingTiers(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string',
            'address_id' => 'nullable|exists:addresses,id',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Get postal code and country
        $postalCode = $request->postal_code;
        $countryId = null;

        if ($request->address_id) {
            $address = Address::find($request->address_id);
            $countryId = $address->country_id;
        } elseif ($request->country_id) {
            $countryId = $request->country_id;
        }

        // Calculate total weight
        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalWeight = 0;
        $productsGross = 0;

        foreach ($products as $product) {
            $qty = $cart[$product->id];
            $unitGross = $product->is_promo ? ($product->promo_price ?? $product->price) : $product->price;
            $productsGross += $unitGross * $qty;
            $totalWeight += ($product->weight * $qty);
        }

        // Check for free shipping
        $freeShippingOver = floatval(ShippingConfig::get('free_shipping_over', 0));
        $qualifiesForFreeShipping = $productsGross >= $freeShippingOver && $freeShippingOver > 0;
        $freeShippingTier = null;

        if ($qualifiesForFreeShipping) {
            // Use region-based default (even if inactive) - no weight check for free tier
            if ($postalCode) {
                $freeShippingTier = DefaultShippingTierResolver::resolve($postalCode, 0);
            }

            // Fallback to global default
            if (! $freeShippingTier) {
                $defaultTierId = ShippingConfig::get('default_shipping_tier_id');
                $freeShippingTier = ShippingTier::withoutGlobalScopes()->find($defaultTierId);
            }
        }

        // Get shipping tiers by postal code
        $tiers = ShippingTier::where('active', true)
            ->where('weight_from', '<=', $totalWeight)
            ->where('weight_to', '>=', $totalWeight)
            ->whereHas('regions', function ($query) use ($postalCode, $countryId) {
                if ($countryId) {
                    $query->where('country_id', $countryId);
                }
                $query->where('postal_code_from', '<=', $postalCode)
                    ->where('postal_code_to', '>=', $postalCode);
            })
            ->orderBy('shipping_days', 'asc')
            ->get();

        // DEBUG
        \Log::info('Shipping Tiers Debug', [
            'postal_code' => $postalCode,
            'total_weight' => $totalWeight,
            'products_gross' => $productsGross,
            'qualifies_for_free' => $qualifiesForFreeShipping,
            'free_tier_id' => $freeShippingTier?->id,
            'free_tier_days' => $freeShippingTier?->shipping_days,
            'tiers_found' => $tiers->count(),
            'tier_ids' => $tiers->pluck('id')->toArray(),
            'tier_days' => $tiers->pluck('shipping_days')->toArray(),
        ]);

        // Fallback to weight-only tiers
        if ($tiers->isEmpty()) {
            $tiers = ShippingTier::where('active', true)
                ->where('weight_from', '<=', $totalWeight)
                ->where('weight_to', '>=', $totalWeight)
                ->orderBy('shipping_days', 'asc')
                ->get();

            \Log::info('Using fallback tiers (no region match)', [
                'fallback_tiers_count' => $tiers->count(),
            ]);
        }

        $tiersData = collect();

        // Add free shipping tier if qualified
        if ($qualifiesForFreeShipping && $freeShippingTier) {
            $tiersData->push([
                'id' => $freeShippingTier->id,
                'name' => $freeShippingTier->name_pt ?? $freeShippingTier->name_en,
                'cost_gross' => 0,
                'shipping_days' => $freeShippingTier->shipping_days,
                'is_free' => true,
                'shipping' => [
                    'gross' => 0,
                    'net' => 0,
                    'tax' => 0,
                ],
            ]);

            // Add all other tiers from the region (with cost) - exclude the free tier
            $otherTiers = $tiers->filter(function ($tier) use ($freeShippingTier) {
                // Show all tiers except the one being used as free
                return $tier->id !== $freeShippingTier->id;
            });

            \Log::info('Other tiers filter', [
                'free_tier_id' => $freeShippingTier->id,
                'free_tier_days' => $freeShippingTier->shipping_days,
                'all_tiers_count' => $tiers->count(),
                'other_tiers_count' => $otherTiers->count(),
                'other_tier_ids' => $otherTiers->pluck('id')->toArray(),
                'other_tier_names' => $otherTiers->pluck('name_en')->toArray(),
            ]);

            foreach ($otherTiers as $tier) {
                $taxPct = optional($tier->tax)->percentage ?? 0;
                $gross = floatval($tier->cost_gross);
                $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                $tax = $gross - $net;

                $tiersData->push([
                    'id' => $tier->id,
                    'name' => $tier->name_pt ?? $tier->name_en,
                    'cost_gross' => round($gross, 2),
                    'shipping_days' => $tier->shipping_days,
                    'is_free' => false,
                    'shipping' => [
                        'gross' => round($gross, 2),
                        'net' => round($net, 2),
                        'tax' => round($tax, 2),
                    ],
                ]);
            }

            \Log::info('Final tiers data', [
                'tiers_count' => $tiersData->count(),
                'tier_ids' => $tiersData->pluck('id')->toArray(),
                'tier_is_free' => $tiersData->pluck('is_free')->toArray(),
            ]);
        } else {
            // No free shipping - show all tiers
            $tiersData = $tiers->map(function ($tier) {
                $taxPct = optional($tier->tax)->percentage ?? 0;
                $gross = floatval($tier->cost_gross);
                $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                $tax = $gross - $net;

                return [
                    'id' => $tier->id,
                    'name' => $tier->name_pt ?? $tier->name_en,
                    'cost_gross' => round($gross, 2),
                    'shipping_days' => $tier->shipping_days,
                    'is_free' => false,
                    'shipping' => [
                        'gross' => round($gross, 2),
                        'net' => round($net, 2),
                        'tax' => round($tax, 2),
                    ],
                ];
            });
        }

        return response()->json([
            'qualifies_for_free_shipping' => $qualifiesForFreeShipping,
            'free_shipping_over' => $freeShippingOver,
            'tiers' => $tiersData->values(),
        ]);
    }

    /**
     * Checkout page
     */
    public function checkout()
    {
        $cart = session('cart', []);

        // Empty cart handling
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $products = Product::whereIn('id', array_keys($cart))
            ->where('active', true)
            ->get();

        // Validate stock availability for all products in cart (skip backorder products)
        $stockErrors = [];
        foreach ($products as $product) {
            // Skip validation for backorder products
            if ($product->is_backorder) {
                continue;
            }

            $qty = $cart[$product->id];
            $productName = optional($product->translation())->name ?? "Product #{$product->id}";

            if ($product->stock <= 0) {
                $stockErrors[] = str_replace(':name', $productName, t('stock.is_out_of_stock'));
            } elseif ($qty > $product->stock) {
                $message = t('stock.insufficient_stock');
                $message = str_replace(':name', $productName, $message);
                $message = str_replace(':stock', $product->stock, $message);
                $message = str_replace(':qty', $qty, $message);
                $stockErrors[] = $message;
            }
        }

        if (! empty($stockErrors)) {
            return redirect()->route('cart.index')->with('error', implode(' ', $stockErrors));
        }

        $items = [];
        $totalWeight = 0;
        $productsGross = 0;
        $productsTax = 0;

        foreach ($products as $product) {
            $qty = $cart[$product->id];
            $unitGross = $product->is_promo ? ($product->promo_price ?? $product->price) : $product->price;

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

        $addresses = Auth::user()->addresses()->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        // Get free shipping threshold
        $freeShippingOver = floatval(ShippingConfig::get('free_shipping_over', 0));
        $qualifiesForFreeShipping = $productsGross >= $freeShippingOver && $freeShippingOver > 0;

        // Get available shipping tiers
        $availableShippingTiers = collect([]);
        $selectedShippingTier = null;
        $freeShippingTier = null;

        if ($qualifiesForFreeShipping && $defaultAddress && $defaultAddress->postal_code) {
            // Get region-based default free shipping tier (even if inactive)
            $freeShippingTier = DefaultShippingTierResolver::resolve($defaultAddress->postal_code, 0);

            // Fallback to global default if no region-based tier found
            if (! $freeShippingTier) {
                $defaultTierId = ShippingConfig::get('default_shipping_tier_id');
                $freeShippingTier = ShippingTier::withoutGlobalScopes()->find($defaultTierId);
            }

            $selectedShippingTier = $freeShippingTier;
        }

        if ($defaultAddress && $defaultAddress->postal_code) {
            // Get active shipping tiers matching the default address postal code
            $availableShippingTiers = ShippingTier::where('active', true)
                ->where('weight_from', '<=', $totalWeight)
                ->where('weight_to', '>=', $totalWeight)
                ->whereHas('regions', function ($query) use ($defaultAddress) {
                    $query->where('postal_code_from', '<=', $defaultAddress->postal_code)
                        ->where('postal_code_to', '>=', $defaultAddress->postal_code);
                })
                ->orderBy('shipping_days', 'asc')
                ->get();

            // If no tiers match postal code, get all active tiers matching weight
            if ($availableShippingTiers->isEmpty()) {
                $availableShippingTiers = ShippingTier::where('active', true)
                    ->where('weight_from', '<=', $totalWeight)
                    ->where('weight_to', '>=', $totalWeight)
                    ->orderBy('shipping_days', 'asc')
                    ->get();
            }

            // If qualifying for free shipping, add free tier and keep all other tiers
            if ($qualifiesForFreeShipping && $freeShippingTier) {
                // Add free tier to the collection (even if inactive)
                $tiersCollection = collect([$freeShippingTier]);

                // Add all other active tiers that are FASTER than the free one (exclude same or slower)
                $otherTiers = $availableShippingTiers->filter(function ($tier) use ($freeShippingTier) {
                    return $tier->id !== $freeShippingTier->id && $tier->shipping_days < $freeShippingTier->shipping_days;
                });

                $availableShippingTiers = $tiersCollection->merge($otherTiers)->values();
            } else {
                // Select first tier as default if not free shipping
                $selectedShippingTier = $selectedShippingTier ?? $availableShippingTiers->first();
            }
        }

        // Calculate shipping cost
        $shipping = ['gross' => 0, 'net' => 0, 'tax' => 0];
        if ($selectedShippingTier) {
            $shipping['gross'] = $qualifiesForFreeShipping ? 0 : $selectedShippingTier->cost_gross;
            $taxPct = optional($selectedShippingTier->tax)->percentage ?? 0;
            $shipping['net'] = $taxPct > 0 ? $shipping['gross'] / (1 + $taxPct / 100) : $shipping['gross'];
            $shipping['tax'] = $shipping['gross'] - $shipping['net'];
        }

        // Map available tiers for Alpine.js with shipping breakdown
        $availableTiersFormatted = $availableShippingTiers->map(function ($t) use ($qualifiesForFreeShipping, $freeShippingTier) {
            $taxPct = optional($t->tax)->percentage ?? 0;
            $isFree = $qualifiesForFreeShipping && $freeShippingTier && $t->id === $freeShippingTier->id;
            $gross = $isFree ? 0 : $t->cost_gross;
            $net = $isFree ? 0 : ($taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross);
            $tax = $gross - $net;

            return [
                'id' => $t->id,
                'name' => $t->name_pt ?? $t->name_en,
                'cost_gross' => $gross,
                'shipping_days' => $t->shipping_days,
                'is_free' => $isFree,
                'regions' => $t->regions->map(fn ($r) => [
                    'country_id' => $r->country_id,
                    'postal_code_from' => $r->postal_code_from,
                    'postal_code_to' => $r->postal_code_to,
                ])->toArray(),
                'shipping' => [
                    'gross' => round($gross, 2),
                    'net' => round($net, 2),
                    'tax' => round($tax, 2),
                ],
            ];
        });

        return view('checkout.index', [
            'items' => $items,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'productsGross' => round($productsGross, 2),
            'productsTax' => round($productsTax, 2),
            'shipping' => $shipping,
            'totalGross' => round($productsGross + $shipping['gross'], 2),
            'totalTax' => round($productsTax + $shipping['tax'], 2),
            'availableShippingTiers' => $availableShippingTiers,
            'availableShippingTiersFormatted' => $availableTiersFormatted,
            'selectedShippingTier' => $selectedShippingTier,
            'qualifiesForFreeShipping' => $qualifiesForFreeShipping,
            'freeShippingOver' => $freeShippingOver,
            'totalWeight' => $totalWeight,
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
            // Only unset other default addresses if this new one should be default
            if ($request->filled('is_default') && $request->is_default) {
                $user->addresses()->update(['is_default' => false]);
            }

            $address = $user->addresses()->create($validated);
        } else {
            $address = Address::where('id', $validated['address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        try {
            $createdOrder = null;

            DB::transaction(function () use ($user, $cart, $address, $request, &$createdOrder) {

                // Lock products for update to prevent race conditions
                $products = Product::whereIn('id', array_keys($cart))
                    ->where('active', true)
                    ->with(['tax'])
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Validate stock availability before processing (skip backorder products)
                foreach ($cart as $productId => $qty) {
                    $product = $products[$productId] ?? null;
                    if (! $product) {
                        throw new \Exception(str_replace(':id', $productId, t('stock.product_not_found')));
                    }

                    // Skip stock validation for backorder products
                    if ($product->is_backorder) {
                        continue;
                    }

                    if ($product->stock < $qty) {
                        $productName = optional($product->translation())->name ?? "Product #{$productId}";
                        $message = t('stock.insufficient_for_order');
                        $message = str_replace(':name', $productName, $message);
                        $message = str_replace(':stock', $product->stock, $message);
                        $message = str_replace(':qty', $qty, $message);
                        throw new \Exception($message);
                    }
                }

                $totalWeight = 0;
                $productsNet = 0;
                $productsTax = 0;
                $productsGross = 0;

                $items = [];

                foreach ($cart as $productId => $qty) {
                    $product = $products[$productId] ?? null;
                    if (! $product) {
                        continue;
                    }

                    $unitGross = $product->is_promo ? ($product->promo_price ?? $product->price) : $product->price;

                    // Safe tax retrieval (Laravel optional helper)
                    $taxPct = optional($product->tax)->percentage ?? 0;

                    $gross = $unitGross * $qty;
                    $net = $gross / (1 + $taxPct / 100);
                    $tax = $gross - $net;

                    // Track if this was a backorder (stock was 0 when ordered)
                    $wasBackordered = ($product->stock == 0);

                    $items[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'was_backordered' => $wasBackordered,
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

                    // Decrement stock when available
                    if ($product->stock > 0) {
                        if ($product->stock >= $qty) {
                            // Full quantity available in stock
                            $product->decrement('stock', $qty);
                        } else {
                            // Partial stock available (rest is backorder if allowed)
                            $availableStock = $product->stock;
                            $product->update(['stock' => 0]);
                        }
                    }
                    // If stock = 0, don't decrement (it's a backorder)
                }

                // Get shipping tier from request or calculate
                $shippingTierId = $request->input('shipping_tier_id');
                $shippingTier = null;
                $shippingTierName = null;

                if ($shippingTierId) {
                    $shippingTier = ShippingTier::find($shippingTierId);
                    $shippingTierName = $shippingTier ? ($shippingTier->name_en ?? $shippingTier->name_pt) : null;
                }

                // Calculate shipping cost
                $freeShippingOver = floatval(ShippingConfig::get('free_shipping_over', 0));
                $qualifiesForFreeShipping = $productsGross >= $freeShippingOver && $freeShippingOver > 0;

                // Determine the free tier (region-based or global)
                $freeTier = null;
                if ($qualifiesForFreeShipping && $address && $address->postal_code) {
                    $freeTier = DefaultShippingTierResolver::resolve($address->postal_code, $totalWeight);
                }

                // Fallback to global default
                if ($qualifiesForFreeShipping && ! $freeTier) {
                    $defaultTierId = ShippingConfig::get('default_shipping_tier_id');
                    $freeTier = ShippingTier::find($defaultTierId);
                }

                // Check if user selected the free tier or a paid tier
                $isUsingFreeTier = $qualifiesForFreeShipping && $freeTier && $shippingTier && $shippingTier->id == $freeTier->id;

                if ($isUsingFreeTier) {
                    // Free shipping
                    $shipping = ['gross' => 0, 'net' => 0, 'tax' => 0];
                } else {
                    // Calculate cost from selected tier or use default calculation
                    if ($shippingTier) {
                        $taxPct = optional($shippingTier->tax)->percentage ?? 0;
                        $gross = $shippingTier->cost_gross;
                        $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                        $tax = $gross - $net;
                        $shipping = [
                            'gross' => round($gross, 2),
                            'net' => round($net, 2),
                            'tax' => round($tax, 2),
                        ];
                    } else {
                        $shipping = ShippingCalculator::calculate($totalWeight);
                    }
                }

                // Get tier name if not set
                if (! $shippingTierName && $qualifiesForFreeShipping && ! $shippingTier) {
                    $shippingTier = ShippingTier::find($defaultTierId);
                    $shippingTierName = $shippingTier ? ($shippingTier->name_en ?? $shippingTier->name_pt) : null;
                }

                // Calculate expected delivery date
                $expectedDeliveryDate = null;
                if ($shippingTier) {
                    $shippingDays = $shippingTier->shipping_days ?? 0;

                    // Find max production time from backordered items
                    $maxProductionDays = 0;
                    foreach ($items as $item) {
                        if ($item['was_backordered']) {
                            $product = $products[$item['product_id']] ?? null;
                            if ($product && $product->is_backorder) {
                                $maxProductionDays = max($maxProductionDays, $product->production_time ?? 0);
                            }
                        }
                    }

                    $totalWorkingDays = $maxProductionDays + $shippingDays;
                    $expectedDeliveryDate = $this->addWorkingDays(Carbon::now(), $totalWorkingDays);
                }

                $order = $user->orders()->create([
                    'address_id' => $address->id,
                    'status' => 'WAITING_PAYMENT',

                    // Address snapshot
                    'address_title' => $address->title,
                    'address_nif' => $address->nif,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'address_postal_code' => $address->postal_code,
                    'address_city' => $address->city,
                    'address_country' => optional($address->country)->name_pt ?? 'Portugal',

                    'products_total_net' => round($productsNet, 2),
                    'products_total_tax' => round($productsTax, 2),
                    'products_total_gross' => round($productsGross, 2),

                    'shipping_net' => $shipping['net'],
                    'shipping_tax' => $shipping['tax'],
                    'shipping_gross' => $shipping['gross'],
                    'shipping_tier_name' => $shippingTierName,
                    'expected_delivery_date' => $expectedDeliveryDate,

                    'total_net' => round($productsNet + $shipping['net'], 2),
                    'total_tax' => round($productsTax + $shipping['tax'], 2),
                    'total_gross' => round($productsGross + $shipping['gross'], 2),
                ]);

                foreach ($items as $item) {
                    $order->items()->create($item);
                }

                // Keep order reference for post-transaction actions
                $createdOrder = $order;

                // Log successful order
                Log::info('Order created successfully', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'total_gross' => $order->total_gross,
                ]);

                session()->forget('cart');
            });

            // Easypay: create payload + checkout session (non-blocking)
            if ($createdOrder && config('easypay.enabled')) {
                try {
                    $payload = EasypayService::createOrGetPayload($createdOrder);
                    EasypayService::createCheckoutSession($payload);
                } catch (\Exception $e) {
                    \Log::error('Easypay integration failed during order placement', ['order_id' => $createdOrder->id, 'error' => $e->getMessage()]);
                    // Do not fail the order placement if Easypay is unavailable
                }
            }

            // Send emails after transaction completes
            if ($createdOrder) {
                $locale = $user->language ?? app()->getLocale();

                // Resolve status translation for customer email
                $statusObj = \App\Models\OrderStatus::where('code', $createdOrder->status)->first();
                $customerStatusLabel = $statusObj?->translation($locale)?->name ?? $createdOrder->status;

                \Illuminate\Support\Facades\Mail::to($user->email)->locale($locale)->queue(new \App\Mail\OrderNotification($createdOrder, t('orders.email.event.placed', ['status' => $customerStatusLabel]) ?: 'Order placed', $user->name, $customerStatusLabel));

                // Notify admin (always in English)
                $adminLocale = 'en-UK';
                $adminStatusLabel = $statusObj?->translation($adminLocale)?->name ?? $createdOrder->status;
                $adminEmail = config('mail.admin_address', 'info@bekkas.pt');
                \Illuminate\Support\Facades\Mail::to($adminEmail)->locale($adminLocale)->queue(new \App\Mail\OrderNotification($createdOrder, t('orders.email.event.new', ['status' => $adminStatusLabel]) ?: 'New order', config('app.name'), $adminStatusLabel));
            }

            return redirect()->route('orders.index')->with('success', t('orders.placed_success') ?: 'Order placed successfully!');
        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show payment (Easypay) page for the order — displays payload + checkout sessions
     *
     * Behaviour (server-side orchestration):
     * - Only allow orders WAITING_PAYMENT and not paid
     * - If an active pending checkout session exists and is within TTL -> expose its manifest (start SDK)
     * - Otherwise: ensure payload exists, create a fresh checkout session and expose its manifest
     * - On any error show a graceful, translatable message; include full error when APP_DEBUG=true
     */
    public function pay(Order $order)
    {
        $this->authorize('view', $order);

        // Strict access: only awaiting payment and not yet paid
        if ($order->is_paid || $order->status !== 'WAITING_PAYMENT' || $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->loadMissing(['items.product', 'easypayPayload', 'easypayCheckoutSessions']);

        $order->loadMissing(['items.product', 'easypayPayload', 'easypayCheckoutSessions']);

        // Delegate payment-refresh + decision logic to dedicated service (DB-first, then best-effort remote refresh).
        $refresh = app(\App\Services\EasypayPaymentRefreshService::class)->refreshLatestPaymentForOrder($order);

        // Ensure controller always provides these view vars (view no longer queries DB directly)
        if (! empty($refresh['suppressSdk'])) {
            return view('orders.pay', [
                'order' => $order,
                'payload' => $order->easypayPayload,
                'sessions' => $order->easypayCheckoutSessions()->latest('created_at')->get(),
                'activeManifest' => null,
                'payUnavailableMessage' => null,
                'suppressSdk' => (bool) $refresh['suppressSdk'],
                'paymentInfo' => $refresh['paymentInfo'] ?? null,
                'paymentStatus' => $refresh['paymentStatus'] ?? null,
                'paymentStatusMessage' => $refresh['paymentStatusMessage'] ?? null,
            ]);
        }

        // Short-circuit when Easypay is disabled: do not create payloads/sessions and always show a friendly message.
        if (! config('easypay.enabled')) {
            // Prefer the DB translation when available; if the gateway-specific key is missing prefer the
            // generic `checkout.pay.unavailable` key (tests sometimes rely on the latter existing).
            $primary = t('checkout.gateways.disabled');
            $msg = ($primary === 'checkout.gateways.disabled') ? t('checkout.pay.unavailable') : $primary;

            return view('orders.pay', [
                'order' => $order,
                'payload' => $order->easypayPayload,
                'sessions' => $order->easypayCheckoutSessions()->latest('created_at')->get(),
                'activeManifest' => null,
                'payUnavailableMessage' => $msg,
            ]);
        }

        $ttl = (int) config('easypay.session_ttl', 1800);
        $now = now();

        $payUnavailableMessage = null;
        $payUnavailableDebug = null;
        $activeManifest = null;

        try {
            $orch = new EasypayOrchestrationService;

            // Reuse a fresh manifest if present
            $activeManifest = $orch->getLatestActiveManifest($order, $ttl);

            // If no usable manifest, attempt payload+session creation (service is idempotent)
            if (empty($activeManifest)) {
                $res = $orch->ensurePayloadAndSession($order);
                $activeManifest = $res['manifest'] ?? null;
                $payUnavailableMessage = $res['message'] ?? null;

                if (! empty($activeManifest)) {
                    $payUnavailableMessage = null; // explicit clearing to preserve prior behaviour
                }
            }
        } catch (\Exception $e) {
            // Log and show a friendly, translatable message. Include full error only in debug.
            Log::error('Easypay orchestration failed on pay page', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            $payUnavailableMessage = t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.';

            if (config('app.debug')) {
                $payUnavailableDebug = $e->getMessage();
                $payUnavailableMessage = ($payUnavailableMessage.' '.($payUnavailableDebug ? (t('checkout.pay.unavailable_debug', ['error' => $payUnavailableDebug]) ?: $payUnavailableDebug) : ''));
            }
        }

        // If orchestration completed but we still don't have an active manifest, show graceful message
        if (empty($activeManifest) && config('easypay.enabled')) {
            $latest = $order->easypayCheckoutSessions()->latest('updated_at')->first();
            $payUnavailableMessage = $payUnavailableMessage ?: (t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.');

            if (config('app.debug') && $latest) {
                $debug = is_string($latest->message) ? $latest->message : json_encode($latest->message);
                $payUnavailableMessage = ($payUnavailableMessage.' '.(t('checkout.pay.unavailable_debug', ['error' => $debug]) ?: $debug));
            }
        }

        // Post-orchestration: re-run the same service to ensure UI reflects any
        // payment state that may have changed as a result of orchestration.
        try {
            $post = app(\App\Services\EasypayPaymentRefreshService::class)->refreshLatestPaymentForOrder($order);
            if (! empty($post['suppressSdk'])) {
                return view('orders.pay', [
                    'order' => $order,
                    'payload' => $order->easypayPayload,
                    'sessions' => $order->easypayCheckoutSessions()->latest('created_at')->get(),
                    'activeManifest' => null,
                    'payUnavailableMessage' => null,
                    'suppressSdk' => (bool) $post['suppressSdk'],
                    'paymentInfo' => $post['paymentInfo'] ?? null,
                    'paymentStatus' => $post['paymentStatus'] ?? null,
                    'paymentStatusMessage' => $post['paymentStatusMessage'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Easypay: post-orchestration payment refresh failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        // Final safety: if a DB session exists in error state, expose a friendly message (defensive)
        if (empty($payUnavailableMessage)) {
            $errored = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->where('in_error', true)->latest('updated_at')->first();
            if ($errored) {
                $payUnavailableMessage = t('checkout.pay.unavailable') ?: 'Payment system is temporarily unavailable — please check your order details in a moment and try again.';
                if (config('app.debug') && $errored->message) {
                    $payUnavailableMessage = $payUnavailableMessage.' '.(t('checkout.pay.unavailable_debug', ['error' => $errored->message]) ?: $errored->message);
                }
            }
        }

        // Prefer post-orchestration refresh when available, otherwise fall back to initial refresh
        $latestRefresh = $post ?? $refresh ?? ['suppressSdk' => false, 'paymentInfo' => null, 'paymentStatus' => null, 'paymentStatusMessage' => null];

        // Defensive: if the refresh indicates SDK must be suppressed, ensure we do not
        // expose an active manifest to the view under any circumstances.
        if (! empty($latestRefresh['suppressSdk'])) {
            $activeManifest = null;
        }

        return view('orders.pay', [
            'order' => $order,
            'payload' => $order->easypayPayload,
            'sessions' => $order->easypayCheckoutSessions()->latest('created_at')->get(),
            'activeManifest' => $activeManifest,
            'payUnavailableMessage' => $payUnavailableMessage,

            // Always supply payment-driven view flags (controller-authoritative)
            'suppressSdk' => (bool) ($latestRefresh['suppressSdk'] ?? false),
            'paymentInfo' => $latestRefresh['paymentInfo'] ?? null,
            'paymentStatus' => $latestRefresh['paymentStatus'] ?? null,
            'paymentStatusMessage' => $latestRefresh['paymentStatusMessage'] ?? null,
        ]);
    }

    /**
     * Add working days (excluding weekends) to a date
     */
    private function addWorkingDays(Carbon $startDate, int $workingDays): Carbon
    {
        $date = $startDate->copy();
        $daysAdded = 0;

        while ($daysAdded < $workingDays) {
            $date->addDay();

            // Skip weekends (Saturday = 6, Sunday = 0)
            if (! $date->isWeekend()) {
                $daysAdded++;
            }
        }

        return $date;
    }

    private function calculateShipping(int $totalWeight): array
    {
        return ShippingCalculator::calculate($totalWeight);
    }
}
