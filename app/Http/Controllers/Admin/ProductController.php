<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('translations')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('translations')->get();
        $materials  = Material::with('translations')->get();

        return view('admin.products.create', compact('categories', 'materials'));
    }

    public function store(Request $request)
    {
        $product = Product::create($request->only([
            'is_new','is_promo','price','promo_price','tax',
            'width','length','height','weight','stock','active'
        ]));

        foreach (['pt-PT', 'en-UK'] as $locale) {
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
    $materials  = Material::with('translations')->get();

    return view('admin.products.edit', compact('product', 'categories', 'materials'));
}

public function update(Request $request, Product $product)
{
    $product->update($request->only([
        'is_new','is_promo','price','promo_price','tax',
        'width','length','height','weight','stock','active'
    ]));

    foreach (['pt-PT', 'en-UK'] as $locale) {
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
