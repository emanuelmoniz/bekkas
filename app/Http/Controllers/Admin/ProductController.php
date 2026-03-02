<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Locale;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Tax;
use App\Services\ImageThumbnailService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('translations');

        // NAME (translation)
        if ($request->filled('name')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.trim($request->name).'%');
            });
        }

        // STOCK (single field semantic logic)
        if ($request->filled('stock')) {
            $stock = (int) $request->stock;

            if ($stock === 0) {
                $query->where('stock', 0);
            } else {
                $query->where('stock', '>=', $stock);
            }
        }

        // CATEGORY
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // MATERIAL
        if ($request->filled('material_id')) {
            $query->whereHas('materials', function ($q) use ($request) {
                $q->where('materials.id', $request->material_id);
            });
        }

        // FEATURED (previously "new")
        if ($request->filled('is_featured')) {
            $query->where('is_featured', (bool) $request->is_featured);
        }

        // PROMO
        if ($request->filled('is_promo')) {
            $query->where('is_promo', (bool) $request->is_promo);
        }

        // ACTIVE
        if ($request->filled('active')) {
            $query->where('active', (bool) $request->active);
        }

        $products = $query->paginate(20)->withQueryString();

        $categories = Category::with('translations')->get();
        $materials = Material::with('translations')->get();

        return view('admin.products.index', compact(
            'products',
            'categories',
            'materials'
        ));
    }

    public function create()
    {
        $categories = Category::with('translations')->get();
        $materials = Material::with('translations')->get();
        $taxes = Tax::where('is_active', true)->orderBy('percentage')->get();

        // we don't need to pass option types on create – the form will be empty
        return view('admin.products.create', compact(
            'categories',
            'materials',
            'taxes'
        ));
    }

    public function store(Request $request)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = ["name.{$defaultLocale}" => 'required|string|max:255'];
        foreach (Locale::activeCodes() as $locale) {
            if ($locale !== $defaultLocale) {
                $nameRules["name.{$locale}"] = 'nullable|string|max:255';
            }
        }
        $request->validate(array_merge($nameRules, [
            'tax_id' => 'required|exists:taxes,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'production_time' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',

            // translations are optional but when provided must be an array
            'description' => 'sometimes|array',
            'technical_info' => 'sometimes|array',

            // option-types are optional; if present validate structure
            'option_types' => 'sometimes|array',
            'option_types.*.is_active' => 'sometimes|boolean',
            'option_types.*.have_stock' => 'sometimes|boolean',
            'option_types.*.have_price' => 'sometimes|boolean',
            'option_types.*.name' => 'sometimes|array',
            'option_types.*.description' => 'sometimes|array',
            'option_types.*.options' => 'sometimes|array',
            'option_types.*.options.*.is_active' => 'sometimes|boolean',
            // stock may legitimately be left empty; treat an empty string as null
            'option_types.*.options.*.stock' => 'sometimes|nullable|integer|min:0',
            'option_types.*.options.*.price' => 'sometimes|nullable|numeric|min:0',
            'option_types.*.options.*.promo_price' => 'sometimes|nullable|numeric|min:0',
            'option_types.*.options.*.name' => 'sometimes|array',
            'option_types.*.options.*.description' => 'sometimes|array',
        ]));

        $this->validateOptionTypeFlags($request->input('option_types', []));

        $product = Product::create([
            'tax_id' => $request->tax_id,
            'price' => $request->price,
            'promo_price' => $request->promo_price,
            'stock' => $request->stock,
            'production_time' => $request->production_time ?? 0,
            'width' => $request->width,
            'length' => $request->length,
            'height' => $request->height,
            'weight' => $request->weight,

            // BOOLEAN NORMALIZATION
            'is_backorder' => $request->boolean('is_backorder', true),
            'is_featured' => $request->boolean('is_featured'),
            'is_promo' => $request->boolean('is_promo'),
            'active' => $request->boolean('active'),
        ]);

        // persist any option types that were submitted along with their options
        $this->saveOptionTypes($product, $request->input('option_types', []));

        foreach (Locale::activeList() as $locale => $locLabel) {
            $nameValue = $request->input("name.$locale");
            if (! empty($nameValue)) {
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'locale' => $locale,
                    'name' => $nameValue,
                    'description' => $request->input("description.$locale"),
                    'technical_info' => $request->input("technical_info.$locale"),
                ]);
            }
        }

        $product->categories()->sync($request->categories ?? []);
        $product->materials()->sync($request->materials ?? []);

        // go straight to the edit form so photos/options can be added immediately
        return redirect()->route('admin.products.edit', $product);
    }

    public function show(Product $product)
    {
        $product->load(['translations', 'categories.translations', 'materials.translations', 'photos', 'tax']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load([
            'translations',
            'categories',
            'materials',
            'optionTypes.translations',
            'optionTypes.options.translations',
        ]);

        $categories = Category::with('translations')->get();
        $materials = Material::with('translations')->get();
        $taxes = Tax::where('is_active', true)->orderBy('percentage')->get();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'materials',
            'taxes'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = ["name.{$defaultLocale}" => 'required|string|max:255'];
        foreach (Locale::activeCodes() as $locale) {
            if ($locale !== $defaultLocale) {
                $nameRules["name.{$locale}"] = 'nullable|string|max:255';
            }
        }
        $request->validate(array_merge($nameRules, [
            'tax_id' => 'required|exists:taxes,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'production_time' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',

            'option_types' => 'sometimes|array',
            'option_types.*.is_active' => 'sometimes|boolean',
            'option_types.*.have_stock' => 'sometimes|boolean',
            'option_types.*.have_price' => 'sometimes|boolean',
            'option_types.*.name' => 'sometimes|array',
            'option_types.*.description' => 'sometimes|array',
            'option_types.*.options' => 'sometimes|array',
            'option_types.*.options.*.is_active' => 'sometimes|boolean',
            // allow blank values here as well; will default to zero when saved
            'option_types.*.options.*.stock' => 'sometimes|nullable|integer|min:0',
            'option_types.*.options.*.price' => 'sometimes|nullable|numeric|min:0',
            'option_types.*.options.*.promo_price' => 'sometimes|nullable|numeric|min:0',
            'option_types.*.options.*.name' => 'sometimes|array',
            'option_types.*.options.*.description' => 'sometimes|array',
        ]));

        $this->validateOptionTypeFlags($request->input('option_types', []));

        $product->update([
            'tax_id' => $request->tax_id,
            'price' => $request->price,
            'promo_price' => $request->promo_price,
            'stock' => $request->stock,
            'production_time' => $request->production_time ?? 0,
            'width' => $request->width,
            'length' => $request->length,
            'height' => $request->height,
            'weight' => $request->weight,

            // ✅ BOOLEAN NORMALIZATION
            'is_backorder' => $request->boolean('is_backorder'),
            'is_featured' => $request->boolean('is_featured'),
            'is_promo' => $request->boolean('is_promo'),
            'active' => $request->boolean('active'),
        ]);

        // wipe and re‑create associated option types/options so the simple
        // form structure can just send the complete set each time
        $product->optionTypes()->each(function ($t) {
            $t->options()->delete();
            $t->translations()->delete();
        });
        $product->optionTypes()->delete();

        $this->saveOptionTypes($product, $request->input('option_types', []));

        foreach (Locale::activeList() as $locale => $locLabel) {
            $nameValue = $request->input("name.$locale");
            if (! empty($nameValue)) {
                $product->translations()
                    ->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $nameValue,
                            'description' => $request->input("description.$locale"),
                            'technical_info' => $request->input("technical_info.$locale"),
                        ]
                    );
            }
        }

        $product->categories()->sync($request->categories ?? []);
        $product->materials()->sync($request->materials ?? []);

        // stay on edit page after update for convenience
        return redirect()->route('admin.products.edit', $product);
    }

    public function destroy(Product $product, ImageThumbnailService $thumbnails)
    {
        // explicit cleanup here mirrors the model event defined on
        // Product; having both ensures that even if the event ever
        // misfires the controller still tidies up storage.
        foreach ($product->photos as $photo) {
            $thumbnails->delete($photo->path, $photo->original_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index');
    }

    /**
     * Create option types & options for a product from the raw array
     * submitted by the form. We don't attempt to patch existing records;
     * instead the caller is expected to delete any previous data before
     * invoking this helper (simpler for our use‑case).
     */
    protected function saveOptionTypes(Product $product, array $types): void
    {
        foreach ($types as $typeData) {
            $haveStock = isset($typeData['have_stock']) ? (bool) $typeData['have_stock'] : false;
            $havePrice = isset($typeData['have_price']) ? (bool) $typeData['have_price'] : false;

            $type = $product->optionTypes()->create([
                'is_active' => isset($typeData['is_active']) ? (bool) $typeData['is_active'] : false,
                'have_stock' => $haveStock,
                'have_price' => $havePrice,
            ]);

            // translations
            foreach (Locale::activeList() as $locale => $label) {
                $type->translations()->create([
                    'locale' => $locale,
                    'name' => $typeData['name'][$locale] ?? null,
                    'description' => $typeData['description'][$locale] ?? null,
                ]);
            }

            // options nested within this type
            foreach ($typeData['options'] ?? [] as $optData) {
                $opt = $type->options()->create([
                    'is_active' => isset($optData['is_active']) ? (bool) $optData['is_active'] : false,
                    'stock' => isset($optData['stock']) ? (int) $optData['stock'] : 0,
                    // Only persist price/promo_price when the parent type has have_price
                    'price' => $havePrice && isset($optData['price']) ? $optData['price'] : null,
                    'promo_price' => $havePrice && isset($optData['promo_price']) ? $optData['promo_price'] : null,
                ]);

                foreach (Locale::activeList() as $locale => $label) {
                    $opt->translations()->create([
                        'locale' => $locale,
                        'name' => $optData['name'][$locale] ?? null,
                        'description' => $optData['description'][$locale] ?? null,
                    ]);
                }
            }
        }
    }

    /**
     * Assert at most one option type per product carries have_stock = true
     * and at most one carries have_price = true. Throws a ValidationException
     * on violation so the user receives a proper form error.
     */
    protected function validateOptionTypeFlags(array $types): void
    {
        $stockCount = 0;
        $priceCount = 0;

        foreach ($types as $typeData) {
            if (! empty($typeData['have_stock'])) {
                $stockCount++;
            }
            if (! empty($typeData['have_price'])) {
                $priceCount++;
            }
        }

        $errors = [];

        if ($stockCount > 1) {
            $errors['option_types'] = ['Only one option type per product may have stock control enabled.'];
        }

        if ($priceCount > 1) {
            $errors['option_types'] = array_merge(
                $errors['option_types'] ?? [],
                ['Only one option type per product may have price control enabled.']
            );
        }

        if (! empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }
}
