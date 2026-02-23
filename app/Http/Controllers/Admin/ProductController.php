<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Tax;
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
        $request->validate([
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
            'option_types.*.name' => 'sometimes|array',
            'option_types.*.description' => 'sometimes|array',
            'option_types.*.options' => 'sometimes|array',
            'option_types.*.options.*.is_active' => 'sometimes|boolean',
            // stock may legitimately be left empty; treat an empty string as null
            'option_types.*.options.*.stock' => 'sometimes|nullable|integer|min:0',
            'option_types.*.options.*.name' => 'sometimes|array',
            'option_types.*.options.*.description' => 'sometimes|array',
        ]);

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

        foreach (config('app.locales') as $locale => $name) {
            ProductTranslation::create([
                'product_id' => $product->id,
                'locale' => $locale,
                'name' => $request->input("name.$locale"),
                'description' => $request->input("description.$locale"),
                'technical_info' => $request->input("technical_info.$locale"),
            ]);
        }

        $product->categories()->sync($request->categories ?? []);
        $product->materials()->sync($request->materials ?? []);

        // go straight to the edit form so photos/options can be added immediately
        return redirect()->route('admin.products.edit', $product);
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
        $request->validate([
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
            'option_types.*.name' => 'sometimes|array',
            'option_types.*.description' => 'sometimes|array',
            'option_types.*.options' => 'sometimes|array',
            'option_types.*.options.*.is_active' => 'sometimes|boolean',
            // allow blank values here as well; will default to zero when saved
            'option_types.*.options.*.stock' => 'sometimes|nullable|integer|min:0',
            'option_types.*.options.*.name' => 'sometimes|array',
            'option_types.*.options.*.description' => 'sometimes|array',
        ]);

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

        foreach (config('app.locales') as $locale => $name) {
            $product->translations()
                ->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $request->input("name.$locale"),
                        'description' => $request->input("description.$locale"),
                        'technical_info' => $request->input("technical_info.$locale"),
                    ]
                );
        }

        $product->categories()->sync($request->categories ?? []);
        $product->materials()->sync($request->materials ?? []);

        // stay on edit page after update for convenience
        return redirect()->route('admin.products.edit', $product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index');
    }

    /**
     * Create option types & options for a product from the raw array
     * submitted by the form. We don't attempt to patch existing records;
     * instead the caller is expected to delete any previous data before
     * invoking this helper (simpler for our use‑case).
     *
     * @param  Product  $product
     * @param  array    $types
     * @return void
     */
    protected function saveOptionTypes(Product $product, array $types)
    {
        foreach ($types as $typeData) {
            $type = $product->optionTypes()->create([
                'is_active' => isset($typeData['is_active']) ? (bool) $typeData['is_active'] : false,
            ]);

            // translations
            foreach (config('app.locales') as $locale => $label) {
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
                ]);

                foreach (config('app.locales') as $locale => $label) {
                    $opt->translations()->create([
                        'locale' => $locale,
                        'name' => $optData['name'][$locale] ?? null,
                        'description' => $optData['description'][$locale] ?? null,
                    ]);
                }
            }
        }
    }
}
