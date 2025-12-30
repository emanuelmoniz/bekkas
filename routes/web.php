<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ProductPhotoController;

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

        // PRODUCTS
        Route::resource('products', ProductController::class);

        // PRODUCT PHOTOS
        Route::post('products/{product}/photos', [ProductPhotoController::class, 'store'])
            ->name('products.photos.store');

        Route::post('photos/{photo}/primary', [ProductPhotoController::class, 'makePrimary'])
            ->name('photos.primary');

        Route::delete('photos/{photo}', [ProductPhotoController::class, 'destroy'])
            ->name('photos.destroy');

        // CATEGORIES
        Route::resource('categories', CategoryController::class);

        // MATERIALS
        Route::resource('materials', MaterialController::class);
    });

require __DIR__ . '/auth.php';
