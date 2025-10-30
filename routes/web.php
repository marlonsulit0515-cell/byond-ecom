<?php


use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ShippingRateController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
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
Route::get('/home', [HomeController::class, 'homepage_products'])->name('home');
Route::get('/shop/sale', function () {
    $product = Product::whereNotNull('discount_price')
        ->where('discount_price', '>', 0)
        ->latest()
        ->paginate(15);

    return view('shop.shop-page', compact('product'));
})->name('shop-sale');


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

    Route::get('/shipping-settings', [ShippingRateController::class, 'shipping_settings'])->name('admin.shipping-settings');
    Route::patch('/shipping-settings/update/{id}', [ShippingRateController::class, 'updateShipPrice'])->name('admin.shipping.update');
    Route::delete('/shipping-settings/delete/{id}', [ShippingRateController::class, 'deleteShipPrice'])->name('admin.shipping.delete');

    Route::post('/shipping-settings/fixed-rates/add', [ShippingRateController::class, 'addFixedRate'])->name('admin.shipping.fixed.add');
    Route::patch('/shipping-settings/fixed-rates/update/{id}', [ShippingRateController::class, 'updateFixedRate'])->name('admin.shipping.fixed.update');
    Route::delete('/shipping-settings/fixed-rates/delete/{id}', [ShippingRateController::class, 'deleteFixedRate'])->name('admin.shipping.fixed.delete');

    Route::get('/registered-users', [AdminController::class, 'registered_users'])->name('admin.user-management');
    Route::get('/inbox', [AdminController::class, 'inbox'])->name('admin.inbox');


});

/*
|--------------------------------------------------------------------------
| ShopController Routes
|--------------------------------------------------------------------------
| Handles: Product display, Homepage, Shop pages, Checkout page view, Confirmation
*/

Route::get('/product-details/{id}', [ShopController::class, 'item_details'])->name('product-details');

// Protected routes requiring authentication
Route::middleware(['auth', \App\Http\Middleware\UserMiddleware::class])->group(function () {
    Route::get('/checkout', [ShopController::class, 'checkout_page'])
        ->middleware('throttle:30,1') // limit to 30 requests per minute
        ->name('checkout_page');

    Route::get('/order-confirmation/{orderId}', [ShopController::class, 'order_confirmation'])
        ->name('order-confirmation');

    Route::post('/user/orders/{id}/request-cancellation', [UserController::class, 'requestCancellation']);
    Route::post('/user/orders/{id}/confirm-delivery', [UserController::class, 'confirmDelivery']);
});

/*
|--------------------------------------------------------------------------
| CartController Routes
|--------------------------------------------------------------------------
| Handles: Add to cart, Update cart, Remove from cart, View cart, Buy now
*/

Route::get('/cart', [CartController::class, 'view_cart'])->name('cart');
Route::get('/view-cart', [CartController::class, 'view_cart'])->name('view-cart');
Route::post('/add-to-cart/{id}', [CartController::class, 'add_to_cart'])->name('cart-page');
Route::patch('/update-cart', [CartController::class, 'update_cart'])->name('update-cart');
Route::delete('/remove-from-cart', [CartController::class, 'remove_from_cart'])->name('remove-from-cart');
Route::post('/buy-now/{id}', [CartController::class, 'buy_now'])->name('buy-now');

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
    Route::get('/admin/orders/status-counts', [OrderController::class, 'getStatusCounts'])->name('orders.status-counts');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    
    Route::post('/orders/{id}/approve-cancellation', [OrderController::class, 'approveCancellation'])->name('orders.approve-cancellation');
    
    Route::post('/orders/{id}/reject-cancellation', [OrderController::class, 'rejectCancellation'])->name('orders.reject-cancellation');
});

/*
|--------------------------------------------------------------------------
| CheckoutController Routes
|--------------------------------------------------------------------------
| Handles: Checkout processing, Payment gateway integration (PayPal, GCash, Maya)
*/

Route::post('/checkout/process', [CheckoutController::class, 'checkout'])->name('checkout.process');
 Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate.shipping');

// PayPal routes
Route::prefix('paypal')->group(function () {
    Route::get('/success', [CheckoutController::class, 'paypalSuccess'])
        ->name('paypal.success');
    
    Route::get('/cancel', [CheckoutController::class, 'paypalCancel'])
        ->name('paypal.cancel');
});
// Paymongo Routes
Route::prefix('paymongo')->group(function () {
    Route::get('/success', [CheckoutController::class, 'paymongoSuccess'])
        ->name('paymongo.success');
    
    Route::get('/cancel', [CheckoutController::class, 'paymongoCancel'])
        ->name('paymongo.cancel');
});

/*
|--------------------------------------------------------------------------
| Additional Routes
|--------------------------------------------------------------------------
*/

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