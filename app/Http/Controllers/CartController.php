<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────

    /**
     * Normalise a raw cart entry to always return
     * ['quantity' => int, 'options' => [type_id => option_id, ...]].
     * Supports the legacy format where the entry was just an integer.
     */
    private function normaliseEntry(mixed $entry): array
    {
        if (is_array($entry)) {
            return [
                'quantity' => (int) ($entry['quantity'] ?? 0),
                'options'  => $entry['options'] ?? [],
            ];
        }
        // Legacy: plain integer quantity, no options
        return ['quantity' => (int) $entry, 'options' => []];
    }

    /**
     * Build a stable cart key from a product ID and its selected options map.
     * Same product + same options → same key (will merge qty).
     * Same product + different options → different key (separate line).
     *
     * Format: "{productId}_{typeId}:{optionId},{typeId}:{optionId},..."
     * Example: "42_3:12,5:7"
     */
    public static function makeCartKey(int $productId, array $optionsMap): string
    {
        ksort($optionsMap);
        $parts = [];
        foreach ($optionsMap as $typeId => $optionId) {
            $parts[] = $typeId . ':' . $optionId;
        }
        return $productId . '_' . implode(',', $parts);
    }

    /** Extract the product ID from a composite cart key. */
    public static function productIdFromCartKey(string $key): int
    {
        return (int) explode('_', $key, 2)[0];
    }

    /** Return the sum of all quantities in the session cart. */
    private function cartCount(): int
    {
        $cart = session('cart', []);
        return array_sum(array_map(
            fn ($e) => $this->normaliseEntry($e)['quantity'],
            $cart
        ));
    }

    /**
     * Given a product and a submitted options map (type_id → option_id),
     * validate that every active option type with is_active has a selection,
     * that the selected option actually belongs to that type, and return the
     * resolved option models keyed by type_id.
     *
     * Returns an array of errors on failure, or the resolved options on success.
     */
    private function resolveOptions(Product $product, array $submitted): array|string
    {
        $product->loadMissing(['optionTypes.options']);

        $resolved   = [];
        $errors     = [];

        foreach ($product->optionTypes as $type) {
            if (! $type->is_active) {
                continue;
            }

            $optionId = $submitted[$type->id] ?? null;

            // Treat 0, "", null as "not selected" (x-model.number sends 0 for blank <option value="">)
            if (! $optionId) {
                $typeName = optional($type->translation())->name ?? "Option type #{$type->id}";
                $errors[] = str_replace(':type', $typeName, t('store.select_option_for') ?: "Please select a value for: {$typeName}");
                continue;
            }

            /** @var \App\Models\ProductOption|null $option */
            $option = $type->options->where('id', $optionId)->where('is_active', true)->first();

            if (! $option) {
                $typeName = optional($type->translation())->name ?? "Option type #{$type->id}";
                $errors[] = str_replace(':type', $typeName, t('store.invalid_option_for') ?: "Invalid selection for: {$typeName}");
                continue;
            }

            $resolved[$type->id] = $option;
        }

        if (! empty($errors)) {
            return implode(' ', $errors);
        }

        return $resolved;
    }

    /**
     * Check stock for one product + resolved options.
     * Returns null on success or an error message string on failure.
     */
    private function checkStock(Product $product, array $resolvedOptions, int $newQty): ?string
    {
        foreach ($resolvedOptions as $typeId => $option) {
            $type = $option->optionType;

            if ($type->have_stock) {
                // Stock lives on the option, not the product
                if (! $product->is_backorder && $option->stock <= 0) {
                    $optName = optional($option->translation())->name ?? "Option #{$option->id}";
                    return t('store.out_of_stock') ?: "'{$optName}' is out of stock.";
                }
                if (! $product->is_backorder && $newQty > $option->stock) {
                    return str_replace(':stock', $option->stock, t('stock.only_available'));
                }
                // Once we found the stock-controlling type, no need to check product stock
                return null;
            }
        }

        // No option type controls stock → fall back to product-level stock
        if (! $product->is_backorder && $product->stock <= 0) {
            return t('store.out_of_stock') ?: 'This product is out of stock.';
        }
        if (! $product->is_backorder && $newQty > $product->stock) {
            return str_replace(':stock', $product->stock, t('stock.only_available'));
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────

    public function index()
    {
        $cart = session()->get('cart', []);

        $productIds = array_unique(array_map([self::class, 'productIdFromCartKey'], array_keys($cart)));

        $products = Product::whereIn('id', $productIds)
            ->where('active', true)
            ->with(['tax', 'optionTypes.options.translations'])
            ->get()
            ->keyBy('id');

        $items = [];
        $productsGross = 0;
        $productsTax   = 0;

        $taxEnabled = (bool) config('app.tax_enabled', env('APP_TAX_ENABLED', true));

        foreach ($cart as $cartKey => $rawEntry) {
            $productId = self::productIdFromCartKey($cartKey);
            $product   = $products[$productId] ?? null;
            if (! $product) {
                continue;
            }

            $entry   = $this->normaliseEntry($rawEntry);
            $qty     = $entry['quantity'];
            $options = $entry['options']; // [type_id => option_id]

            // Resolve unit price (may come from a selected option's price)
            $unitGross = $this->resolveUnitPrice($product, $options);

            $taxPct    = $taxEnabled ? (optional($product->tax)->percentage ?? 0) : 0;
            $lineGross = $unitGross * $qty;
            $lineNet   = $taxPct > 0 ? $lineGross / (1 + $taxPct / 100) : $lineGross;
            $lineTax   = $taxPct > 0 ? $lineGross - $lineNet : 0;

            // Build human-readable option labels for display
            $selectedOptionLabels = $this->buildOptionLabels($product, $options);

            $items[] = [
                'cart_key'              => $cartKey,
                'product'               => $product,
                'quantity'              => $qty,
                'options'               => $options,
                'selected_option_labels'=> $selectedOptionLabels,
                'unit_gross'            => $unitGross,
                'line_gross'            => round($lineGross, 2),
                'line_tax'              => round($lineTax, 2),
            ];

            $productsGross += $lineGross;
            $productsTax   += $lineTax;
        }

        return view('cart.index', [
            'items'         => $items,
            'productsGross' => round($productsGross, 2),
            'productsTax'   => round($productsTax, 2),
        ]);
    }

    public function add(AddToCartRequest $request, Product $product)
    {
        if (! $product->active) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => t('store.product_unavailable') ?: 'Product unavailable',
                ], 404);
            }
            abort(404);
        }

        $product->loadMissing(['optionTypes.options.translations']);

        $submittedOptions = $request->input('options', []);

        // Resolve and validate selections
        $resolved = $this->resolveOptions($product, $submittedOptions);
        if (is_string($resolved)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $resolved], 422);
            }
            return back()->with('error', $resolved);
        }

        // Build options map [type_id => option_id] and compute the cart key
        $optionsMap = [];
        foreach ($resolved as $typeId => $option) {
            $optionsMap[$typeId] = $option->id;
        }
        $cartKey = self::makeCartKey($product->id, $optionsMap);

        $cart       = session()->get('cart', []);
        $entry      = $this->normaliseEntry($cart[$cartKey] ?? 0);
        $currentQty = $entry['quantity'];
        $newQty     = $currentQty + $request->quantity;

        // Stock check
        $stockError = $this->checkStock($product, $resolved, $newQty);
        if ($stockError) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $stockError], 422);
            }
            return back()->with('error', $stockError);
        }

        $cart[$cartKey] = ['quantity' => $newQty, 'options' => $optionsMap];
        session()->put('cart', $cart);

        if ($request->headers->get('referer') && ! str_contains($request->headers->get('referer'), '/cart')) {
            session()->put('shopping_return_url', $request->headers->get('referer'));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'cartCount' => $this->cartCount(),
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function update(AddToCartRequest $request, Product $product)
    {
        $product->loadMissing(['optionTypes.options.translations']);

        $submittedOptions = $request->input('options', []);
        $oldCartKey       = $request->input('old_cart_key');

        $resolved = $this->resolveOptions($product, $submittedOptions);
        if (is_string($resolved)) {
            return back()->with('error', $resolved);
        }

        $newQty = $request->quantity;

        $stockError = $this->checkStock($product, $resolved, $newQty);
        if ($stockError) {
            return back()->with('error', $stockError);
        }

        $optionsMap = [];
        foreach ($resolved as $typeId => $option) {
            $optionsMap[$typeId] = $option->id;
        }

        $newCartKey = self::makeCartKey($product->id, $optionsMap);

        $cart = session()->get('cart', []);

        // Remove the old entry if the key has changed (options were modified)
        if ($oldCartKey && $oldCartKey !== $newCartKey) {
            unset($cart[$oldCartKey]);
        }

        $cart[$newCartKey] = ['quantity' => $newQty, 'options' => $optionsMap];
        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        $cartKey = $request->input('cart_key');
        if ($cartKey) {
            $cart = session()->get('cart', []);
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    // ─────────────────────────────────────────────────────────
    // Price helpers (shared with checkout / order placement)
    // ─────────────────────────────────────────────────────────

    /**
     * Resolve the effective unit price for a product given the selected options.
     * If any option type has have_price = true, use the selected option's price.
     * Otherwise fall back to the product's normal price logic.
     */
    public static function resolveUnitPrice(Product $product, array $optionsMap): float
    {
        // $optionsMap = [type_id => option_id]
        foreach ($product->optionTypes as $type) {
            if (! $type->is_active || ! $type->have_price) {
                continue;
            }
            $optionId = $optionsMap[$type->id] ?? null;
            if ($optionId === null) {
                continue;
            }
            $option = $type->options->firstWhere('id', $optionId);
            if ($option && $option->price !== null) {
                // Use option's promo price when product is in promo mode
                if ($product->is_promo && $option->promo_price !== null) {
                    return (float) $option->promo_price;
                }
                return (float) $option->price;
            }
        }

        // Fallback: product-level price
        return (float) ($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price);
    }

    /**
     * Build an array of ['type_name' => string, 'option_name' => string]
     * for display in cart and order views.
     */
    private function buildOptionLabels(Product $product, array $optionsMap): array
    {
        $labels = [];
        foreach ($product->optionTypes as $type) {
            $optionId = $optionsMap[$type->id] ?? null;
            if ($optionId === null) {
                continue;
            }
            $option = $type->options->firstWhere('id', $optionId);
            if (! $option) {
                continue;
            }
            $labels[] = [
                'type_name'   => optional($type->translation())->name ?? '',
                'option_name' => optional($option->translation())->name ?? '',
            ];
        }
        return $labels;
    }
}
