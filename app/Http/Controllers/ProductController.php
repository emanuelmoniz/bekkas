<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Services\DeliveryDateCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $deliveryCalculator;

    public function __construct(DeliveryDateCalculator $deliveryCalculator)
    {
        $this->deliveryCalculator = $deliveryCalculator;
    }

    public function index(Request $request)
    {
        $query = Product::query()
            ->where('active', true)
            ->with(['translations', 'photos']);

        if ($request->filled('name')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        $categoryIds = array_filter((array) $request->input('category_ids', []));
        if (! empty($categoryIds)) {
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        $materialIds = array_filter((array) $request->input('material_ids', []));
        if (! empty($materialIds)) {
            $query->whereHas('materials', function ($q) use ($materialIds) {
                $q->whereIn('materials.id', $materialIds);
            });
        }

        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        if ($request->boolean('is_promo')) {
            $query->where('is_promo', true);
        }

        if ($request->boolean('available')) {
            $query->where('stock', '>', 0);
        }

        if ($request->filled('price_min')) {
            $min = (float) $request->price_min;
            $query->whereRaw(
                '(CASE WHEN is_promo THEN COALESCE(promo_price, price) ELSE price END) >= ?',
                [$min]
            );
        }

        if ($request->filled('price_max')) {
            $max = (float) $request->price_max;
            $query->whereRaw(
                '(CASE WHEN is_promo THEN COALESCE(promo_price, price) ELSE price END) <= ?',
                [$max]
            );
        }

        // ORDERING
        if ($request->filled('order')) {
            switch ($request->order) {
                case 'name_az':
                case 'name_za':
                    // join product_translations for ordering by name in current locale
                    $dir = $request->order === 'name_az' ? 'asc' : 'desc';
                    $query->join('product_translations as pt', 'products.id', '=', 'pt.product_id')
                          ->where('pt.locale', app()->getLocale())
                          // avoid selecting translation columns in paginator
                          ->select('products.*')
                          ->orderBy('pt.name', $dir);
                    break;
                case 'price_low_high':
                    $query->orderByRaw(
                        '(CASE WHEN is_promo THEN COALESCE(promo_price, price) ELSE price END) asc'
                    );
                    break;
                case 'price_high_low':
                    $query->orderByRaw(
                        '(CASE WHEN is_promo THEN COALESCE(promo_price, price) ELSE price END) desc'
                    );
                    break;
                case 'featured_first':
                    $query->orderByDesc('is_featured');
                    break;
                case 'promo_first':
                    $query->orderByDesc('is_promo');
                    break;
                default:
                    // ignore unknown values
                    break;
            }
        }

        $products = $query->paginate(12)->withQueryString();

        // remember search/filters page so product view can link back
        // only store if it's actually the listing (avoid storing when paginating from other pages)
        session()->put('store_return_url', $request->fullUrl());

        // Build category/material lists with product counts (only from active products)
        $activeProductsForCounts = Product::where('active', true)
            ->with(['categories:id', 'materials:id'])
            ->get(['id']);

        $categoryCounts = [];
        $materialCounts = [];

        foreach ($activeProductsForCounts as $p) {
            foreach ($p->categories as $cat) {
                $categoryCounts[$cat->id] = ($categoryCounts[$cat->id] ?? 0) + 1;
            }
            foreach ($p->materials as $mat) {
                $materialCounts[$mat->id] = ($materialCounts[$mat->id] ?? 0) + 1;
            }
        }

        $categories = Category::whereIn('id', array_keys($categoryCounts))
            ->with('translations')
            ->get();

        $materials = Material::whereIn('id', array_keys($materialCounts))
            ->with('translations')
            ->get();

        // compute the bounds using effective price (promo price overrides)
        $priceFloor = (int) floor(
            Product::where('active', true)
                ->selectRaw('MIN(CASE WHEN is_promo THEN COALESCE(promo_price, price) ELSE price END) as min_price')
                ->value('min_price') ?? 0
        );

        $priceCeiling = (int) ceil(
            Product::where('active', true)
                ->selectRaw('MAX(CASE WHEN is_promo THEN COALESCE(promo_price, price) ELSE price END) as max_price')
                ->value('max_price') ?? 0
        );

        // Get favorite product IDs for the current user
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        } else {
            $favoriteIds = session('favorites', []);
        }

        // Calculate delivery dates for paginated products
        $deliveryDates = [];
        foreach ($products as $product) {
            $deliveryInfo = $this->deliveryCalculator->calculateDeliveryDate($product);
            $deliveryDates[$product->id] = $deliveryInfo['formatted'];
        }

        return view('store.index', compact(
            'products',
            'categories',
            'materials',
            'categoryCounts',
            'materialCounts',
            'priceFloor',
            'priceCeiling',
            'favoriteIds',
            'deliveryDates'
        ));
    }

    public function show(Product $product)
    {
        abort_if(! $product->active, 404);

        $product->load(['translations', 'photos', 'categories', 'materials']);

        // Check if product is in favorites
        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = Auth::user()->favorites()->where('product_id', $product->id)->exists();
        } else {
            $sessionFavorites = session('favorites', []);
            $isFavorite = in_array($product->id, $sessionFavorites);
        }

        // Calculate delivery date
        $deliveryInfo = $this->deliveryCalculator->calculateDeliveryDate($product);
        $deliveryDate = $deliveryInfo['formatted'];

        $backUrl = session('store_return_url', route('store.index'));
        return view('store.show', compact('product', 'isFavorite', 'deliveryDate', 'backUrl'));
    }
}
