<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ProductPhotoController;
use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ShippingTierController;
use App\Http\Controllers\Admin\TaxController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController as ClientProductController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Public (Client)
|--------------------------------------------------------------------------
*/

Route::get('/products', [ClientProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product}', [ClientProductController::class, 'show'])
    ->name('products.show');

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/update/{product}', [CartController::class, 'update'])
    ->name('cart.update');

Route::post('/cart/remove/{product}', [CartController::class, 'remove'])
    ->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Authenticated (Clients + Admins)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    | Profile
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    | Orders (Client)
    */

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    /*
    | Checkout
    */

    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout.index');

    Route::post('/checkout', [OrderController::class, 'place'])
        ->name('checkout.place');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        /*
        | Products
        */

        Route::resource('products', ProductController::class);

        Route::post(
            'products/{product}/photos',
            [ProductPhotoController::class, 'store']
        )->name('products.photos.store');

        Route::post(
            'photos/{photo}/primary',
            [ProductPhotoController::class, 'makePrimary']
        )->name('photos.primary');

        Route::delete(
            'photos/{photo}',
            [ProductPhotoController::class, 'destroy']
        )->name('photos.destroy');

        Route::resource('categories', CategoryController::class);
        Route::resource('materials', MaterialController::class);

        /*
        | Orders (ADMIN) ✅ FIXED
        */

        Route::get('/orders', [AdminOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])
            ->name('orders.update');

        /*
        | Shipping Tiers
        */

        Route::resource('shipping-tiers', ShippingTierController::class);

        /*
        | Taxes
        */

        Route::resource('taxes', TaxController::class)->except(['show']);
    });

require __DIR__ . '/auth.php';
