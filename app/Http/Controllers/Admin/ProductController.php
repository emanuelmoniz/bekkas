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

        // NEW
        if ($request->filled('is_new')) {
            $query->where('is_new', (bool) $request->is_new);
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
            'is_new' => $request->boolean('is_new'),
            'is_promo' => $request->boolean('is_promo'),
            'active' => $request->boolean('active'),
        ]);

        foreach (config('app.locales') as $locale => $name) {
            ProductTranslation::create([
                'product_id' => $product->id,
                'locale' => $locale,
                'name' => $request->input("name.$locale"),
                'description' => $request->input("description.$locale"),
            ]);
        }

        $product->categories()->sync($request->categories ?? []);
        $product->materials()->sync($request->materials ?? []);

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        $product->load(['translations', 'categories', 'materials']);

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
            'is_new' => $request->boolean('is_new'),
            'is_promo' => $request->boolean('is_promo'),
            'active' => $request->boolean('active'),
        ]);

        foreach (config('app.locales') as $locale => $name) {
            $product->translations()
                ->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $request->input("name.$locale"),
                        'description' => $request->input("description.$locale"),
                    ]
                );
        }

        $product->categories()->sync($request->categories ?? []);
        $product->materials()->sync($request->materials ?? []);

        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index');
    }
}
