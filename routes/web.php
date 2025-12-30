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

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/', [FrontendController::class, 'index'])->name('home');

Route::get('/product/{slug}', [FrontendController::class, 'productDetails'])->name('product.details');

Route::get('/menu', [FrontendController::class, 'menu'])->name('menu');

Route::get('/our-story', [FrontendController::class, 'ourStory'])->name('our-story');

Route::get('/checkout', [FrontendController::class, 'checkout'])->name('checkout');

Route::post('/place-order', [FrontendController::class, 'placeOrder'])->name('checkout.place-order');

Route::get('/find-us', [FrontendController::class, 'findUs'])->name('find-us');

Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');

Route::post('/contact', [FrontendController::class, 'storeContact'])->name('contact.store');

Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy');

Route::get('/terms-and-conditions', [FrontendController::class, 'termsAndConditions'])->name('terms-and-conditions');

Route::get('/frequently-asked-questions', [FrontendController::class, 'frequentlyAskedQuestions'])->name('faq');

Route::get('/sitemap.xml', [FrontendController::class, 'sitemap']);

Route::get('/product', [FrontendController::class, 'product']);

Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user']], function(){
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');
});