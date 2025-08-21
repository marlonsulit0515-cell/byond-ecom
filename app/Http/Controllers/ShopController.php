<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function store(Request $request)
  {
        // Handle the request to store a product
        // This method can be used for adding products to the cart or wishlist
        // Implementation will depend on your application's requirements
    }
    
    public function shop_page()
    {
        $product=Product::all();
        return view('home', compact('product'));
    }

    public function item_details($id)
    {   
        $product=Product::find($id);
        return view('shop.product-details', compact('product'));
    }

        public function add_to_cart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $size = $request->size ?? 'M'; // Get size from request, default to M
        $requestedQty = $request->quantity ?? 1;
        
        // Get current stock for the selected size
        $stockField = 'stock_' . strtolower($size);
        $availableStock = $product->$stockField ?? 0;
        
        // Check if size is available
        if ($availableStock <= 0) {
            return redirect()->route('view-cart')->with('error', "Size {$size} is currently out of stock.");
        }
        
        $cart = session()->get('cart', []);
        
        // Create unique cart key combining product ID and size
        $cartKey = $id . '_' . $size;
        
        // Calculate total quantity already in cart for this product+size combination
        $totalInCart = 0;
        foreach($cart as $key => $item) {
            // Check both new format (with product_id and size) and old format (just product id)
            if (isset($item['product_id']) && isset($item['size'])) {
                // New format with size
                if ($item['product_id'] == $id && $item['size'] == $size) {
                    $totalInCart += $item['quantity'];
                }
            } elseif ($key == $cartKey) {
                // Exact cart key match
                $totalInCart += $item['quantity'];
            } elseif ($key == $id && !isset($item['size'])) {
                // Legacy format without size (assume same size)
                $totalInCart += $item['quantity'];
            }
        }
        
        // Validate if adding requested quantity would exceed stock
        if ($totalInCart + $requestedQty > $availableStock) {
            $remaining = max(0, $availableStock - $totalInCart);
            if ($remaining > 0) {
                return redirect()->route('view-cart')->with('error', "Cannot add {$requestedQty} items. Only {$remaining} more available for size {$size}. (Available: {$availableStock}, In cart: {$totalInCart})");
            } else {
                return redirect()->route('view-cart')->with('error', "Cannot add more items. You already have the maximum stock ({$availableStock}) for size {$size} in your cart.");
            }
        }
        
        // Use discount price if available, otherwise regular price
        $price = $product->discount_price && $product->discount_price > 0 
            ? $product->discount_price 
            : $product->price;

        // Check if this exact product+size combination already exists in cart
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $requestedQty;
        } else {
            $cart[$cartKey] = [
                "product_id"     => $id, // Store product ID for reference
                "name"          => $product->name,
                "size"          => $size, // Store selected size
                "quantity"      => $requestedQty,
                "price"         => $price,
                "image"         => $product->image,
                "original_price" => $product->price,
                "discount_price" => $product->discount_price, // Store discount price for display
            ];
        }

        session()->put('cart', $cart);

        $successMessage = "Added {$requestedQty} x {$product->name} (Size: {$size}) to cart!";
        return redirect()->route('view-cart')->with('success', $successMessage);
    }

    public function view_cart()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart', compact('cart'));
    }

    public function update_cart(Request $request)
    {
        if($request->id && $request->quantity) {
            $cart = session()->get('cart', []);
            $cartId = $request->id;
            $newQuantity = intval($request->quantity);
            
            if (!isset($cart[$cartId])) {
                return redirect()->back()->with('error', 'Item not found in cart.');
            }
            
            // Get product and size info for stock validation
            $cartItem = $cart[$cartId];
            $productId = $cartItem['product_id'] ?? $cartId; // Handle both old and new format
            $size = $cartItem['size'] ?? 'M';
            
            // Validate against current stock
            $product = Product::find($productId);
            if ($product) {
                $stockField = 'stock_' . strtolower($size);
                $availableStock = $product->$stockField ?? 0;
                
                // Calculate total quantity in cart for this product+size (excluding current item being updated)
                $totalInCartExcludingCurrent = 0;
                foreach($cart as $key => $item) {
                    if ($key != $cartId) {
                        $itemProductId = $item['product_id'] ?? $key;
                        $itemSize = $item['size'] ?? 'M';
                        
                        if ($itemProductId == $productId && $itemSize == $size) {
                            $totalInCartExcludingCurrent += $item['quantity'];
                        }
                    }
                }
                
                // Check if new quantity exceeds available stock
                if ($newQuantity > $availableStock) {
                    return redirect()->back()->with('error', "Cannot update quantity. Maximum available stock for size {$size} is {$availableStock}.");
                }
                
                if ($totalInCartExcludingCurrent + $newQuantity > $availableStock) {
                    $maxAllowed = max(0, $availableStock - $totalInCartExcludingCurrent);
                    return redirect()->back()->with('error', "Cannot update to {$newQuantity}. Only {$maxAllowed} available for size {$size}.");
                }
            }
            
            // Update quantity or remove if 0
            if ($newQuantity > 0) {
                $cart[$cartId]["quantity"] = $newQuantity;
                session()->put('cart', $cart);
                return redirect()->back()->with('success', 'Cart updated successfully!');
            } else {
                // Remove item if quantity is 0
                unset($cart[$cartId]);
                session()->put('cart', $cart);
                return redirect()->back()->with('success', 'Item removed from cart!');
            }
        }
        
        return redirect()->back()->with('error', 'Invalid request.');
    }

    public function remove_from_cart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart', []);
            $cartId = $request->id;
            
            if(isset($cart[$cartId])) {
                $itemName = $cart[$cartId]['name'];
                $itemSize = $cart[$cartId]['size'] ?? '';
                
                unset($cart[$cartId]);
                session()->put('cart', $cart);
                
                $message = "Removed {$itemName}" . ($itemSize ? " (Size: {$itemSize})" : "") . " from cart!";
                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'Item not found in cart.');
            }
        }
        
        return redirect()->back()->with('error', 'Invalid request.');
    }
    public function checkout_page()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('view-cart')->with('error', 'Your cart is empty!');
        }

        // Here you can implement the logic to handle the checkout process
        // For example, you might want to redirect to a payment gateway or order summary page
        return view('shop.checkout', compact('cart'));
    }
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('view-cart')->with('error', 'Your cart is empty.');
        }
        
        // Validate stock before checkout
        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? explode('_', $cartKey)[0];
            $size = $item['size'] ?? 'M';
            
            $product = Product::find($productId);
            if ($product) {
                $stockField = 'stock_' . strtolower($size);
                $availableStock = $product->$stockField ?? 0;
                
                if ($item['quantity'] > $availableStock) {
                    return redirect()->route('view-cart')->with('error', 
                        "Item '{$item['name']}' (Size: {$size}) has insufficient stock. Available: {$availableStock}, Requested: {$item['quantity']}");
                }
            }
        }

        // Continue with checkout logic or redirect as needed
        // For now, just redirect to a success page or back with a success message
        return redirect()->route('checkout_page')->with('success', 'Checkout successful!');
    }
}