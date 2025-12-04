<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\ContactMailController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ContentCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ClientReviewController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\FeatureContoller;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductFeatureController;
use App\Http\Controllers\Admin\ProductFaqController;
use App\Http\Controllers\Admin\ProductClientController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductTagController;

Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');
    //User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/{id}/delete', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/status', [UserController::class, 'toggleStatus'])->name('user.status');

    Route::get('/slider', [SliderController::class, 'getSlider'])->name('allslider');
    Route::post('/slider', [SliderController::class, 'sliderStore']);
    Route::get('/slider/{id}/edit', [SliderController::class, 'sliderEdit']);
    Route::post('/slider-update', [SliderController::class, 'sliderUpdate']);
    Route::get('/slider/{id}', [SliderController::class, 'sliderDelete']);
    Route::post('/slider-status', [SliderController::class, 'toggleStatus']);

    Route::get('/service', [ServiceController::class, 'getService'])->name('allservice');
    Route::post('/service', [ServiceController::class, 'serviceStore']);
    Route::get('/service/{id}/edit', [ServiceController::class, 'serviceEdit']);
    Route::post('/service-update', [ServiceController::class, 'serviceUpdate']);
    Route::get('/service/{id}', [ServiceController::class, 'serviceDelete']);
    Route::post('/service-status', [ServiceController::class, 'toggleStatus']);
    Route::post('/remove-file', [ServiceController::class, 'removeFile'])->name('remove.file');

    Route::get('/feature', [FeatureContoller::class, 'getService'])->name('allfeature');
    Route::post('/feature', [FeatureContoller::class, 'serviceStore']);
    Route::get('/feature/{id}/edit', [FeatureContoller::class, 'serviceEdit']);
    Route::post('/feature-update', [FeatureContoller::class, 'serviceUpdate']);
    Route::get('/feature/{id}', [FeatureContoller::class, 'serviceDelete']);
    Route::post('/feature-status', [FeatureContoller::class, 'toggleStatus']);

    Route::get('/client-reviews', [ClientReviewController::class, 'index'])->name('client-reviews.index');
    Route::post('/client-reviews', [ClientReviewController::class, 'store']);
    Route::get('/client-reviews/{id}/edit', [ClientReviewController::class, 'edit']);
    Route::post('/client-reviews/update', [ClientReviewController::class, 'update']);
    Route::get('/client-reviews/{id}', [ClientReviewController::class, 'destroy']);
    Route::post('/client-reviews/status', [ClientReviewController::class, 'toggleStatus'])->name('client-reviews.status');

    Route::get('tags', [TagController::class,'index'])->name('tags.index');
    Route::post('tags', [TagController::class,'store']);
    Route::get('tags/{id}/edit', [TagController::class,'edit']);
    Route::post('tags/update', [TagController::class,'update']);
    Route::get('tags/{id}/delete', [TagController::class,'destroy']);
    Route::post('tags/status', [TagController::class,'toggleStatus']);

    Route::get('/content-category', [ContentCategoryController::class,'index'])->name('content.category.index');
    Route::post('/content-category', [ContentCategoryController::class,'store'])->name('content.category.store');
    Route::get('/content-category/{id}/edit', [ContentCategoryController::class,'edit']);
    Route::post('/content-category-update', [ContentCategoryController::class,'update'])->name('content.category.update');
    Route::get('/content-category/{id}/delete', [ContentCategoryController::class,'destroy']);
    Route::post('/content-category-status', [ContentCategoryController::class,'toggleStatus']);

    Route::get('content/{type}', [ContentController::class,'index'])->name('content.index');
    Route::post('content/{type}', [ContentController::class,'store']);
    Route::get('content/{type}/{id}/edit', [ContentController::class,'edit']);
    Route::post('content/{type}/update', [ContentController::class,'update']);
    Route::get('content/{type}/{id}/delete', [ContentController::class,'delete']);
    Route::post('content/{type}/status', [ContentController::class,'toggleStatus']);

    Route::get('/contact-email', [ContactMailController::class, 'getContactEmail'])->name('allcontactemail');
    Route::post('/contact-email', [ContactMailController::class, 'contactEmailStore']);
    Route::get('/contact-email/{id}/edit', [ContactMailController::class, 'contactEmailEdit']);
    Route::post('/contact-email-update', [ContactMailController::class, 'contactEmailUpdate']);
    Route::get('/contact-email/{id}', [ContactMailController::class, 'contactEmailDelete']);

    Route::get('/faq-questions', [FAQController::class, 'index'])->name('allFaq');    
    Route::post('/faq-questions', [FAQController::class, 'store']);
    Route::get('/faq-questions/{id}/edit', [FAQController::class, 'edit']);
    Route::post('/faq-questions-update', [FAQController::class, 'update']);
    Route::get('/faq-questions/{id}', [FAQController::class, 'delete']);

    // Team Members
    Route::get('/team-members', [TeamMemberController::class, 'index'])->name('team-members.index');
    Route::post('/team-members', [TeamMemberController::class, 'store']);
    Route::get('/team-members/{id}/edit', [TeamMemberController::class, 'edit']);
    Route::post('/team-members/update', [TeamMemberController::class, 'update']);
    Route::get('/team-members/{id}', [TeamMemberController::class, 'destroy']);
    Route::post('/team-members/status', [TeamMemberController::class, 'toggleStatus'])->name('team-members.status');

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::get('/contacts/{id}/delete', [ContactController::class, 'destroy']);
    Route::post('/contacts/status', [ContactController::class, 'toggleStatus'])->name('contacts.status');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::get('/bookings/{id}/delete', [BookingController::class, 'destroy']);
    Route::post('/bookings/status', [BookingController::class, 'toggleStatus'])->name('bookings.status');

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
    
    Route::get('/mail-body', [CompanyDetailsController::class, 'mailBody'])->name('admin.mail-body');
    Route::post('/mail-body', [CompanyDetailsController::class, 'mailBodyUpdate'])->name('admin.mail-body');

    Route::get('/master', [MasterController::class, 'index'])->name('allMaster');
    Route::post('/master', [MasterController::class, 'store']);
    Route::get('/master/{id}/edit', [MasterController::class, 'edit']);
    Route::post('/master-update', [MasterController::class, 'update']);
    Route::get('/master/{id}', [MasterController::class, 'delete']);

    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::post('/sections/update-order', [SectionController::class, 'updateOrder'])->name('sections.updateOrder');
    Route::post('/sections/toggle-status', [SectionController::class, 'toggleStatus'])->name('sections.toggleStatus');

    Route::get('/plans', [PlanController::class, 'index'])->name('allplans');
    Route::post('/plans', [PlanController::class, 'store']);
    Route::get('/plans/{id}/edit', [PlanController::class, 'edit']);
    Route::post('/plans-update', [PlanController::class, 'update']);
    Route::get('/plans/{id}', [PlanController::class, 'destroy']);
    Route::post('/plans-status', [PlanController::class, 'toggleStatus']);

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('allsubscriptions');

    // Category Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('allcategories');
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']);
    Route::post('/categories-update', [CategoryController::class, 'update']);
    Route::get('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::post('/categories-status', [CategoryController::class, 'toggleStatus']);

    // Product Tag Routes
    Route::get('/product-tags', [ProductTagController::class, 'index'])->name('producttags.index');
    Route::post('/product-tags', [ProductTagController::class, 'store'])->name('producttags.store');
    Route::get('/product-tags/{id}/edit', [ProductTagController::class, 'edit'])->name('producttags.edit');
    Route::post('/product-tags-update', [ProductTagController::class, 'update'])->name('producttags.update');
    Route::get('/product-tags/{id}', [ProductTagController::class, 'destroy'])->name('producttags.destroy');
    Route::post('/product-tags-status', [ProductTagController::class, 'toggleStatus'])->name('producttags.status');

    // Product 
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
    Route::post('/products/update', [ProductController::class, 'update']);
    Route::get('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/status', [ProductController::class, 'toggleStatus'])->name('products.status');

    Route::get('/products/{product}/process', [ProductController::class, 'getProcess']);
    Route::post('/products/{product}/process', [ProductController::class, 'saveProcess']) ->name('products.process.save');

    // Product Features
    Route::get('/products/{product}/features', [ProductFeatureController::class, 'index'])->name('products.features.index');
    Route::post('/products/features', [ProductFeatureController::class, 'store']);
    Route::get('/products/features/{id}/edit', [ProductFeatureController::class, 'edit']);
    Route::post('/products/features/update', [ProductFeatureController::class, 'update']);
    Route::get('/products/features/{id}', [ProductFeatureController::class, 'destroy']);
    Route::post('/products/features/status', [ProductFeatureController::class, 'toggleStatus'])->name('products.features.status');

    // Product FAQs
    Route::get('/products/{product}/faqs', [ProductFaqController::class, 'index'])->name('products.faqs.index');
    Route::post('/products/faqs', [ProductFaqController::class, 'store']);
    Route::get('/products/faqs/{id}/edit', [ProductFaqController::class, 'edit']);
    Route::post('/products/faqs/update', [ProductFaqController::class, 'update']);
    Route::get('/products/faqs/{id}', [ProductFaqController::class, 'destroy']);
    Route::post('/products/faqs/status', [ProductFaqController::class, 'toggleStatus'])->name('products.faqs.status');

    // Product Clients Routes
    Route::get('/products/{product}/clients', [ProductClientController::class, 'index'])->name('products.clients.index');
    Route::post('/products/clients', [ProductClientController::class, 'store']);
    Route::get('/products/clients/{id}', [ProductClientController::class, 'destroy']);
    Route::post('/products/clients/status', [ProductClientController::class, 'toggleStatus'])->name('products.clients.status');

        //banner
    Route::get('/banner', [BannerController::class,'index'])->name('banner.index');
    Route::post('/banner', [BannerController::class,'store'])->name('banner.store');
    Route::get('/banner/{id}/edit', [BannerController::class,'edit']);
    Route::post('/banner-update', [BannerController::class,'update'])->name('banner.update');
    Route::get('/banner/{id}/delete', [BannerController::class,'destroy']);
    Route::post('/banner-status', [BannerController::class,'toggleStatus']);
});