<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product-details');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.addToCart');
Route::put('/cart/increase/{rowId}', [CartController::class, 'increaseCartQuantity'])->name('cart.increase-cart-quantity');
Route::put('/cart/decrease/{rowId}', [CartController::class, 'decreaseCartQuantity'])->name('cart.decrease-cart-quantity');


Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group( function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/create', [AdminController::class, 'brand_create'])->name('admin.brand-create');
    Route::post('/admin/brand/store', [AdminController::class, 'brand_store'])->name('admin.brand-store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand-edit');
    Route::put('/admin/brand/update', [AdminController::class, 'brand_update'])->name('admin.brand-update');
    Route::delete('/admin/brand/delete/{id}', [AdminController::class, 'brand_destroy'])->name('admin.brand-destroy');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/create', [AdminController::class, 'category_create'])->name('admin.category-create');
    Route::post('/admin/category/store', [AdminController::class, 'category_store'])->name('admin.category-store');
    Route::get('/admin/category/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category-edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category-update');
    Route::delete('/admin/category/delete/{id}', [AdminController::class, 'category_destroy'])->name('admin.category-destroy');

    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/create', [AdminController::class, 'product_create'])->name('admin.product-create');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product-store');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product-edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product-update');
    Route::delete('/admin/product/delete/{id}', [AdminController::class, 'product_destroy'])->name('admin.product-destroy');
});
