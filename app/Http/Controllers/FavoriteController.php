<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Material;
use App\Models\Product;
use App\Services\DeliveryDateCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    protected $deliveryCalculator;

    public function __construct(DeliveryDateCalculator $deliveryCalculator)
    {
        $this->deliveryCalculator = $deliveryCalculator;
    }

    public function index(Request $request)
    {
        // Get favorites from session or database
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        } else {
            $favoriteIds = session('favorites', []);
        }

        // Build query with filters (same as products page)
        $query = Product::whereIn('id', $favoriteIds)
            ->where('active', true)
            ->with(['translations', 'photos', 'categories', 'materials']);

        // Name search filter
        if ($request->filled('name')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Material filter
        if ($request->filled('material_id')) {
            $query->whereHas('materials', function ($q) use ($request) {
                $q->where('materials.id', $request->material_id);
            });
        }

        // Featured filter (was "new")
        if ($request->filled('is_featured')) {
            $query->where('is_featured', (bool) $request->is_featured);
        }

        // Promo filter
        if ($request->filled('is_promo')) {
            $query->where('is_promo', (bool) $request->is_promo);
        }

        // Available filter
        if ($request->boolean('available')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->paginate(12)->withQueryString();

        // Get filter options
        $categories = Category::whereHas('products', function ($q) use ($favoriteIds) {
            $q->whereIn('products.id', $favoriteIds)->where('active', true);
        })->with('translations')->get();

        $materials = Material::whereHas('products', function ($q) use ($favoriteIds) {
            $q->whereIn('products.id', $favoriteIds)->where('active', true);
        })->with('translations')->get();

        // Build category/material counts scoped to favorite products
        $categoryCounts = [];
        $materialCounts = [];
        $favoriteProductsForCounts = Product::whereIn('id', $favoriteIds)
            ->where('active', true)
            ->with(['categories:id', 'materials:id'])
            ->get(['id']);

        foreach ($favoriteProductsForCounts as $p) {
            foreach ($p->categories as $cat) {
                $categoryCounts[$cat->id] = ($categoryCounts[$cat->id] ?? 0) + 1;
            }
            foreach ($p->materials as $mat) {
                $materialCounts[$mat->id] = ($materialCounts[$mat->id] ?? 0) + 1;
            }
        }

        $hasFavorites = ! empty($favoriteIds);

        // Calculate delivery dates for products
        $deliveryDates = [];
        foreach ($products as $product) {
            $deliveryInfo = $this->deliveryCalculator->calculateDeliveryDate($product);
            $deliveryDates[$product->id] = $deliveryInfo['formatted'];
        }

        return view('favorites.index', compact('products', 'categories', 'materials', 'categoryCounts', 'materialCounts', 'hasFavorites', 'deliveryDates', 'favoriteIds'));
    }

    public function toggle(Request $request, Product $product)
    {
        if (Auth::check()) {
            // User is logged in - use database
            $favorite = Favorite::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                $isFavorite = false;
            } else {
                Favorite::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ]);
                $isFavorite = true;
            }
        } else {
            // Guest user - use session
            $favorites = session('favorites', []);

            if (in_array($product->id, $favorites)) {
                $favorites = array_diff($favorites, [$product->id]);
                $isFavorite = false;
            } else {
                $favorites[] = $product->id;
                $isFavorite = true;
            }

            session(['favorites' => array_values($favorites)]);
        }

        // Get updated favorites count
        $favoritesCount = Auth::check()
            ? Auth::user()->favorites()->count()
            : count(session('favorites', []));

        if ($request->expectsJson()) {
            return response()->json([
                'isFavorite' => $isFavorite,
                'favoritesCount' => $favoritesCount,
            ]);
        }

        return back();
    }

    public function remove(Product $product)
    {
        if (Auth::check()) {
            Favorite::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();
        } else {
            $favorites = session('favorites', []);
            $favorites = array_diff($favorites, [$product->id]);
            session(['favorites' => array_values($favorites)]);
        }

        return back()->with('success', t('favorites.removed') ?: 'Product removed from favorites.');
    }

    public static function mergeFavoritesOnLogin($userId)
    {
        $sessionFavorites = session('favorites', []);

        if (! empty($sessionFavorites)) {
            foreach ($sessionFavorites as $productId) {
                Favorite::firstOrCreate([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ]);
            }

            session()->forget('favorites');
        }
    }
}
