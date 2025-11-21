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
            $cart = $this->validateAndUpdateCart($cart);
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
            $size = strtoupper($request->input('size', 'M'));
            $requestedQty = max(1, (int)$request->input('quantity', 1));
            
            if (!$this->isValidSize($size)) {
                return $this->respondError('Invalid size selected.', $request);
            }
            
            $availableStock = $this->getProductStock($product->fresh(), $size);
            
            if ($availableStock <= 0) {
                return $this->respondError("Size {$size} is currently out of stock.", $request);
            }
            
            $cart = session()->get('cart', []);
            $cartKey = "{$id}_{$size}";
            
            $totalInCart = $cart[$cartKey]['quantity'] ?? 0;
            
            if ($totalInCart + $requestedQty > $availableStock) {
                $remaining = max(0, $availableStock - $totalInCart);
                $message = $remaining > 0 
                    ? "Only {$remaining} more available for size {$size}."
                    : "Maximum stock ({$availableStock}) already in cart.";
                
                return $this->respondError($message, $request);
            }

            $price = $this->getProductPrice($product);

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $requestedQty;
                $cart[$cartKey]['price'] = $price;
                $cart[$cartKey]['current_stock'] = $availableStock; // Add current stock
            } else {
                $cart[$cartKey] = [
                    "product_id" => $id,
                    "name" => $product->name,
                    "size" => $size,
                    "quantity" => $requestedQty,
                    "price" => $price,
                    "image" => $product->image,
                    "current_stock" => $availableStock, // Add current stock
                ];
            }

            session()->put('cart', $cart);
            $cartCount = $this->getCartItemCount($cart);
            
            $message = "Added {$requestedQty} x {$product->name} (Size: {$size}) to cart!";
            
            return $request->expectsJson()
                ? response()->json([
                    'success' => true, 
                    'message' => $message, 
                    'cartCount' => $cartCount,
                    'remainingStock' => $availableStock - ($totalInCart + $requestedQty) // NEW
                ])
                : redirect()->back()->with('success', 'Product added to cart successfully!');
            
        } catch (\Exception $e) {
            Log::error('Cart Add Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->respondError('An error occurred while adding to cart.', $request);
        }
    }

    /**
     * Update cart quantity
     */
    public function update_cart(Request $request)
{
    try {
        $cartId = $request->input('id');
        $newQuantity = max(1, (int)$request->input('quantity', 1));
        
        if (!$cartId) {
            return response()->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $cart = session()->get('cart', []);
        
        if (!isset($cart[$cartId])) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart'], 404);
        }
        
        // CRITICAL FIX: Always get fresh product data
        $product = Product::find($cart[$cartId]['product_id']);
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        
        $size = $cart[$cartId]['size'] ?? 'M';
        
        // CRITICAL FIX: Get real-time stock
        $availableStock = $this->getProductStock($product, $size);
        
        if ($newQuantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Only {$availableStock} available in stock.",
                'maxStock' => $availableStock,
                'quantity' => $cart[$cartId]['quantity'] // Return current quantity
            ], 400);
        }
        
        // Get current price (in case it changed)
        $currentPrice = $this->getProductPrice($product);
        
        // Update cart with fresh data
        $cart[$cartId]['quantity'] = $newQuantity;
        $cart[$cartId]['price'] = $currentPrice;
        $cart[$cartId]['current_stock'] = $availableStock; // Update stock info
        
        session()->put('cart', $cart);
        
        // Calculate totals
        $subtotal = $currentPrice * $newQuantity;
        $total = $this->calculateCartTotal($cart);
        $cartCount = $this->getCartItemCount($cart);
        
        return response()->json([
            'success' => true,
            'subtotal' => $subtotal,
            'total' => $total,
            'cartCount' => $cartCount,
            'quantity' => $newQuantity,
            'price' => $currentPrice,
            'maxStock' => $availableStock,
            'message' => 'Cart updated successfully'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Update Cart Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false, 
            'message' => 'An error occurred while updating cart.'
        ], 500);
    }
}

    /**
     * Remove item from cart
     */
    public function remove_from_cart(Request $request)
    {
        try {
            $cartId = $request->input('id');
            
            if (!$cartId) {
                return response()->json(['success' => false, 'message' => 'Invalid request']);
            }
            
            $cart = session()->get('cart', []);
            
            if (!isset($cart[$cartId])) {
                return response()->json(['success' => false, 'message' => 'Item not found']);
            }
            
            unset($cart[$cartId]);
            session()->put('cart', $cart);
            
            $total = $this->calculateCartTotal($cart);
            $cartCount = $this->getCartItemCount($cart);
            $cartEmpty = empty($cart);
            
            return response()->json([
                'success' => true,
                'total' => $total,
                'cartCount' => $cartCount,
                'cartEmpty' => $cartEmpty,
                'itemCount' => count($cart)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Remove Cart Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'An error occurred while removing item.']);
        }
    }

    /**
     * Get cart count (API)
     */
    public function getCartCount()
    {
        $cart = session()->get('cart', []);
        return response()->json(['cartCount' => $this->getCartItemCount($cart)]);
    }
    public function getProductStockAPI($id, Request $request)
    {
        try {
            $product = Product::findOrFail($id);
            $size = strtoupper($request->input('size', 'M'));
            
            // Validate size
            if (!$this->isValidSize($size)) {
                return response()->json(['success' => false, 'message' => 'Invalid size'], 400);
            }
            
            $stock = $this->getProductStock($product, $size);
            $price = $this->getProductPrice($product);
            
            return response()->json([
                'success' => true,
                'stock' => $stock,
                'price' => $price,
                'size' => $size,
                'available' => $stock > 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get Stock Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
    }
    /**
     * Buy now - Add to cart and redirect to checkout
     */
    public function buy_now(Request $request, $id)
    {
        $response = $this->add_to_cart($request, $id);
        
        if (session()->has('error')) {
            return $response;
        }
        
        return redirect()->route('checkout_page');
    }
    
    //HELPER METHODS

    private function isValidSize($size)
    {
        return in_array($size, ['S', 'M', 'L', 'XL', '2XL']);
    }
    
    private function getStockField($size)
    {
        return 'stock_' . strtolower($size);
    }

    private function getProductStock($product, $size)
    {
        $stockField = $this->getStockField($size);
        return (int)($product->$stockField ?? 0);
    }
    
    private function getProductPrice($product)
    {
        return ($product->discount_price && $product->discount_price > 0) 
            ? $product->discount_price 
            : $product->price;
    }
    
    /**
     * Calculate total cart value
     */
    private function calculateCartTotal($cart)
    {
        return array_reduce($cart, function($carry, $item) {
            return $carry + ((float)$item['price'] * (int)$item['quantity']);
        }, 0);
    }
    
    private function validateAndUpdateCart($cart)
    {
        $productIds = array_column($cart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        foreach ($cart as $key => &$item) {
            $product = $products[$item['product_id']] ?? null;
            
            if (!$product) {
                unset($cart[$key]);
                continue;
            }
            
            $size = $item['size'] ?? 'M';
            $currentStock = $this->getProductStock($product, $size);
            
            // Update item data
            $item['current_stock'] = $currentStock;
            $item['image'] = $product->image;
            $item['price'] = $this->getProductPrice($product);

            // Auto-adjust quantity if exceeds current stock
            if ($item['quantity'] > $currentStock) {
                $item['quantity'] = max(1, $currentStock);
            }
            
            // Remove if out of stock
            if ($currentStock <= 0) {
                unset($cart[$key]);
            }
        }
        
        return $cart;
    }
    
    private function respondError($message, $request)
    {
        return $request->expectsJson()
            ? response()->json(['success' => false, 'message' => $message])
            : redirect()->back()->with('error', $message);
    }

    private function getCartItemCount(array $cart)
    {
        $count = 0;
        foreach ($cart as $item) {
            $count += isset($item['quantity']) ? (int)$item['quantity'] : 0;
        }
        return $count;
    }
}