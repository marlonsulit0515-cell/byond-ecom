<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Public Routes (No Controller)
|--------------------------------------------------------------------------
| Routes that don't require authentication and use closures
|
*/

// Main Home Page
// Redirect `/` → `/home`
Route::get('/', function () {
    return redirect()->route('home');
});

// Landing page route
Route::get('/home', function () {
    $product = Product::all(); //Display all products in the home page
    return view('home', compact('product'));
})->name('home');

Route::get('/aboutus', function () {
    return view('aboutus');
})->name('aboutus');

Route::get('/contact', function () {
    return view('contact');
})->name('view.contact');

// Admin Dashboard (Closure Routes)
Route::middleware(['auth', \App\Http\Middleware\Admin::class])->group(function () {
    Route::get('/admin-dashboard', function () {
        return view('AdminPanel.AdminDash');
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
});

/*
|--------------------------------------------------------------------------
| HomeController Routes
|--------------------------------------------------------------------------
|
*/

//Menu Pages Routes
Route::get('/shop-page', [HomeController::class, 'shop_menu'])->name('shop-page');
Route::get('/shop-page/category/{category}', [HomeController::class, 'category_dropdown'])->name('shop-category');


//Routes for Blogs
Route::get('/blog_content1', [HomeController::class, 'blog_content_one'])->name('content-one');
Route::get('/blog_content2', [HomeController::class, 'blog_content_two'])->name('content-two');
Route::get('/blog_content3', [HomeController::class, 'blog_content_three'])->name('content-three');
Route::get('/blog_content4', [HomeController::class, 'blog_content_four'])->name('content-four');

/*
|--------------------------------------------------------------------------
| AuthenticatedSessionController Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/auth/google', [AuthenticatedSessionController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback'])->name('google.callback');


/*
|--------------------------------------------------------------------------
| UserController Routes
|--------------------------------------------------------------------------
|
*/
Route::middleware(['auth', \App\Http\Middleware\UserMiddleware::class])->group(function () {
Route::get('/homepage', [UserController::class, 'index'])->name('userdash');
Route::get('/My-Dashboard', [UserController::class, 'user_dashboard'])->name('user.dashboard.legacy');


Route::get('/dashboard/orders', [UserController::class, 'myOrders'])->name('orders.dashboard');

Route::get('/user-dashboard', [UserController::class, 'dashboard'])
    ->name('user.dashboard');

// Alternative route for dashboard (if you prefer /user/dashboard)
Route::get('/user/dashboard', [UserController::class, 'dashboard'])
    ->name('user.dashboard.alt');

// Orders with filtering and pagination
Route::get('/user/orders', [UserController::class, 'orders'])
    ->name('user.orders');

// Order Details/Receipt
Route::get('/user/orders/{id}', [UserController::class, 'orderDetails'])
    ->name('user.order.details')
    ->where('id', '[0-9]+'); // Ensure ID is numeric

// AJAX Route for Order Status Updates
Route::get('/user/orders/{id}/status', [UserController::class, 'getOrderStatus'])
    ->name('user.order.status')
    ->where('id', '[0-9]+');

// Additional useful routes you might need:

// Cancel Order
Route::post('/user/orders/{id}/cancel', [UserController::class, 'cancelOrder'])
    ->name('user.order.cancel')
    ->where('id', '[0-9]+');

// Confirm Delivery
Route::post('/user/orders/{id}/confirm-delivery', [UserController::class, 'confirmDelivery'])
    ->name('user.order.confirm-delivery')
    ->where('id', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| ProfileController Routes
|--------------------------------------------------------------------------
|
*/

// Profile Management
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

/*
|--------------------------------------------------------------------------
| AdminController Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', \App\Http\Middleware\Admin::class])->group(function () {
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
| ShopController Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/cart', [ShopController::class, 'cart_page'])->name('cart');
Route::get('/product-details/{id}', [ShopController::class, 'item_details'])->name('product-details');

// Add to cart
Route::post('/add-to-cart/{id}', [ShopController::class, 'add_to_cart'])->name('cart-page');
Route::get('/view-cart', [ShopController::class, 'view_cart'])->name('view-cart');
Route::post('/update-cart', [ShopController::class, 'update_cart'])->name('update-cart');
Route::post('/remove-from-cart', [ShopController::class, 'remove_from_cart'])->name('remove-from-cart');
Route::post('/buy-now/{id}', [ShopController::class, 'buy_now'])->name('buy-now');

Route::middleware(['auth', \App\Http\Middleware\UserMiddleware::class])->group(function () {
Route::get('/checkout', [ShopController::class, 'checkout_page'])->name('checkout_page');
Route::get('/shop/confirmation/{orderNumber}', [ShopController::class, 'confirmation'])->name('shop.confirmation');
Route::get('/shop-more', [ShopController::class, 'shop_more'])->name('shop.more');
});
/*
|--------------------------------------------------------------------------
| OrderController Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', \App\Http\Middleware\Admin::class])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/bulk-update', [OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-update');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
});

/*
|--------------------------------------------------------------------------
| CheckoutController Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/checkout/process', [CheckoutController::class, 'checkout'])->name('checkout.process');
// PayPal routes
Route::get('/paypal/success', [CheckoutController::class, 'paypalSuccess'])->name('paypal.success');
Route::get('/paypal/cancel', [CheckoutController::class, 'paypalCancel'])->name('paypal.cancel');

Route::get('/my-orders', function () {
    return view('UserPanel.user-orders');
})->name('view-my-orders');
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Include Laravel's default authentication routes
|
*/
require __DIR__.'/auth.php';