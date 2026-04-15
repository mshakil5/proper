<?php

use App\Http\Controllers\Admin\BlockedCustomerController;
use App\Http\Controllers\Admin\BlockedCustomerOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContactMailController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CredentialController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\GiftcardPackageController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductOptionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){

    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');

    Route::get('/pos', [PosController::class, 'pos'])->name('admin.pos');
    Route::get('/pos/product', [PosController::class, 'posGetProduct'])->name('admin.pos.product');
    Route::post('/pos/quick-customer', [PosController::class, 'posQuickCustomer'])->name('admin.pos.quick-customer');
    Route::get('/pos/customer-info', [PosController::class, 'posGetCustomerInfo'])->name('admin.pos.customer-info');
    Route::post('/pos/validate-promo', [PosController::class, 'posValidatePromo'])->name('admin.pos.validate-promo');
    Route::post('/pos/place-order', [PosController::class, 'posPlaceOrder'])->name('admin.pos.place-order');
    Route::get('/pos/payment/success', [PosController::class, 'posPaymentSuccess'])->name('admin.pos.payment.success');
    Route::get('/pos/payment/cancel', [PosController::class, 'posPaymentCancel'])->name('admin.pos.payment.cancel');

    // Clients
    Route::get('/client', [ClientController::class, 'index'])->name('client.index');
    Route::post('/client', [ClientController::class, 'store'])->name('client.store');
    Route::get('/client/{id}/edit', [ClientController::class, 'edit'])->name('client.edit');
    Route::post('/client/update', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/client/{id}', [ClientController::class, 'destroy'])->name('client.destroy');
    Route::post('/client/toggle-status', [ClientController::class, 'toggleStatus'])->name('client.toggleStatus');
    Route::get('/client/export/csv', [ClientController::class, 'exportClients'])->name('client.export');
    Route::post('/client/import/csv', [ClientController::class, 'importClients'])->name('client.import');

    Route::get('/gift-cards', [ClientController::class, 'giftCards'])->name('gift-cards.index');
    Route::get('/points', [ClientController::class, 'points'])->name('points.index');
    Route::post('/points', [ClientController::class, 'storePoint'])->name('points.store');
    Route::get('/subscriptions', [ClientController::class, 'subscriptions'])->name('subscriptions.index');

    // Company
    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetails');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    Route::get('/company/seo-meta', [CompanyDetailsController::class, 'seoMeta'])->name('admin.company.seo-meta');
    Route::post('/company/seo-meta/update', [CompanyDetailsController::class, 'seoMetaUpdate'])->name('admin.company.seo-meta.update');

    Route::get('/about-us', [CompanyDetailsController::class, 'aboutUs'])->name('admin.aboutUs');
    Route::post('/about-us', [CompanyDetailsController::class, 'aboutUsUpdate'])->name('admin.aboutUs');

    Route::get('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicy'])->name('admin.privacy-policy');
    Route::post('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicyUpdate'])->name('admin.privacy-policy');

    Route::get('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditions'])->name('admin.terms-and-conditions');
    Route::post('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditionsUpdate'])->name('admin.terms-and-conditions');

    Route::get('/promotions', [CompanyDetailsController::class, 'promotions'])->name('admin.promotions');
    Route::post('/promotions', [CompanyDetailsController::class, 'promotionsUpdate'])->name('admin.promotions');

    // FAQ
    Route::get('/faq', [FAQController::class, 'index'])->name('faq.index');
    Route::post('/faq', [FAQController::class, 'store'])->name('faq.store');
    Route::get('/faq/{id}/edit', [FAQController::class, 'edit'])->name('faq.edit');
    Route::post('/faq-update', [FAQController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{id}', [FAQController::class, 'destroy'])->name('faq.delete');

    // Section
    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::post('/sections/update-order', [SectionController::class, 'updateOrder'])->name('sections.updateOrder');
    Route::post('/sections/toggle-status', [SectionController::class, 'toggleStatus'])->name('sections.toggleStatus');

    // Master
    Route::get('/master', [MasterController::class, 'index'])->name('master.index');
    Route::post('/master', [MasterController::class, 'store'])->name('master.store');
    Route::get('/master/{id}/edit', [MasterController::class, 'edit'])->name('master.edit');
    Route::post('/master-update', [MasterController::class, 'update'])->name('master.update');
    Route::delete('/master/{id}', [MasterController::class, 'destroy'])->name('master.delete');

    // Slider
    Route::get('/slider', [SliderController::class, 'getSlider'])->name('allslider');
    Route::post('/slider', [SliderController::class, 'sliderStore']);
    Route::get('/slider/{id}/edit', [SliderController::class, 'sliderEdit']);
    Route::post('/slider-update', [SliderController::class, 'sliderUpdate']);
    Route::delete('/slider/{id}', [SliderController::class, 'sliderDelete'])->name('slider.delete');
    Route::post('/slider-status', [SliderController::class, 'toggleStatus']);
    Route::post('/slider/{id}/remove-image', [SliderController::class, 'removeImage']);
    Route::post('/sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.updateOrder');

    // Contact
    Route::get('/contacts', [ContactController::class,'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [ContactController::class,'show'])->name('contacts.show');
    Route::delete('/contacts/{id}/delete', [ContactController::class,'destroy'])->name('contacts.delete');
    Route::post('/contacts/toggle-status', [ContactController::class,'toggleStatus'])->name('contacts.toggleStatus');

    // Contact Email
    Route::get('/contact-email', [ContactMailController::class, 'index'])->name('contactemail.index');
    Route::post('/contact-email', [ContactMailController::class, 'store']);
    Route::get('/contact-email/{id}/edit', [ContactMailController::class, 'edit']);
    Route::post('/contact-email-update', [ContactMailController::class, 'update']);
    Route::delete('/contact-email/{id}', [ContactMailController::class, 'destroy'])->name('contactemail.destroy');

    // Category
    Route::get('/categories', [CategoryController::class, 'index'])->name('allcategories');
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']);
    Route::post('/categories-update', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories-status', [CategoryController::class, 'toggleStatus']);
    Route::post('/categories-toggle-sidebar', [CategoryController::class, 'toggleSidebar']);
    Route::post('/sort-categories/update', [CategoryController::class, 'updateCategoryOrder'])->name('categories.updateOrder');

    // Tag
    Route::get('/tags', [TagController::class, 'index'])->name('alltags');
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags/{id}/edit', [TagController::class, 'edit']);
    Route::post('/tags-update', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('tag.destroy');
    Route::post('/tags-status', [TagController::class, 'toggleStatus']);

    // Product 
    Route::get('/products', [ProductController::class, 'index'])->name('allproducts');
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
    Route::post('/products-update', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::post('/products-status', [ProductController::class, 'toggleStatus']);
    Route::post('/products-toggle-sidebar', [ProductController::class, 'toggleSidebar']);
    Route::post('/products-toggle-stock', [ProductController::class, 'toggleStockStatus']);
    Route::post('/products/{id}/remove-image', [ProductController::class, 'removeImage']);

    Route::post('/products-reorder', [ProductController::class, 'reorder'])->name('product.reorder');
    Route::get('/products-sort/{category_id}', [ProductController::class, 'sortView'])->name('product.sort');

    // Product Option
    Route::get('/product/options/{id}', [ProductOptionController::class, 'index'])->name('product.options');
    Route::post('/product-options', [ProductOptionController::class, 'store']);
    Route::get('/product-options/{id}/edit', [ProductOptionController::class, 'edit']);
    Route::post('/product-options/{id}', [ProductOptionController::class, 'update']);
    Route::delete('/product-options/{id}', [ProductOptionController::class, 'destroy'])->name('product-option.destroy');
    Route::post('/product-options/{id}/copy', [ProductOptionController::class, 'copy'])->name('product-option.copy');
    Route::post('/product/update-sort', [ProductOptionController::class, 'updateSort'])->name('product.update-sort');

    // Helper Routes
    Route::get('/product/{productId}/category/{categoryId}/products/{optionId?}', [ProductOptionController::class, 'getCategoryProducts']);

    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons-update', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{id}', [CouponController::class, 'delete'])->name('coupons.delete');
    Route::post('/coupons-status', [CouponController::class, 'toggleStatus'])->name('coupons.status');
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon'])->name('coupons.validate');
    Route::get('/coupons/{id}/edit', [CouponController::class, 'edit'])->name('coupons.edit');

    Route::get('/giftcard-packages', [GiftcardPackageController::class, 'index'])->name('giftcard-packages.index');
    Route::post('/giftcard-packages', [GiftcardPackageController::class, 'store'])->name('giftcard-packages.store');
    Route::get('/giftcard-packages/{id}/edit', [GiftcardPackageController::class, 'edit'])->name('giftcard-packages.edit');
    Route::put('/giftcard-packages/{id}', [GiftcardPackageController::class, 'update'])->name('giftcard-packages.update');
    Route::delete('/giftcard-packages/{id}', [GiftcardPackageController::class, 'destroy'])->name('giftcard-packages.destroy');
    Route::post('/giftcard-packages-status', [GiftcardPackageController::class, 'toggleStatus']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.details');

    Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::get('/credentials/{id}/edit', [CredentialController::class, 'edit'])->name('credentials.edit');
    Route::put('/credentials/{id}', [CredentialController::class, 'update'])->name('credentials.update');

    Route::get('/blocked-customers', [BlockedCustomerController::class, 'index'])->name('admin.blocked.index');
    Route::post('/blocked-customers', [BlockedCustomerController::class, 'store'])->name('admin.blocked.store');
    Route::get('/blocked-customers/{id}/edit', [BlockedCustomerController::class, 'edit'])->name('admin.blocked.edit');
    Route::put('/blocked-customers/{id}', [BlockedCustomerController::class, 'update'])->name('admin.blocked.update');
    Route::delete('/blocked-customers/{id}', [BlockedCustomerController::class, 'destroy'])->name('admin.blocked.destroy');

    Route::get('/blocked-orders', [BlockedCustomerOrderController::class, 'index'])->name('admin.blocked-orders.index');
    Route::get('/blocked-orders/{id}', [BlockedCustomerOrderController::class, 'show'])->name('admin.blocked-orders.show');
    Route::delete('/blocked-orders/{id}', [BlockedCustomerOrderController::class, 'destroy'])->name('admin.blocked-orders.destroy');
});