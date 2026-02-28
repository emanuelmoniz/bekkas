<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItemOption;
use App\Models\Product;
use App\Models\ProductOption;
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

    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with(['items', 'address']);

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . trim($request->order_number) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('paid') && in_array($request->paid, ['1', '0'], true)) {
            $query->where('is_paid', $request->paid === '1');
        }

        $orders = $query->latest()->get();

        $statuses = \App\Models\OrderStatus::with('translations')->orderBy('sort_order')->get();

        return view('orders.index', compact('orders', 'statuses'));
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

        $order->load(['items.product', 'items.orderItemOptions', 'address', 'easypayPayload', 'easypayCheckoutSessions', 'easypayPayments']);

        // If the most-recent payment row indicates a persisted state that should
        // be reflected to the user (pending/paid/authorised), perform a best-effort
        // refresh so the UI (and order model) are authoritative.
        $latestPayment = $order->easypayPayments()->latest('created_at')->first();
        $paymentRefresh = null;
        if ($latestPayment && in_array($latestPayment->payment_status, ['pending', 'paid', 'authorised'], true)) {
            $paymentRefresh = app(\App\Services\EasypayPaymentRefreshService::class)->refreshLatestPaymentForOrder($order);
        }

        $viewVars = ['order' => $order];
        if (! empty($paymentRefresh)) {
            $viewVars = array_merge($viewVars, [
                'paymentInfo' => $paymentRefresh['paymentInfo'] ?? null,
                'paymentStatus' => $paymentRefresh['paymentStatus'] ?? null,
                'paymentStatusMessage' => $paymentRefresh['paymentStatusMessage'] ?? null,
                'suppressSdk' => (bool) ($paymentRefresh['suppressSdk'] ?? false),
            ]);
        }

        return view('orders.show', $viewVars);
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
        $productIds = array_unique(array_map([CartController::class, 'productIdFromCartKey'], array_keys($cart)));
        $products = Product::whereIn('id', $productIds)
            ->with(['optionTypes.options'])
            ->get()
            ->keyBy('id');
        $totalWeight = 0;
        $productsGross = 0;

        foreach ($cart as $cartKey => $rawEntry) {
            $productId = CartController::productIdFromCartKey($cartKey);
            $product = $products[$productId] ?? null;
            if (! $product) continue;
            $entry   = $this->cartEntry($rawEntry);
            $qty     = $entry['quantity'];
            $unitGross = CartController::resolveUnitPrice($product, $entry['options']);
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
            ->with('translations')
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
                ->with('translations')
                ->where('weight_from', '<=', $totalWeight)
                ->where('weight_to', '>=', $totalWeight)
                ->orderBy('shipping_days', 'asc')
                ->get();

            \Log::info('Using fallback tiers (no region match)', [
                'fallback_tiers_count' => $tiers->count(),
            ]);
        }

        $taxEnabled = (bool) config('app.tax_enabled', env('APP_TAX_ENABLED', true));

        $tiersData = collect();

        // Add free shipping tier if qualified
        if ($qualifiesForFreeShipping && $freeShippingTier) {
            $tiersData->push([
                'id' => $freeShippingTier->id,
                'name' => $freeShippingTier->translation()?->name,
                'cost_gross' => 0,
                'shipping_days' => $freeShippingTier->shipping_days,
                'is_free' => true,
                'shipping' => [
                    'gross' => 0,
                    'net' => 0,
                    'tax' => 0,
                ],
            ]);

            // Add only tiers that are strictly faster than the free tier (fewer shipping days)
            $otherTiers = $tiers->filter(function ($tier) use ($freeShippingTier) {
                // Only show tiers with fewer shipping days (faster delivery)
                return $tier->id !== $freeShippingTier->id
                    && $tier->shipping_days < $freeShippingTier->shipping_days;
            });

            \Log::info('Other tiers filter', [
                'free_tier_id' => $freeShippingTier->id,
                'free_tier_days' => $freeShippingTier->shipping_days,
                'all_tiers_count' => $tiers->count(),
                'other_tiers_count' => $otherTiers->count(),
                'other_tier_ids' => $otherTiers->pluck('id')->toArray(),
                'other_tier_names' => $otherTiers->map(fn ($t) => $t->translation()?->name)->toArray(),
            ]);

            foreach ($otherTiers as $tier) {
                $taxPct = $taxEnabled ? (optional($tier->tax)->percentage ?? 0) : 0;
                $gross = floatval($tier->cost_gross);
                $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                $tax = $taxPct > 0 ? $gross - $net : 0;

                $tiersData->push([
                    'id' => $tier->id,
                    'name' => $tier->translation()?->name,
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
            $tiersData = $tiers->map(function ($tier) use ($taxEnabled) {
                $taxPct = $taxEnabled ? (optional($tier->tax)->percentage ?? 0) : 0;
                $gross = floatval($tier->cost_gross);
                $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                $tax = $taxPct > 0 ? $gross - $net : 0;

                return [
                    'id' => $tier->id,
                    'name' => $tier->translation()?->name,
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

        $productIds = array_unique(array_map([CartController::class, 'productIdFromCartKey'], array_keys($cart)));
        $products = Product::whereIn('id', $productIds)
            ->where('active', true)
            ->with(['tax', 'optionTypes.options.translations', 'optionTypes.translations'])
            ->get()
            ->keyBy('id');

        // Validate stock availability for all products in cart (skip backorder products)
        $stockErrors = [];
        foreach ($cart as $cartKey => $rawCartEntry) {
            $productId = CartController::productIdFromCartKey($cartKey);
            $product = $products[$productId] ?? null;
            if (! $product) continue;

            // Skip validation for backorder products
            if ($product->is_backorder) {
                continue;
            }

            $entry   = $this->cartEntry($rawCartEntry);
            $qty     = $entry['quantity'];
            $productName = optional($product->translation())->name ?? "Product #{$productId}";

            // Check if an option type controls stock
            $stockType = $product->optionTypes->where('is_active', true)->where('have_stock', true)->first();
            if ($stockType) {
                $optionId = $entry['options'][$stockType->id] ?? null;
                if ($optionId) {
                    $option = $stockType->options->firstWhere('id', $optionId);
                    if ($option) {
                        if ($option->stock <= 0) {
                            $optName = optional($option->translation())->name ?? '';
                            $stockErrors[] = str_replace(':name', "{$productName} ({$optName})", t('stock.is_out_of_stock'));
                        } elseif ($qty > $option->stock) {
                            $optName = optional($option->translation())->name ?? '';
                            $message = t('stock.insufficient_stock');
                            $message = str_replace(':name', "{$productName} ({$optName})", $message);
                            $message = str_replace(':stock', $option->stock, $message);
                            $message = str_replace(':qty', $qty, $message);
                            $stockErrors[] = $message;
                        }
                        continue;
                    }
                }
            }

            // Product-level stock check (no have_stock option type)
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

        $taxEnabled = (bool) config('app.tax_enabled', env('APP_TAX_ENABLED', true));

        foreach ($cart as $cartKey => $rawCartEntry) {
            $productId = CartController::productIdFromCartKey($cartKey);
            $product   = $products[$productId] ?? null;
            if (! $product) continue;
            $entry     = $this->cartEntry($rawCartEntry);
            $qty       = $entry['quantity'];
            $unitGross = CartController::resolveUnitPrice($product, $entry['options']);

            // Safe tax retrieval (Laravel optional helper)
            $taxPct = $taxEnabled ? (optional($product->tax)->percentage ?? 0) : 0;

            $gross = $unitGross * $qty;
            // When taxes are disabled net == gross and tax == 0
            $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
            $tax = $taxPct > 0 ? $gross - $net : 0;

            // Build human-readable option labels for checkout display
            $selectedOptionLabels = [];
            foreach ($product->optionTypes->where('is_active', true) as $type) {
                $optionId = $entry['options'][$type->id] ?? null;
                if ($optionId) {
                    $option = $type->options->firstWhere('id', $optionId);
                    if ($option) {
                        $selectedOptionLabels[] = [
                            'type_name'   => optional($type->translation())->name ?? '',
                            'option_name' => optional($option->translation())->name ?? '',
                        ];
                    }
                }
            }

            $items[] = [
                'product'               => $product,
                'quantity'              => $qty,
                'options'               => $entry['options'],
                'selected_option_labels'=> $selectedOptionLabels,
                'gross'                 => round($gross, 2),
                'tax'                   => round($tax, 2),
            ];

            $productsGross += $gross;
            $productsTax   += $tax;
            $totalWeight   += ($product->weight * $qty);
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
                ->with('translations')
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
                    ->with('translations')
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
            $taxPct = $taxEnabled ? (optional($selectedShippingTier->tax)->percentage ?? 0) : 0;
            $shipping['net'] = $taxPct > 0 ? $shipping['gross'] / (1 + $taxPct / 100) : $shipping['gross'];
            $shipping['tax'] = $taxPct > 0 ? $shipping['gross'] - $shipping['net'] : 0;
        }

        // Map available tiers for Alpine.js with shipping breakdown
        $availableTiersFormatted = $availableShippingTiers->map(function ($t) use ($qualifiesForFreeShipping, $freeShippingTier, $taxEnabled) {
            $taxPct = $taxEnabled ? (optional($t->tax)->percentage ?? 0) : 0;
            $isFree = $qualifiesForFreeShipping && $freeShippingTier && $t->id === $freeShippingTier->id;
            $gross = $isFree ? 0 : $t->cost_gross;
            $net = $isFree ? 0 : ($taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross);
            $tax = $taxPct > 0 ? $gross - $net : 0;

            return [
                'id' => $t->id,
                'name' => $t->translation()?->name,
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

            $taxEnabled = (bool) config('app.tax_enabled', env('APP_TAX_ENABLED', true));

            DB::transaction(function () use ($user, $cart, $address, $request, &$createdOrder, $taxEnabled) {

                // Lock products for update to prevent race conditions
                $cartProductIds = array_unique(array_map([CartController::class, 'productIdFromCartKey'], array_keys($cart)));
                $products = Product::whereIn('id', $cartProductIds)
                    ->where('active', true)
                    ->with(['tax', 'optionTypes.options.translations', 'optionTypes.translations'])
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Collect and lock all referenced product_option rows
                $allOptionIds = [];
                foreach ($cart as $_cartKey => $rawEntry) {
                    $entry = $this->cartEntry($rawEntry);
                    foreach ($entry['options'] as $typeId => $optionId) {
                        $allOptionIds[] = (int) $optionId;
                    }
                }
                $lockedOptions = \App\Models\ProductOption::whereIn('id', $allOptionIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Validate stock availability before processing (skip backorder products)
                foreach ($cart as $cartKey => $rawEntry) {
                    $entry     = $this->cartEntry($rawEntry);
                    $qty       = $entry['quantity'];
                    $productId = CartController::productIdFromCartKey($cartKey);
                    $product   = $products[$productId] ?? null;
                    if (! $product) {
                        throw new \Exception(str_replace(':id', $productId, t('stock.product_not_found')));
                    }

                    // Skip stock validation for backorder products
                    if ($product->is_backorder) {
                        continue;
                    }

                    // Check option-level stock first
                    $stockType = $product->optionTypes->where('is_active', true)->where('have_stock', true)->first();
                    if ($stockType) {
                        $optId   = $entry['options'][$stockType->id] ?? null;
                        $option  = $optId ? ($lockedOptions[$optId] ?? null) : null;
                        if ($option) {
                            if ($option->stock < $qty) {
                                $productName = optional($product->translation())->name ?? "Product #{$productId}";
                                $message = t('stock.insufficient_for_order');
                                $message = str_replace(':name', $productName, $message);
                                $message = str_replace(':stock', $option->stock, $message);
                                $message = str_replace(':qty', $qty, $message);
                                throw new \Exception($message);
                            }
                            continue; // option-level check passed
                        }
                    }

                    // Fallback: product-level stock
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

                foreach ($cart as $cartKey => $rawEntry) {
                    $entry     = $this->cartEntry($rawEntry);
                    $qty       = $entry['quantity'];
                    $productId = CartController::productIdFromCartKey($cartKey);
                    $product   = $products[$productId] ?? null;
                    if (! $product) {
                        continue;
                    }

                    $unitGross = CartController::resolveUnitPrice($product, $entry['options']);

                    // Safe tax retrieval (Laravel optional helper). Respect global feature toggle.
                    $taxPct = $taxEnabled ? (optional($product->tax)->percentage ?? 0) : 0;

                    $gross = $unitGross * $qty;
                    // When taxes are disabled net == gross and tax == 0
                    $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                    $tax = $taxPct > 0 ? $gross - $net : 0;

                    // Determine backorder status at option or product level
                    $stockType   = $product->optionTypes->where('is_active', true)->where('have_stock', true)->first();
                    $optionStock = null;
                    if ($stockType) {
                        $optId = $entry['options'][$stockType->id] ?? null;
                        $optionStock = $optId ? ($lockedOptions[$optId] ?? null) : null;
                    }
                    $wasBackordered = $optionStock ? ($optionStock->stock == 0) : ($product->stock == 0);

                    $items[] = [
                        'product_id'       => $product->id,
                        'quantity'         => $qty,
                        'was_backordered'  => $wasBackordered,
                        'unit_price_gross' => $unitGross,
                        'tax_percentage'   => $taxPct,
                        'unit_weight'      => $product->weight,
                        'total_net'        => round($net, 2),
                        'total_tax'        => round($tax, 2),
                        'total_gross'      => round($gross, 2),
                        '_options_map'     => $entry['options'],
                    ];

                    $productsGross += $gross;
                    $productsTax   += $tax;
                    $productsNet   += $net;

                    $totalWeight += ($product->weight * $qty);

                    // Decrement option stock or product stock
                    if ($optionStock !== null) {
                        if ($optionStock->stock > 0) {
                            if ($optionStock->stock >= $qty) {
                                $optionStock->decrement('stock', $qty);
                            } else {
                                $optionStock->update(['stock' => 0]);
                            }
                        }
                    } else {
                        // Decrement product-level stock when available
                        if ($product->stock > 0) {
                            if ($product->stock >= $qty) {
                                // Full quantity available in stock
                                $product->decrement('stock', $qty);
                            } else {
                                // Partial stock available (rest is backorder if allowed)
                                $product->update(['stock' => 0]);
                            }
                        }
                        // If stock = 0, don't decrement (it's a backorder)
                    }
                }

                // Get shipping tier from request or calculate
                $shippingTierId = $request->input('shipping_tier_id');
                $shippingTier = null;
                $shippingTierName = null;

                if ($shippingTierId) {
                    $shippingTier = ShippingTier::with('translations')->find($shippingTierId);
                    $shippingTierName = $shippingTier?->translation()?->name;
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
                        $taxPct = $taxEnabled ? (optional($shippingTier->tax)->percentage ?? 0) : 0;
                        $gross = $shippingTier->cost_gross;
                        $net = $taxPct > 0 ? $gross / (1 + $taxPct / 100) : $gross;
                        $tax = $taxPct > 0 ? $gross - $net : 0;
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
                    $shippingTier = ShippingTier::with('translations')->find($defaultTierId);
                    $shippingTierName = $shippingTier?->translation()?->name;
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
                    'address_country' => optional($address->country)->name ?? 'Portugal',

                    'products_total_net' => round($productsNet, 2),
                    'products_total_tax' => round($productsTax, 2),
                    'products_total_gross' => round($productsGross, 2),
                    'tax_enabled' => $taxEnabled,

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
                    $optionsMap = $item['_options_map'] ?? [];
                    unset($item['_options_map']);

                    $orderItem = $order->items()->create($item);

                    foreach ($optionsMap as $typeId => $optionId) {
                        $option = $lockedOptions[$optionId] ?? null;
                        if (! $option) {
                            continue;
                        }
                        $type = $option->optionType;
                        $orderItem->orderItemOptions()->create([
                            'product_option_id' => $optionId,
                            'option_type_name'  => optional($type?->translation())->name ?? '',
                            'option_name'       => optional($option->translation())->name ?? '',
                        ]);
                    }
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

                // Customer: pass translation key + params so the Mailable resolves it at build time
                \Illuminate\Support\Facades\Mail::to($user->email)->locale($locale)->queue(new \App\Mail\OrderNotification(
                    $createdOrder,
                    'orders.email.event.placed',
                    $user->name,
                    $customerStatusLabel,
                    ['status' => $customerStatusLabel]
                ));

                // Notify admin (always in English) — pass the translation key and let the Mailable
                // resolve the label using the admin locale.
                $adminLocale = 'en-UK';
                $adminStatusLabel = $statusObj?->translation($adminLocale)?->name ?? $createdOrder->status;
                $adminEmail = config('mail.admin_address', 'info@bekkas.pt');
                \Illuminate\Support\Facades\Mail::to($adminEmail)->locale($adminLocale)->queue(new \App\Mail\OrderNotification(
                    $createdOrder,
                    'orders.email.event.new',
                    config('app.name'),
                    $adminStatusLabel,
                    ['status' => $adminStatusLabel],
                    route('admin.orders.show', $createdOrder)
                ));
            }

            return redirect()->route('orders.pay', $createdOrder)->with('success', t('orders.placed_success') ?: 'Order placed successfully!');
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

            // Run preflight cleanup + decide which checkout session (if any) should be
            // used by the SDK according to the new start logic (TTL + payment-record rules).
            $res = $orch->getManifestForSdk($order, $ttl);
            $activeManifest = $res['manifest'] ?? null;
            $payUnavailableMessage = $res['message'] ?? null;

            if (! empty($activeManifest)) {
                $payUnavailableMessage = null; // explicit clearing to preserve prior behaviour
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

            // Respect post-refresh suppression **only** when orchestration did not explicitly
            // produce an active manifest for the SDK. This ensures the preflight/start
            // decisions take precedence (per new requirements).
            if (! empty($post['suppressSdk']) && empty($activeManifest)) {
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

            // otherwise keep the activeManifest produced by orchestration (if any)
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

        // Defensive: if the refresh indicates SDK must be suppressed, only clear
        // the manifest when orchestration did not explicitly produce one OR when
        // the suppression is authoritative (order/payment confirmed `paid`).
        if (! empty($latestRefresh['suppressSdk'])) {
            $authoritativePaid = $order->is_paid || (($latestRefresh['paymentStatus'] ?? null) === 'paid');
            if (empty($activeManifest) || $authoritativePaid) {
                $activeManifest = null;
            }
        }

        // ENFORCE: only allow the SDK to be exposed when the order is explicitly
        // in the WAITING_PAYMENT state AND the order is not marked paid. This is a
        // defensive server-side guard against race conditions where a manifest
        // might be produced while the order has moved to another status.
        if (! ($order->status === 'WAITING_PAYMENT' && ! $order->is_paid)) {
            // clear any manifest that may have been produced by orchestration
            $activeManifest = null;
            // ensure the view/controller treats SDK as suppressed
            $latestRefresh['suppressSdk'] = true;
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

    /**
     * Normalise a raw cart entry to ['quantity' => int, 'options' => [type_id => option_id]].
     * Supports the legacy plain-integer format alongside the new array format.
     */
    private function cartEntry(mixed $rawEntry): array
    {
        if (is_array($rawEntry)) {
            return [
                'quantity' => (int) ($rawEntry['quantity'] ?? 0),
                'options'  => $rawEntry['options'] ?? [],
            ];
        }
        return ['quantity' => (int) $rawEntry, 'options' => []];
    }
}
