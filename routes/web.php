<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Routes that don't require authentication
|
*/

// Main Home Page
Route::get('/', function () {
    $product = Product::all();
    return view('home', compact('product'));
})->name('home');

//Menu Pages Routes
Route::get('/shop-page', [UserController::class, 'view_shop'])->name('shop-page');
Route::get('/cart', [ShopController::class, 'cart_page'])->name('cart');
Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/aboutus', function () {
    return view('aboutus');
})->name('aboutus');

Route::get('/contact', function () {
    return view('contact');
})->name('view.contact');
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Include Laravel's default authentication routes
|
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| User Panel Routes (Authenticated Users Only)
|--------------------------------------------------------------------------
| Routes that require user authentication
|
*/
    Route::middleware(['auth'])->group(function () {
    Route::get('/homepage', [UserController::class, 'index'])->name('userdash');
        
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (CRUD operation for product management)
|--------------------------------------------------------------------------
|
*/
    Route::middleware(['auth', \App\Http\Middleware\Admin::class])->group(function () {
    // Admin Dashboard
    Route::get('/admin-dashboard', function () {
        return view('AdminPanel.products.index');
    })->name('admin.dashboard');
    
    Route::get('/products', function () {
    return view('AdminPanel.products.index');
    })->name('admin.index');

    Route::get('/manage-product', function () {
    return view('AdminPanel.products.manage');
    })->name('admin.manage-product');
    Route::get('/create-new', function () {
    return view('AdminPanel.products.create');
    })->name('admin.create_new');
    
    Route::get('/view_category', [AdminController::class, 'view_category'])->name('admin.categories');
    Route::post('/view_category', [AdminController::class, 'add_category'])->name('admin.categories');
    Route::get('/delete_category/{id}', [AdminController::class, 'delete_category'])->name('admin.delete-category');

    Route::get('/manage_product', [AdminController::class, 'manage_product'])->name('admin.manage-product');
    Route::get('/add_product', [AdminController::class, 'add_product'])->name('admin.add-product');
    Route::post('/add_product', [AdminController::class, 'store_product'])->name('admin.store-product');
    Route::get('/show_product', [AdminController::class, 'show_product'])->name('admin.show-product');
    Route::get('/update_product/{id}', [AdminController::class, 'update_product'])->name('admin.update-product');
    Route::post('/update_confirmation/{id}', [AdminController::class, 'update_confirmation'])->name('admin.update-changes');
    Route::get('/delete_product/{id}', [AdminController::class, 'delete_product'])->name('admin.delete-product');
    });

    /*
    |--------------------------------------------------------------------------
    | Shop Routes (Displaying Products, Categories, and Prodcut Details)
    |--------------------------------------------------------------------------
    |
    */
    Route::get('/home', [ShopController::class, 'shop_page'])->name('home');
    Route::get('/product-details/{id}', [ShopController::class, 'item_details'])->name('product-details');
    // Add to cart
    Route::post('/add-to-cart/{id}', [ShopController::class, 'add_to_cart'])->name('cart-page');
    Route::get('/view-cart', [ShopController::class, 'view_cart'])->name('view-cart');
    Route::post('/update-cart', [ShopController::class, 'update_cart'])->name('update-cart');
    Route::post('/remove-from-cart', [ShopController::class, 'remove_from_cart'])->name('remove-from-cart');
        
    Route::get('/checkout', [ShopController::class, 'checkout_page'])->name('checkout_page');
    Route::post('/checkout', [ShopController::class, 'checkout'])->name('checkout');