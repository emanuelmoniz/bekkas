<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductPhotoController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectPhotoController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ShippingConfigController;
use App\Http\Controllers\Admin\ShippingTierController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\LocaleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController as ClientProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\TicketStatusController;
use Illuminate\Support\Facades\Route;

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

Route::get('/store', [ClientProductController::class, 'index'])
    ->middleware(\App\Http\Middleware\EnsureStoreEnabled::class)
    ->name('store.index');

Route::get('/store/{product}', [ClientProductController::class, 'show'])
    ->middleware(\App\Http\Middleware\EnsureStoreEnabled::class)
    ->name('store.show');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Legal pages
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/custom', function () {
    return view('custom');
})->name('custom.index');

// Portfolio / Projects – public pages
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');

Route::get('/portfolio/{project:uuid}', function () {
    abort(404);
})->name('portfolio.show');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

// Easypay generic notifications (webhook) — server-to-server. Exempt from CSRF and secured via BasicAuth + header.
Route::post('/webhooks/easypay', [\App\Http\Controllers\Webhooks\EasypayController::class, 'generic'])
    ->name('webhooks.easypay')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

Route::get('/cart', [CartController::class, 'index'])
    ->middleware(\App\Http\Middleware\EnsureStoreEnabled::class)
    ->name('cart.index');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->middleware(['throttle:30,1', \App\Http\Middleware\EnsureStoreEnabled::class]) // 30 per minute
    ->name('cart.add');

Route::post('/cart/update/{product}', [CartController::class, 'update'])
    ->middleware(['throttle:30,1', \App\Http\Middleware\EnsureStoreEnabled::class])
    ->name('cart.update');

Route::post('/cart/remove', [CartController::class, 'remove'])
    ->middleware(['throttle:30,1', \App\Http\Middleware\EnsureStoreEnabled::class])
    ->name('cart.remove');

// Favorites
Route::get('/favorites', [FavoriteController::class, 'index'])
    ->name('favorites.index');

Route::post('/favorites/toggle/{product:id}', [FavoriteController::class, 'toggle'])
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

    Route::post('/profile/delete/request', [ProfileController::class, 'sendDeletionLink'])
        ->name('profile.delete.request');

    // Social account linking/unlinking (authenticated)
    $__profile_link_allowed = [];
    if (config('services.google.enabled')) { $__profile_link_allowed[] = 'google'; }
    if (config('services.microsoft.enabled')) { $__profile_link_allowed[] = 'microsoft'; }

    if (! empty($__profile_link_allowed)) {
        Route::get('/profile/social/{provider}/link', [SocialAuthController::class, 'redirectToProvider'])
            ->where('provider', implode('|', $__profile_link_allowed))
            ->name('profile.social.link');
    }

    // Always allow unlinking existing linked providers (even if provider is disabled)
    Route::delete('/profile/social/{provider}', [SocialAuthController::class, 'unlinkProvider'])
        ->where('provider', 'google|microsoft')
        ->name('profile.social.unlink');

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

    // SDK onSuccess endpoint — client SDK posts `checkout`/`payment` info here for persistence
    Route::post('/orders/{order}/pay/verify', [\App\Http\Controllers\EasypayPaymentController::class, 'store'])
        ->name('orders.pay.verify')
        ->middleware('throttle:30,1');

    // SDK error endpoint — SDK client will POST errors (onError/onPaymentError) here for server-side handling
    Route::post('/orders/{order}/pay/sdk-error', [\App\Http\Controllers\EasypayPaymentController::class, 'logSdkError'])
        ->name('orders.pay.sdk_error')
        ->middleware('throttle:30,1');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    /*
    | Checkout
    */

    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->middleware(\App\Http\Middleware\EnsureStoreEnabled::class)
        ->name('checkout.index');

    Route::post('/checkout/shipping-tiers', [OrderController::class, 'getShippingTiers'])
        ->middleware(\App\Http\Middleware\EnsureStoreEnabled::class)
        ->name('checkout.shipping-tiers');

    Route::post('/checkout', [OrderController::class, 'place'])
        ->middleware(['throttle:5,1', \App\Http\Middleware\EnsureStoreEnabled::class]) // 5 per minute (sensitive)
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

Route::get('/profile/delete/confirm/{id}', [ProfileController::class, 'confirmDeletion'])
    ->name('profile.delete.confirm')
    ->middleware('signed');

Route::post('/profile/delete/confirm/{id}', [ProfileController::class, 'performDeletion'])
    ->name('profile.delete.perform')
    ->middleware('signed');

