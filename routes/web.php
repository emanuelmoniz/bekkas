<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ProductPhotoController;
use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ShippingTierController;
use App\Http\Controllers\Admin\TaxController;

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\TicketAttachmentController;

use App\Http\Controllers\AddressController;

use App\Http\Controllers\OrderController;

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
| Authenticated (Clients + Admins)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |------------------------
    | Profile
    |------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |------------------------
    | Tickets (Client & Admin)
    |------------------------
    */

    Route::get('/tickets', [TicketController::class, 'index'])
        ->name('tickets.index');

    Route::get('/tickets/create', [TicketController::class, 'create'])
        ->name('tickets.create');

    Route::post('/tickets', [TicketController::class, 'store'])
        ->name('tickets.store');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    Route::post('/tickets/{ticket}/messages',
        [TicketMessageController::class, 'store']
    )->name('tickets.messages.store');

    Route::post('/tickets/{ticket}/close',
        [TicketStatusController::class, 'close']
    )->name('tickets.close');

    Route::post('/tickets/{ticket}/reopen',
        [TicketStatusController::class, 'reopen']
    )->name('tickets.reopen');

    Route::post('/tickets/{ticket}/mark-unread', function (\App\Models\Ticket $ticket) {
        $user = auth()->user();

        if (! $user->hasRole('admin') && $ticket->user_id !== $user->id) {
            abort(403);
        }

        $ticket->markAsUnread($user->id);

        return redirect()->route('tickets.index');
    })->name('tickets.mark-unread');

    Route::get('/tickets/attachments/{attachment}',
        [TicketAttachmentController::class, 'download']
    )->name('tickets.attachments.download');

    /*
    |------------------------
    | Addresses
    |------------------------
    */

    Route::post('/addresses', [AddressController::class, 'store'])
	->name('addresses.store');
    Route::patch('/addresses/{address}', [AddressController::class, 'update'])
	->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])
	->name('addresses.destroy');

    /*
    |------------------------
    | Orders
    |------------------------
    */

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

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
        |------------------------
        | Users
        |------------------------
        */

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->only(['index', 'show', 'update']);

        /*
        |------------------------
        | Products (already implemented)
        |------------------------
        */

        Route::resource('products', ProductController::class);

        Route::post('products/{product}/photos',
            [ProductPhotoController::class, 'store']
        )->name('products.photos.store');

        Route::post('photos/{photo}/primary',
            [ProductPhotoController::class, 'makePrimary']
        )->name('photos.primary');

        Route::delete('photos/{photo}',
            [ProductPhotoController::class, 'destroy']
        )->name('photos.destroy');

        Route::resource('categories', CategoryController::class);
        Route::resource('materials', MaterialController::class);

        /*
        |------------------------
        | Ticket Admin Actions
        |------------------------
        */

        Route::get('/tickets/{ticket}/edit',
            [TicketAdminController::class, 'edit']
        )->name('tickets.edit');

        Route::post('/tickets/{ticket}',
            [TicketAdminController::class, 'update']
        )->name('tickets.update');

        /*
        |------------------------
        | Ticket Categories 
        |------------------------
        */

        Route::resource('ticket-categories', TicketCategoryController::class)
            ->except(['show']);

        /*
        |------------------------
        | Orders
        |------------------------
        */

        Route::get('/orders', [AdminOrderController::class, 'index'])
            ->name('admin.orders.index');

        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('admin.orders.show');

        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])
            ->name('admin.orders.update');

        /*
        |------------------------
        | Shipping Tiers
        |------------------------
        */

	Route::get('/shipping-tiers', [ShippingTierController::class, 'index'])
    		->name('shipping-tiers.index');

	Route::get('/shipping-tiers/create', [ShippingTierController::class, 'create'])
    		->name('shipping-tiers.create');

	Route::post('/shipping-tiers', [ShippingTierController::class, 'store'])
    		->name('shipping-tiers.store');

	Route::get('/shipping-tiers/{shippingTier}/edit', [ShippingTierController::class, 'edit'])
    		->name('shipping-tiers.edit');

	Route::patch('/shipping-tiers/{shippingTier}', [ShippingTierController::class, 'update'])
    		->name('shipping-tiers.update');

	Route::delete('/shipping-tiers/{shippingTier}', [ShippingTierController::class, 'destroy'])
    		->name('shipping-tiers.destroy');

        /*
        |------------------------
        | Taxes
        |------------------------
        */

	Route::get('/taxes', [TaxController::class, 'index'])
    		->name('taxes.index');

	Route::get('/taxes/create', [TaxController::class, 'create'])
    		->name('taxes.create');

	Route::post('/taxes', [TaxController::class, 'store'])
    		->name('taxes.store');

	Route::get('/taxes/{tax}/edit', [TaxController::class, 'edit'])
    		->name('taxes.edit');

	Route::patch('/taxes/{tax}', [TaxController::class, 'update'])
    		->name('taxes.update');


    });

require __DIR__ . '/auth.php';
