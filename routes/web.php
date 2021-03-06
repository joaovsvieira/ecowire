<?php

use App\Http\Controllers\CartIndexController;
use App\Http\Controllers\CategoryShowController;
use App\Http\Controllers\CheckoutIndexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderConfirmationIndexController;
use App\Http\Controllers\OrderIndexController;
use App\Http\Controllers\ProductShowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/cart', CartIndexController::class)->name('cart');
Route::get('/checkout', CheckoutIndexController::class);
Route::get('/orders/{order:uuid}/confirmation', OrderConfirmationIndexController::class)->name('orders.confirmation');
Route::get('/orders', OrderIndexController::class)->name('orders');
Route::get('/categories/{category:slug}', CategoryShowController::class);
Route::get('/products/{product:slug}', ProductShowController::class);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
