<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function view_cart()
    { 
        $cart = session()->get('cart', []);
        $categories = Category::all();
        
        if (!empty($cart)) {
            $productIds = array_column($cart, 'product_id');
            $products = Product::whereIn('id', $productIds)
                            ->get()
                            ->keyBy('id'); 
            
            foreach ($cart as $key => &$item) {
                if (isset($products[$item['product_id']])) {
                    $product = $products[$item['product_id']];
                    $stockField = $this->getStockField($item['size'] ?? 'M');
                    $currentStock = $product->$stockField ?? 0;
                    
                    $item['current_stock'] = $currentStock;
                    $item['image'] = $product->image;
                    $item['price'] = ($product->discount_price && $product->discount_price > 0) 
                                    ? $product->discount_price 
                                    : $product->price;

                    // Auto-adjust quantity if exceeds current stock
                    if ($item['quantity'] > $currentStock) {
                        $item['quantity'] = max(1, $currentStock);
                    }
                } else {
                    unset($cart[$key]);
                }
            }

            session()->put('cart', $cart);
        }

        return view('shop.cart', compact('cart', 'categories'));
    }

    /**
     * Add product to cart
     */
    public function add_to_cart(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $size = $request->size ?? 'M';
            $requestedQty = (int)($request->quantity ?? 1);
            
            // Validate size
            if (!in_array($size, ['S', 'M', 'L', 'XL', '2XL'])) {
                $message = "Invalid size selected.";
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Check stock availability
            $stockField = $this->getStockField($size);
            $availableStock = $product->$stockField ?? 0;
            
            if ($availableStock <= 0) {
                $message = "Size {$size} is currently out of stock.";
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return redirect()->back()->with('error', $message);
            }
            
            $cart = session()->get('cart', []);
            $cartKey = $id . '_' . $size;
            
            // Calculate total quantity in cart
            $totalInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
            
            // Validate stock
            if ($totalInCart + $requestedQty > $availableStock) {
                $remaining = max(0, $availableStock - $totalInCart);
                $message = $remaining > 0 
                    ? "Only {$remaining} more available for size {$size}."
                    : "Maximum stock ({$availableStock}) already in cart.";
                
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Determine price
            $price = ($product->discount_price && $product->discount_price > 0) ? 
                    $product->discount_price : $product->price;

            // Add or update cart
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $requestedQty;
            } else {
                $cart[$cartKey] = [
                    "product_id"     => $id,
                    "name"           => $product->name,
                    "size"           => $size,
                    "quantity"       => $requestedQty,
                    "price"          => $price,
                    "image"          => $product->image,
                    "original_price" => $product->price,
                    "discount_price" => $product->discount_price,
                ];
            }

            session()->put('cart', $cart);
            
            $cartCount = array_sum(array_column($cart, 'quantity'));
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Added {$requestedQty} x {$product->name} (Size: {$size}) to cart!",
                    'cartCount' => $cartCount
                ]);
            }
            
            return redirect()->back()->with('success', 'Product added to cart successfully!');
            
        } catch (\Exception $e) {
            Log::error('Cart Error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'An error occurred while adding to cart.');
        }
    }

    /**
     * Update cart quantity
     */
    public function update_cart(Request $request)
    {
        try {
            if ($request->id && isset($request->quantity)) {
                $cart = session()->get('cart', []);
                $cartId = $request->id;
                $newQuantity = intval($request->quantity);
                
                if (!isset($cart[$cartId])) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Item not found in cart'
                    ]);
                }
                
                // Validate stock
                $productId = $cart[$cartId]['product_id'];
                $size = $cart[$cartId]['size'] ?? 'M';
                $product = Product::find($productId);
                
                if (!$product) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Product not found'
                    ]);
                }
                
                $stockField = $this->getStockField($size);
                $availableStock = $product->$stockField ?? 0;
                
                if ($newQuantity > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Only {$availableStock} available in stock.",
                        'maxStock' => $availableStock
                    ]);
                }
                
                if ($newQuantity < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity must be at least 1'
                    ]);
                }
                
                // Get current price from product (in case it changed)
                $currentPrice = ($product->discount_price && $product->discount_price > 0) 
                    ? $product->discount_price 
                    : $product->price;
                
                // Update cart with current price and quantity
                $cart[$cartId]["quantity"] = $newQuantity;
                $cart[$cartId]["price"] = $currentPrice;
                session()->put('cart', $cart);
                
                // Calculate totals
                $subtotal = $currentPrice * $newQuantity;
                $total = array_reduce($cart, function($carry, $item) {
                    return $carry + ((float)$item['price'] * (int)$item['quantity']);
                }, 0);
                
                $cartCount = array_sum(array_column($cart, 'quantity'));
                
                return response()->json([
                    'success' => true,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'cartCount' => $cartCount,
                    'quantity' => $newQuantity,
                    'price' => $currentPrice
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'Invalid request']);
            
        } catch (\Exception $e) {
            Log::error('Update Cart Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove_from_cart(Request $request)
    {
        try {
            if ($request->id) {
                $cart = session()->get('cart', []);
                
                if (isset($cart[$request->id])) {
                    unset($cart[$request->id]);
                    session()->put('cart', $cart);
                    
                    // Calculate new total
                    $total = array_reduce($cart, function($carry, $item) {
                        return $carry + ($item['price'] * $item['quantity']);
                    }, 0);
                    
                    $cartCount = array_sum(array_column($cart, 'quantity'));
                    $cartEmpty = count($cart) === 0;
                    
                    return response()->json([
                        'success' => true,
                        'total' => $total,
                        'cartCount' => $cartCount,
                        'cartEmpty' => $cartEmpty,
                        'itemCount' => count($cart)
                    ]);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'Item not found']);
            
        } catch (\Exception $e) {
            Log::error('Remove Cart Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Buy now - Add to cart and redirect to checkout
     */
    public function buy_now(Request $request, $id)
    {
        $addToCartResponse = $this->add_to_cart($request, $id);
        
        if (session()->has('error')) {
            return $addToCartResponse;
        }
        
        return redirect()->route('checkout_page');
    }
    
    /**
     * Helper method to get stock field name
     */
    private function getStockField($size)
    {
        $normalized = strtolower($size);
        return 'stock_' . $normalized;
    }
}