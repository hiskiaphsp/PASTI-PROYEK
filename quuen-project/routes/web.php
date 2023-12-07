<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
require __DIR__.'/admin.php';
Route::group(['middleware' => 'api'],function(){
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.index');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/viewsession', [AuthController::class, 'viewSession'])->name('auth.viewSession');
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::resource('product', ProductController::Class);
    Route::resource('service', ServiceController::Class);
    Route::resource('booking', BookingController::Class);
    Route::put('booking/{id}/{status}', [BookingController::class, 'updateStatus'])->name('booking.updateBookingStatus');

    Route::prefix('product')->name('product.')->group(function () {
        Route::post('/add-to-cart', [ProductController::class, 'addToCart'])->name('addToCart');
        Route::post('/remove-cart', [ProductController::class, 'removeFromCart'])->name('removeCart');
        Route::get('/cart/load', [ProductController::class, 'loadCart'])->name('loadCart');
        Route::get('cart', [CartController::class, 'index']);
    });

    Route::get('order', [OrderController::class, 'index'])->name('order.index');
});
