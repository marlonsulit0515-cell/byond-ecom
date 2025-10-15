<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{


    public function item_details($id)
    {   
        $product=Product::find($id);
        return view('shop.product-details', compact('product'));
    }

    public function add_to_cart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $size = $request->size ?? 'M';
        $requestedQty = $request->quantity ?? 1;
        
        // Check stock availability
        $stockField = 'stock_' . strtolower($size);
        $availableStock = $product->$stockField ?? 0;
        
        if ($availableStock <= 0) {
            return redirect()->route('view-cart')->with('error', "Size {$size} is currently out of stock.");
        }
        
        $cart = session()->get('cart', []);
        $cartKey = $id . '_' . $size;
        
        // Calculate total quantity already in cart for this product+size
        $totalInCart = $this->getTotalInCart($cart, $id, $size);
        
        // Validate stock availability
        if ($totalInCart + $requestedQty > $availableStock) {
            return $this->handleStockError($totalInCart, $requestedQty, $availableStock, $size);
        }
        
        // Determine price (discount or regular)
        $price = ($product->discount_price && $product->discount_price > 0) ? 
                $product->discount_price : $product->price;

        // Add or update cart item
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $requestedQty;
        } else {
            $cart[$cartKey] = [
                "product_id"     => $id,
                "name"          => $product->name,
                "size"          => $size,
                "quantity"      => $requestedQty,
                "price"         => $price,
                "image"         => $product->image,
                "original_price" => $product->price,
                "discount_price" => $product->discount_price,
            ];
        }

        session()->put('cart', $cart);
        
        return redirect()->route('view-cart')
            ->with('success', "Added {$requestedQty} x {$product->name} (Size: {$size}) to cart!");
    }

    public function view_cart()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart', compact('cart'));
    }

    public function update_cart(Request $request)
    {
        if (!$request->id || !$request->quantity) {
            return $this->respondError($request, 'Invalid request.');
        }
        
        $cart = session()->get('cart', []);
        $cartId = $request->id;
        $newQuantity = intval($request->quantity);
        
        if (!isset($cart[$cartId])) {
            return $this->respondError($request, 'Item not found in cart.');
        }
        
        // Get product and validate stock
        $cartItem = $cart[$cartId];
        $productId = $cartItem['product_id'] ?? $cartId;
        $size = $cartItem['size'] ?? 'M';
        
        $stockValidation = $this->validateStock($cart, $cartId, $productId, $size, $newQuantity);
        if ($stockValidation !== true) {
            return $this->respondError($request, $stockValidation);
        }
        
        // Update or remove item
        if ($newQuantity > 0) {
            $cart[$cartId]["quantity"] = $newQuantity;
            session()->put('cart', $cart);
            return $this->respondSuccess($request, 'Cart updated successfully!', $cart[$cartId]['price']);
        } else {
            unset($cart[$cartId]);
            session()->put('cart', $cart);
            return $this->respondSuccess($request, 'Item removed from cart!');
        }
    }

    public function remove_from_cart(Request $request)
    {
        if (!$request->id) {
            return $this->respondError($request, 'Invalid request.');
        }
        
        $cart = session()->get('cart', []);
        $cartId = $request->id;
        
        if (!isset($cart[$cartId])) {
            return $this->respondError($request, 'Item not found in cart.');
        }
        
        unset($cart[$cartId]);
        session()->put('cart', $cart);
        
        return $this->respondSuccess($request, 'Item removed from cart!');
    }

    // Helper Methods
    private function getTotalInCart($cart, $productId, $size)
    {
        $total = 0;
        $cartKey = $productId . '_' . $size;
        
        foreach ($cart as $key => $item) {
            if (isset($item['product_id']) && isset($item['size'])) {
                // New format with size
                if ($item['product_id'] == $productId && $item['size'] == $size) {
                    $total += $item['quantity'];
                }
            } elseif ($key == $cartKey) {
                // Exact cart key match
                $total += $item['quantity'];
            } elseif ($key == $productId && !isset($item['size'])) {
                // Legacy format without size
                $total += $item['quantity'];
            }
        }
        
        return $total;
    }

    private function handleStockError($totalInCart, $requestedQty, $availableStock, $size)
    {
        $remaining = max(0, $availableStock - $totalInCart);
        
        if ($remaining > 0) {
            $message = "Cannot add {$requestedQty} items. Only {$remaining} more available for size {$size}. (Available: {$availableStock}, In cart: {$totalInCart})";
        } else {
            $message = "Cannot add more items. You already have the maximum stock ({$availableStock}) for size {$size} in your cart.";
        }
        
        return redirect()->route('view-cart')->with('error', $message);
    }

    private function validateStock($cart, $currentCartId, $productId, $size, $newQuantity)
    {
        $product = Product::find($productId);
        if (!$product) return true; // Skip validation if product not found
        
        $stockField = 'stock_' . strtolower($size);
        $availableStock = $product->$stockField ?? 0;
        
        // Check direct quantity limit
        if ($newQuantity > $availableStock) {
            return "Cannot update quantity. Maximum available stock for size {$size} is {$availableStock}.";
        }
        
        // Calculate other items in cart with same product+size
        $otherItemsTotal = 0;
        foreach ($cart as $key => $item) {
            if ($key != $currentCartId) {
                $itemProductId = $item['product_id'] ?? $key;
                $itemSize = $item['size'] ?? 'M';
                
                if ($itemProductId == $productId && $itemSize == $size) {
                    $otherItemsTotal += $item['quantity'];
                }
            }
        }
        
        // Check combined quantity limit
        if ($otherItemsTotal + $newQuantity > $availableStock) {
            $maxAllowed = max(0, $availableStock - $otherItemsTotal);
            return "Cannot update to {$newQuantity}. Only {$maxAllowed} available for size {$size}.";
        }
        
        return true;
    }

    private function respondError($request, $message)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message]);
        }
        return redirect()->back()->with('error', $message);
    }

    private function respondSuccess($request, $message, $itemPrice = null)
    {
        if ($request->ajax()) {
            $response = ['success' => true];
            if ($itemPrice !== null) {
                $response['item_price'] = $itemPrice;
            }
            return response()->json($response);
        }
        return redirect()->back()->with('success', $message);
    }


        public function buy_now(Request $request, $id)
    {
        // Add the item to cart first
        $addToCartResponse = $this->add_to_cart($request, $id);
        
        if ($addToCartResponse instanceof \Illuminate\Http\RedirectResponse) {
            // Check for errors in the session
            if (session()->has('error')) {
                return $addToCartResponse; // Redirect back with error
            }
        }
        
        // Redirect to checkout page
        return redirect()->route('checkout_page');
    }

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

        return view('shop.checkout', compact('cart'));
    }

    public function checkout(Request $request)
    {
        // Additional security check - ensure user is still authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to complete your order.');
        }
        
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('view-cart')->with('error', 'Your cart is empty.');
            
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'barangay' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'billing_address' => 'required|string',
            'delivery_option' => 'required|string',
            'payment_method' => 'required|string',
            // Remove email validation since only authenticated users can checkout now
            // 'email' => 'nullable|email',
        ]);

        // Validate stock before proceeding
        foreach ($cart as $cartKey => $item) {
            // FIX: Handle both array key formats consistently
            $productId = is_numeric($cartKey) ? $cartKey : ($item['product_id'] ?? explode('_', $cartKey)[0]);
        $size = $item['size'] ?? 'M';

        $product = Product::find($productId);
        if ($product) {
            $stockField = 'stock_' . strtolower($size);
            $availableStock = $product->$stockField ?? 0;

            if ($item['quantity'] > $availableStock) {
                return redirect()->route('view-cart')->with('error',
                    "Item '{$item['name']}' (Size: {$size}) has insufficient stock. Available: {$availableStock}, Requested: {$item['quantity']}"
                );
            }
        }
    }

    try {
        DB::beginTransaction();

        // Create Order - Only for authenticated users now
        $order = Order::create([
            'user_id' => Auth::id(), // This will always have a value now
            'guest_email' => null, // No longer needed since all users must be authenticated
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'total' => collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'country' => $request->country,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'postal_code' => $request->postal_code,
            'billing_address' => $request->billing_address,
            'delivery_option' => $request->delivery_option,
            'same_as_billing' => $request->has('same_as_billing'),
            'shipping_address' => $request->has('same_as_billing')
                ? $request->billing_address
                : $request->shipping_address,
        ]);

        // Save Order Items - FIX: Use correct product_id
        foreach ($cart as $cartKey => $item) {
            // FIX: Extract product_id consistently
            $productId = is_numeric($cartKey) ? $cartKey : ($item['product_id'] ?? explode('_', $cartKey)[0]);
            
            $order->items()->create([
                'product_id' => $productId,
                'product_name' => $item['name'],
                'size' => $item['size'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
            ]);

            // Reduce stock
            $product = Product::find($productId);
            if ($product) {
                $size = $item['size'] ?? 'M';
                $stockField = 'stock_' . strtolower($size);
                if ($product->$stockField >= $item['quantity']) {
                    $product->$stockField -= $item['quantity'];
                    $product->save();
                }
            }
        }

        // Create Payment Record
        $order->payment()->create([
            'method' => $request->payment_method,
            'status' => $request->payment_method === 'cash' ? 'pending' : 'initiated',
            'amount' => $order->total,
        ]);

        DB::commit();

        // Clear cart
        session()->forget('cart');

        return redirect()
            ->route('shop.confirmation', $order->order_number)
            ->with('success', 'Order placed successfully! Your order number is ' . $order->order_number);

    } catch (\Exception $e) {
        DB::rollBack();
        
        // FIX: Add detailed error logging for debugging
        Log::error('Checkout failed: ' . $e->getMessage(), [
            'user_id' => Auth::id(),
            'cart' => $cart,
            'request_data' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()->with('error', 'Something went wrong. Please try again. Error: ' . $e->getMessage());
    }
}

    public function confirmation($orderNumber)
    {
        // Ensure only authenticated users can view confirmations
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view order confirmations.');
        }
        
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id()) // Only allow users to view their own orders
            ->with(['items.product', 'payment']) // FIX: Load product relationship too
            ->firstOrFail();

        return view('shop.confirmation', compact('order'));
    }
    public function shop_more()
    {
        $product=Product::all();
        return view('shop.shop-page', compact('product'));
    }
}