<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;

// cache clear
Route::get('/clear', function() {
  Auth::logout();
  session()->flush();
  Artisan::call('cache:clear');
  Artisan::call('config:clear');
  Artisan::call('config:cache');
  Artisan::call('view:clear');
  return "Cleared!";
});

 Route::fallback(function () {
    return redirect('/');
});

require __DIR__.'/admin.php';
require __DIR__.'/user.php';

Auth::routes();

Route::get('/', [FrontendController::class, 'index'])->name('home');

Route::get('/product/{slug}', [FrontendController::class, 'productDetails'])->name('product.details');

Route::get('/check-delivery', [FrontendController::class, 'checkDelivery'])->name('check-delivery');

Route::get('/get-addresses', [FrontendController::class, 'getAddresses'])->name('get-addresses');

Route::get('/menu', [FrontendController::class, 'menu'])->name('menu');

Route::get('/our-story', [FrontendController::class, 'ourStory'])->name('our-story');

Route::get('/gift-cards', [FrontendController::class, 'giftCards'])->name('gift-cards');

Route::middleware('auth')->group(function () {
    Route::post('/giftcard/checkout', [FrontendController::class, 'giftCardCheckout'])->name('giftcard.checkout');
    Route::get('/giftcard/payment/success', [FrontendController::class, 'giftCardPaymentSuccess'])->name('giftcard.payment.success');
    Route::get('/giftcard/payment/cancel', [FrontendController::class, 'giftCardPaymentCancel'])->name('giftcard.payment.cancel');
});

Route::get('/checkout', [FrontendController::class, 'checkout'])->name('checkout');

Route::post('/validate-promo-code', [FrontendController::class, 'validatePromoCode']);

Route::post('/place-order', [FrontendController::class, 'placeOrder']);

Route::get('/payment/success', [FrontendController::class, 'orderPaymentSuccess'])->name('order.payment.success');

Route::get('/payment/cancel', [FrontendController::class, 'orderPaymentCancel'])->name('order.payment.cancel');

Route::get('/order-confirmation/{orderNumber}', [FrontendController::class, 'orderConfirmation'])->name('order.confirmation');

// For later
// Route::post('/stripe-webhook', [FrontendController::class, 'stripeWebhook'])->name('stripe.webhook');

Route::get('/setup-hubrise-callback', [FrontendController::class, 'setupHubRiseCallback']);

Route::post('/hubrise-webhook', [FrontendController::class, 'hubRiseOrderCallback']);

Route::get('/find-us', [FrontendController::class, 'findUs'])->name('find-us');

Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');

Route::post('/contact', [FrontendController::class, 'storeContact'])->name('contact.store');

Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy');

Route::get('/terms-and-conditions', [FrontendController::class, 'termsAndConditions'])->name('terms-and-conditions');

Route::get('/promotions', [FrontendController::class, 'promotions'])->name('promotions');

Route::get('/sitemap.xml', [FrontendController::class, 'sitemap']);

Route::get('/catalog/facebook', [FrontendController::class, 'facebookCatalog']);

Route::get('/product', [FrontendController::class, 'product']);

Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');