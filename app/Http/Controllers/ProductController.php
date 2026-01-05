<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('active', true)
            ->with(['translations', 'primaryPhoto', 'categories', 'materials']);

        if ($request->filled('name')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->filled('material_id')) {
            $query->whereHas('materials', function ($q) use ($request) {
                $q->where('materials.id', $request->material_id);
            });
        }

        if ($request->filled('is_new')) {
            $query->where('is_new', (bool) $request->is_new);
        }

        if ($request->filled('is_promo')) {
            $query->where('is_promo', (bool) $request->is_promo);
        }

        if ($request->boolean('available')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->paginate(12)->withQueryString();

	$activeProducts = Product::where('active', true)
    		->with(['categories', 'materials'])
    		->get();

	$categoryIds = $activeProducts
    		->pluck('categories')
   		->flatten()
    		->pluck('id')
    		->unique();

	$materialIds = $activeProducts
    		->pluck('materials')
    		->flatten()
    		->pluck('id')
    		->unique();

	$categories = Category::whereIn('id', $categoryIds)
    		->with('translations')
    		->get();

	$materials = Material::whereIn('id', $materialIds)
    		->with('translations')
    		->get();

        return view('products.index', compact('products', 'categories', 'materials'));
    }

    public function show(Product $product)
    {
        abort_if(! $product->active, 404);

        $product->load(['translations', 'photos', 'categories', 'materials']);

        return view('products.show', compact('product'));
    }
}
