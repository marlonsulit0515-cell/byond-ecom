<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

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
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => "Size {$size} is currently out of stock."
            ]);
        }
        return redirect()->back()->with('error', "Size {$size} is currently out of stock.");
    }
    
    $cart = session()->get('cart', []);
    $cartKey = $id . '_' . $size;
    
    // Calculate total quantity in cart for this product+size
    $totalInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
    
    // Validate stock
    if ($totalInCart + $requestedQty > $availableStock) {
        $remaining = max(0, $availableStock - $totalInCart);
        $message = $remaining > 0 
            ? "Only {$remaining} more available for size {$size}."
            : "Maximum stock ({$availableStock}) already in cart.";
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ]);
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
    
    // Calculate total cart count
    $cartCount = array_sum(array_column($cart, 'quantity'));
    
    // Return JSON for AJAX requests
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => "Added {$requestedQty} x {$product->name} (Size: {$size}) to cart!",
            'cartCount' => $cartCount
        ]);
    }
    
    return redirect()->back()->with('success', 'Product added to cart successfully!');
}

    /**
     * Update cart quantity
     */
    public function update_cart(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cartId = $request->id;
            $newQuantity = intval($request->quantity);
            
            if (isset($cart[$cartId])) {
                // Validate stock
                $productId = $cart[$cartId]['product_id'];
                $size = $cart[$cartId]['size'] ?? 'M';
                $product = Product::find($productId);
                
                if ($product) {
                    $stockField = 'stock_' . strtolower($size);
                    $availableStock = $product->$stockField ?? 0;
                    
                    if ($newQuantity > $availableStock) {
                        session()->flash('error', "Only {$availableStock} available in stock.");
                        return response()->json(['success' => false]);
                    }
                }
                
                $cart[$cartId]["quantity"] = $newQuantity;
                session()->put('cart', $cart);
            }
            
            session()->flash('success', 'Cart updated successfully');
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Remove item from cart
     */
    public function remove_from_cart(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            
            session()->flash('success', 'Product removed successfully');
        }
        
        return response()->json(['success' => true]);
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
}