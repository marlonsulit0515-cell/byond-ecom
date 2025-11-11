<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{

    public function item_details($id)
    {   
        $categories = Category::all();
        $product = Product::find($id);
        return view('shop.product-details', compact('product', 'categories'));
    }

    /**
     * Display checkout page (requires authentication)
     */
    public function checkout_page()
    {
        // Check if user is authenticated - REDIRECT TO LOGIN IF NOT
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to proceed to checkout. Please login or create an account.');
        }
        
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('view-cart')->with('error', 'Your cart is empty!');
        }

        $provinces = DB::table('shipping_rates')
            ->where('is_active', true)
            ->orderBy('province')
            ->get(['province', 'price']);

        return view('shop.checkout', compact('cart', 'provinces'));
    }

    /**
     * Display order confirmation page
     */
    public function confirmation($orderNumber)
    {
        // Ensure only authenticated users can view confirmations
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view order confirmations.');
        }
        
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id()) // Only allow users to view their own orders
            ->with(['items.product', 'payment']) // Load product relationship
            ->firstOrFail();

        return view('shop.confirmation', compact('order'));
    }
}