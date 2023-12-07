<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\OrderController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('product', ProductController::class);
    Route::resource('order', OrderController::class);
    Route::resource('service', ServiceController::class);
    Route::resource('booking', BookingController::class);
    Route::put('booking/{id}/{status}', [BookingController::class, 'updateStatus'])->name('booking.updateBookingStatus');

});