// Test-only helpers for Cypress (only available in local/testing). These
// routes are intentionally non-authenticated and guarded by environment.
if (app()->environment('local', 'testing')) {
    Route::post('/__cypress/seed-order', [\App\Http\Controllers\TestHelpersController::class, 'seedOrder']);
    Route::get('/__cypress/login/{token}', [\App\Http\Controllers\TestHelpersController::class, 'loginWithToken']);
    Route::post('/__cypress/mock-easypay', [\App\Http\Controllers\TestHelpersController::class, 'mockEasypay']);
    Route::get('/__cypress/flash', [\App\Http\Controllers\TestHelpersController::class, 'flash']);
}

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

        // Projects
        Route::resource('projects', ProjectController::class);

        Route::post(
            'projects/{project}/photos',
            [ProjectPhotoController::class, 'store']
        )->name('projects.photos.store');

        Route::post(
            'project-photos/{photo}/primary',
            [ProjectPhotoController::class, 'makePrimary']
        )->name('project-photos.primary');

        Route::delete(
            'project-photos/{photo}',
            [ProjectPhotoController::class, 'destroy']
        )->name('project-photos.destroy');

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
        // Create a payload for an order (admin)
        Route::post('/orders/{order}/payloads', [\App\Http\Controllers\Admin\EasypayPayloadController::class, 'store'])
            ->name('orders.payloads.store');
        Route::delete('/orders/payloads/{payload}', [\App\Http\Controllers\Admin\EasypayPayloadController::class, 'destroy'])
            ->name('orders.payloads.destroy');

        // Easypay checkout sessions (admin)
        Route::get('/orders/checkouts', [\App\Http\Controllers\Admin\EasypayCheckoutSessionController::class, 'index'])
            ->name('orders.checkouts.index');
        Route::get('/orders/checkouts/{session}', [\App\Http\Controllers\Admin\EasypayCheckoutSessionController::class, 'show'])
            ->name('orders.checkouts.show');

        // Admin: create / refresh / cancel checkout sessions via Easypay API
        Route::post('/orders/{order}/checkouts', [\App\Http\Controllers\Admin\EasypayCheckoutSessionController::class, 'store'])
            ->name('orders.checkouts.store');
        Route::post('/orders/checkouts/{session}/refresh', [\App\Http\Controllers\Admin\EasypayCheckoutSessionController::class, 'refresh'])
            ->name('orders.checkouts.refresh');
        Route::post('/orders/checkouts/{session}/cancel', [\App\Http\Controllers\Admin\EasypayCheckoutSessionController::class, 'cancel'])
            ->name('orders.checkouts.cancel');

        // Easypay payments (admin)
        Route::get('/orders/payments', [\App\Http\Controllers\Admin\EasypayPaymentController::class, 'index'])
            ->name('orders.payments.index');
        Route::get('/orders/payments/{payment}', [\App\Http\Controllers\Admin\EasypayPaymentController::class, 'show'])
            ->name('orders.payments.show');
        // Admin: refresh payment details from Easypay (single payment endpoint)
        Route::post('/orders/payments/{payment}/refresh', [\App\Http\Controllers\Admin\EasypayPaymentController::class, 'refresh'])
            ->name('orders.payments.refresh');
        // Admin: refresh refund details from Easypay (refund endpoint)
        Route::post('/orders/payments/{payment}/refund/refresh', [\App\Http\Controllers\Admin\EasypayPaymentController::class, 'refreshRefund'])
            ->name('orders.payments.refund.refresh');
        // Admin: request a refund for a single payment (Easypay)
        Route::post('/orders/payments/{payment}/refund', [\App\Http\Controllers\Admin\EasypayPaymentController::class, 'refund'])
            ->name('orders.payments.refund');

        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])
            ->name('orders.update');

        /*
        | App Configurations (history kept)
        */
        Route::get('/configurations', [ConfigurationController::class, 'index'])
            ->name('configurations.index');
        Route::put('/configurations', [ConfigurationController::class, 'update'])
            ->name('configurations.update');

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

        Route::resource('taxes', TaxController::class);

        /*
        | Countries
        */

        Route::resource('countries', CountryController::class);

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

        /*
        | Locales (Pillar 4)
        */
        Route::resource('locales', LocaleController::class);

        // Static translations — key-grouped admin (Pillar 2)
        Route::get('static-translations', [\App\Http\Controllers\Admin\StaticTranslationController::class, 'index'])
            ->name('static-translations.index');
        Route::get('static-translations/create', [\App\Http\Controllers\Admin\StaticTranslationController::class, 'create'])
            ->name('static-translations.create');
        Route::post('static-translations', [\App\Http\Controllers\Admin\StaticTranslationController::class, 'store'])
            ->name('static-translations.store');
        Route::get('static-translations/{encodedKey}/edit', [\App\Http\Controllers\Admin\StaticTranslationController::class, 'edit'])
            ->name('static-translations.edit')
            ->where('encodedKey', '[A-Za-z0-9_-]+');
        Route::put('static-translations/{encodedKey}', [\App\Http\Controllers\Admin\StaticTranslationController::class, 'update'])
            ->name('static-translations.update')
            ->where('encodedKey', '[A-Za-z0-9_-]+');
        Route::delete('static-translations/{encodedKey}', [\App\Http\Controllers\Admin\StaticTranslationController::class, 'destroy'])
            ->name('static-translations.destroy')
            ->where('encodedKey', '[A-Za-z0-9_-]+');

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

require __DIR__.'/auth.php';
