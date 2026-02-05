<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ProductPhotoController;
use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\ShippingTierController;
use App\Http\Controllers\Admin\ShippingConfigController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController as ClientProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ContactController;

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

Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::get('/products', [ClientProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product}', [ClientProductController::class, 'show'])
    ->name('products.show');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/architecture', function () {
    return view('architecture');
})->name('architecture.index');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->middleware('throttle:30,1') // 30 per minute
    ->name('cart.add');

Route::post('/cart/update/{product}', [CartController::class, 'update'])
    ->middleware('throttle:30,1')
    ->name('cart.update');

Route::post('/cart/remove/{product}', [CartController::class, 'remove'])
    ->middleware('throttle:30,1')
    ->name('cart.remove');

// Favorites
Route::get('/favorites', [FavoriteController::class, 'index'])
    ->name('favorites.index');

Route::post('/favorites/toggle/{product}', [FavoriteController::class, 'toggle'])
    ->middleware('throttle:30,1')
    ->name('favorites.toggle');

Route::post('/favorites/remove/{product}', [FavoriteController::class, 'remove'])
    ->middleware('throttle:30,1')
    ->name('favorites.remove');

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

    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])
        ->name('password.update');

    /*
    | Addresses
    */

    Route::post('/addresses', [AddressController::class, 'store'])
        ->name('addresses.store');

    Route::patch('/addresses/{address}', [AddressController::class, 'update'])
        ->name('addresses.update');

    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])
        ->name('addresses.destroy');

    /*
    | Orders (Client)
    */

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    // Easypay: payment page for orders awaiting payment
    Route::get('/orders/{order}/pay', [OrderController::class, 'pay'])
        ->name('orders.pay');

    // Create a new Easypay checkout session for an order (AJAX)
    Route::post('/orders/{order}/pay/session', [OrderController::class, 'createPaySession'])
        ->name('orders.pay.session');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    /*
    | Checkout
    */

    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout.index');

    Route::post('/checkout/shipping-tiers', [OrderController::class, 'getShippingTiers'])
        ->name('checkout.shipping-tiers');

    Route::post('/checkout', [OrderController::class, 'place'])
        ->middleware('throttle:5,1') // 5 per minute (sensitive)
        ->name('checkout.place');

    /*
    | Tickets (Client)
    */

    Route::get('/tickets', [TicketController::class, 'index'])
        ->name('tickets.index');

    Route::get('/tickets/create', [TicketController::class, 'create'])
        ->name('tickets.create');

    Route::post('/tickets', [TicketController::class, 'store'])
        ->name('tickets.store');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    Route::post('/tickets/{ticket}/mark-unread', [TicketStatusController::class, 'markUnread'])
        ->name('tickets.mark-unread');

    Route::post('/tickets/{ticket}/close', [TicketStatusController::class, 'close'])
        ->name('tickets.close');

    Route::post('/tickets/{ticket}/reopen', [TicketStatusController::class, 'reopen'])
        ->name('tickets.reopen');

    Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])
        ->name('tickets.messages.store');

    Route::get('/tickets/attachments/{attachment}', [TicketAttachmentController::class, 'download'])
        ->name('tickets.attachments.download');
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

        // Easypay payloads (admin)
        Route::get('/orders/payloads', [\App\Http\Controllers\Admin\EasypayPayloadController::class, 'index'])
            ->name('orders.payloads.index');
        Route::get('/orders/payloads/{payload}', [\App\Http\Controllers\Admin\EasypayPayloadController::class, 'show'])
            ->name('orders.payloads.show');

        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])
            ->name('orders.update');

        /*
        | Shipping Config
        */
        Route::get('/shipping-config', [ShippingConfigController::class, 'index'])
            ->name('shipping-config.index');

        Route::put('/shipping-config', [ShippingConfigController::class, 'update'])
            ->name('shipping-config.update');

        /*
        | Order Statuses
        */
        Route::resource('order-statuses', OrderStatusController::class);

        /*
        | Shipping Tiers
        */

        Route::resource('shipping-tiers', ShippingTierController::class);
        Route::post('shipping-tiers/regions', [ShippingTierController::class, 'getRegions'])
            ->name('shipping-tiers.get-regions');
        Route::post('shipping-tiers/{shippingTier}/duplicate', [ShippingTierController::class, 'duplicate'])
            ->name('shipping-tiers.duplicate');

        /*
        | Taxes
        */

        Route::resource('taxes', TaxController::class)->except(['show']);

        /*
        | Countries
        */

        Route::resource('countries', CountryController::class)->except(['show']);

        /*Regions
        */

        Route::resource('regions', RegionController::class);

        /*
        | 
        | Users
        */

        Route::get('users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::get('users/create', [AdminUserController::class, 'create'])
            ->name('users.create');

        Route::post('users', [AdminUserController::class, 'store'])
            ->name('users.store');

        Route::get('users/{user}', [AdminUserController::class, 'show'])
            ->name('users.show');

        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])
            ->name('users.edit');

        Route::patch('users/{user}', [AdminUserController::class, 'update'])
            ->name('users.update');

        Route::patch('users/{user}/addresses/{address}', [AdminUserController::class, 'updateAddress'])
            ->name('users.addresses.update');

        Route::post('users/{user}/addresses', [AdminUserController::class, 'createAddress'])
            ->name('users.addresses.store');

        /*
        | Tickets (Admin)
        */

        Route::resource('ticket-categories', TicketCategoryController::class);

        // Static translations (DB-driven)
        Route::resource('static-translations', \App\Http\Controllers\Admin\StaticTranslationController::class)->except(['show']);

        Route::get('tickets/create', [TicketAdminController::class, 'create'])
            ->name('tickets.create');

        Route::post('tickets', [TicketAdminController::class, 'store'])
            ->name('tickets.store');

        Route::get('tickets', [TicketAdminController::class, 'index'])
            ->name('tickets.index');

        Route::get('tickets/{ticket}', [TicketAdminController::class, 'show'])
            ->name('tickets.show');

        Route::post('tickets/{ticket}/mark-unread', [TicketStatusController::class, 'markUnread'])
            ->name('tickets.mark-unread');

        Route::get('tickets/{ticket}/edit', [TicketAdminController::class, 'edit'])
            ->name('tickets.edit');

        Route::patch('tickets/{ticket}', [TicketAdminController::class, 'update'])
            ->name('tickets.update');
    });

require __DIR__ . '/auth.php';
