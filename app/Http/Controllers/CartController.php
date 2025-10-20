<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function view_cart()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart', compact('cart'));
    }

    /**
     * Add product to cart
     */
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

    /**
     * Update cart item quantity
     */
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

    /**
     * Remove item from cart
     */
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

    /**
     * Buy now - Add to cart and redirect to checkout
     */
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

    // ==================== Helper Methods ====================

    /**
     * Get total quantity in cart for specific product and size
     */
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

    /**
     * Handle stock error messages
     */
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

    /**
     * Validate stock availability
     */
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

    /**
     * Return error response (AJAX or redirect)
     */
    private function respondError($request, $message)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message]);
        }
        return redirect()->back()->with('error', $message);
    }

    /**
     * Return success response (AJAX or redirect)
     */
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
}