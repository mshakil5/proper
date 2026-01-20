<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\UserController;

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'is_user'], 'as' => 'user.'], function () {

    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('dashboard');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    Route::post('/profile', [UserController::class, 'updateProfile'])->name('update-profile');

    Route::get('/password', [UserController::class, 'password'])->name('password');

    Route::post('update-password', [UserController::class, 'updatePassword'])->name('update-password');

    Route::get('/orders', [UserController::class, 'orders'])->name('orders');

    Route::get('/orders/{order}', [UserController::class, 'orderDetails'])->name('orders.details');

    Route::get('/coupons', [UserController::class, 'coupons'])->name('coupons');

    Route::get('/gift-cards', [UserController::class, 'giftCards'])->name('gift-cards');

    Route::get('/points', [UserController::class, 'points'])->name('points');

    Route::get('/social', [UserController::class, 'social'])->name('social');

    Route::post('/social/share', [UserController::class, 'socialShare'])->name('social.share');

    Route::post('/apply-referral', [UserController::class, 'applyReferral'])->name('apply.referral');

});