@extends('layouts.default')
<link href="{{ asset('css/view-cart.css') }}" rel="stylesheet" />
@section('maincontent')
<div class="cart-container">
    <h1 class="cart-title">Your Cart</h1>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    @if(!empty($cart))
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Details</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($cart as $id => $item)
                    @php 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        
                        // Get current stock for this size from database
                        $product = \App\Models\Product::find($item['product_id'] ?? $id);
                        $sizeField = 'stock_' . strtolower($item['size'] ?? 'm');
                        $currentStock = $product ? ($product->$sizeField ?? 0) : 0;
                        
                        // Calculate total quantity of this product+size already in cart
                        $cartKey = $item['product_id'] . '_' . ($item['size'] ?? 'M');
                        $totalInCart = 0;
                        foreach($cart as $cartItem) {
                            $itemCartKey = ($cartItem['product_id'] ?? $id) . '_' . ($cartItem['size'] ?? 'M');
                            if($itemCartKey === $cartKey) {
                                $totalInCart += $cartItem['quantity'];
                            }
                        }
                    @endphp
                    <tr class="{{ $currentStock < $item['quantity'] ? 'low-stock-warning' : '' }}">
                        <td>
                            <img src="/product/{{ $item['image'] }}" alt="{{ $item['name'] }}" class="product-image">
                        </td>
                        <td class="product-details">
                            <div class="product-name">{{ $item['name'] }}</div>
                            <div class="product-size">Size: <strong>{{ $item['size'] ?? 'M' }}</strong></div>
                            <div class="stock-info">
                                @if($currentStock > 0)
                                    <span class="stock-available">{{ $currentStock }} in stock</span>
                                @else
                                    <span class="stock-unavailable">Out of stock</span>
                                @endif
                            </div>
                            @if($totalInCart > $currentStock)
                                <div class="stock-warning">
                                    Cart quantity ({{ $totalInCart }}) exceeds available stock ({{ $currentStock }})
                                </div>
                            @endif
                        </td>
                        <td class="price-cell">
                            @if(isset($item['discount_price']) && $item['discount_price'] > 0 && $item['discount_price'] < $item['original_price'])
                                <div class="price-discount">₱{{ number_format($item['discount_price'], 2) }}</div>
                                <div class="price-original"><s>₱{{ number_format($item['original_price'], 2) }}</s></div>
                                <div class="discount-badge">SALE</div>
                            @elseif(isset($item['original_price']) && $item['price'] < $item['original_price'])
                                <div class="price-discount">₱{{ number_format($item['price'], 2) }}</div>
                                <div class="price-original"><s>₱{{ number_format($item['original_price'], 2) }}</s></div>
                                <div class="discount-badge">SALE</div>
                            @else
                                <div class="price-regular">₱{{ number_format($item['price'], 2) }}</div>
                            @endif
                        </td>
                        <td>
                            <!-- Update Quantity Form with Stock Validation -->
                            <form action="{{ route('update-cart') }}" method="POST" class="quantity-form">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <input type="hidden" name="size" value="{{ $item['size'] ?? 'M' }}">
                                <input type="hidden" name="product_id" value="{{ $item['product_id'] ?? $id }}">
                                
                                <div class="quantity-controls">
                                    <button type="button" class="qty-btn" onclick="decreaseQty(this)" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                                    <input type="number" 
                                           name="quantity" 
                                           value="{{ $item['quantity'] }}" 
                                           min="1" 
                                           max="{{ $currentStock }}"
                                           class="qty-input"
                                           data-stock="{{ $currentStock }}"
                                           onchange="validateQuantity(this)">
                                    <button type="button" class="qty-btn" onclick="increaseQty(this)" {{ $item['quantity'] >= $currentStock ? 'disabled' : '' }}>+</button>
                                </div>
                                
                                <button type="submit" class="btn-update">Update</button>
                                
                                @if($item['quantity'] > $currentStock)
                                    <div class="quantity-error">
                                        Max available: {{ $currentStock }}
                                    </div>
                                @endif
                            </form>
                        </td>
                        <td class="subtotal">₱{{ number_format($subtotal, 2) }}</td>
                        <td>
                            <!-- Remove Item Form -->
                            <form action="{{ route('remove-from-cart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <button type="submit" class="btn-remove" onclick="return confirm('Remove this item from cart?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="cart-total">
                    <td colspan="5" class="total-label">Total:</td>
                    <td class="subtotal">₱{{ number_format($total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="checkout-actions">
                        <a href="{{ url('/home') }}" class="btn-continue">Continue Shopping</a>
                        <button type="button" class="btn-checkout" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Add some products to get started!</p>
            <a href="{{ url('/home') }}" class="btn-shop">Start Shopping</a>
        </div>
    @endif
</div>
<script>
function decreaseQty(button) {
    const input = button.parentElement.querySelector('.qty-input');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
        updateQuantityButtons(input);
    }
}

function increaseQty(button) {
    const input = button.parentElement.querySelector('.qty-input');
    const currentValue = parseInt(input.value);
    const maxStock = parseInt(input.dataset.stock);
    if (currentValue < maxStock) {
        input.value = currentValue + 1;
        updateQuantityButtons(input);
    }
}

function validateQuantity(input) {
    const value = parseInt(input.value);
    const maxStock = parseInt(input.dataset.stock);
    
    if (value > maxStock) {
        input.value = maxStock;
        alert(`Maximum available stock is ${maxStock}`);
    }
    if (value < 1) {
        input.value = 1;
    }
    
    updateQuantityButtons(input);
}

function updateQuantityButtons(input) {
    const container = input.parentElement;
    const decreaseBtn = container.querySelector('.qty-btn:first-child');
    const increaseBtn = container.querySelector('.qty-btn:last-child');
    const value = parseInt(input.value);
    const maxStock = parseInt(input.dataset.stock);
    
    decreaseBtn.disabled = value <= 1;
    increaseBtn.disabled = value >= maxStock;
}

function proceedToCheckout() {
    // Check for stock issues before checkout
    const warnings = document.querySelectorAll('.stock-warning');
    const outOfStock = document.querySelectorAll('.stock-unavailable');
    
    if (warnings.length > 0 || outOfStock.length > 0) {
        if (confirm('Some items in your cart have stock issues. Continue to checkout anyway?')) {
            // Proceed to checkout route
            window.location.href = '{{ route("checkout") }}';
        }
    } else {
        // Proceed to checkout route
        window.location.href = '{{ route("checkout") }}';
    }
}

// Initialize quantity buttons on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.qty-input').forEach(input => {
        updateQuantityButtons(input);
    });
});
</script>
@endsection