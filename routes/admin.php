<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMailController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\ProductOptionController;

Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){

    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');

    //User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/{id}/delete', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/status', [UserController::class, 'toggleStatus'])->name('user.status');

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

    // Product Option
    Route::get('/product/options/{id}', [ProductOptionController::class, 'index'])->name('product.options');
    Route::post('/product-options', [ProductOptionController::class, 'store']);
    Route::get('/product-options/{id}/edit', [ProductOptionController::class, 'edit']);
    Route::post('/product-options/{id}', [ProductOptionController::class, 'update']);
    Route::delete('/product-options/{id}', [ProductOptionController::class, 'destroy'])->name('product-option.destroy');

    // Helper Routes
    Route::get('/product/{productId}/category/{categoryId}/products/{optionId?}', [ProductOptionController::class, 'getCategoryProducts']);

});