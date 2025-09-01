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

    <!-- Loading overlay for AJAX requests -->
    <div id="cart-loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px;">
            Updating cart...
        </div>
    </div>

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
            <tbody id="cart-tbody">
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
                    <tr class="{{ $currentStock < $item['quantity'] ? 'low-stock-warning' : '' }}" data-cart-id="{{ $id }}">
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
                            <!-- Auto-sync Quantity Form -->
                            <form class="quantity-form" data-cart-id="{{ $id }}">
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
                                           onchange="syncQuantityChange(this)"
                                           data-original-value="{{ $item['quantity'] }}">
                                    <button type="button" class="qty-btn" onclick="increaseQty(this)" {{ $item['quantity'] >= $currentStock ? 'disabled' : '' }}>+</button>
                                </div>
                                
                                @if($item['quantity'] > $currentStock)
                                    <div class="quantity-error">
                                        Max available: {{ $currentStock }}
                                    </div>
                                @endif
                            </form>
                        </td>
                        <td class="subtotal" data-subtotal="{{ $subtotal }}">₱{{ number_format($subtotal, 2) }}</td>
                        <td>
                            <!-- Auto-sync Remove Item Button -->
                            <button type="button" class="btn-remove" onclick="syncRemoveItem('{{ $id }}')" data-cart-id="{{ $id }}">Remove</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="cart-total">
                    <td colspan="5" class="total-label">Total:</td>
                    <td class="subtotal" id="cart-total">₱{{ number_format($total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="checkout-actions">
                        <a href="{{ url('/home') }}" class="btn-continue">Continue Shopping</a>

                        @if(auth()->check())
                            {{-- Logged-in user: Form to proceed to checkout --}}
                            <form action="{{ route('checkout_page') }}" method="GET" style="display: inline;">
                                <button type="submit" class="btn-checkout">Proceed to Checkout</button>
                            </form>
                        @else
                            {{-- Guest user: Show login modal --}}
                            <button type="button" class="btn-checkout" onclick="showAuthModal()">Proceed to Checkout</button>
                        @endif
                    </td>
                </tr>

                {{-- Place the modal outside the table, preferably at the bottom of the page --}}
                @guest
                    <x-auth-modal />
                @endguest
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
// Debounce timer for quantity changes
let quantityTimer = null;

function decreaseQty(button) {
    const input = button.parentElement.querySelector('.qty-input');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
        updateQuantityButtons(input);
        syncQuantityChange(input);
    }
}

function increaseQty(button) {
    const input = button.parentElement.querySelector('.qty-input');
    const currentValue = parseInt(input.value);
    const maxStock = parseInt(input.dataset.stock);
    if (currentValue < maxStock) {
        input.value = currentValue + 1;
        updateQuantityButtons(input);
        syncQuantityChange(input);
    }
}

function validateQuantity(input) {
    const value = parseInt(input.value);
    const maxStock = parseInt(input.dataset.stock);
    
    if (value > maxStock) {
        input.value = maxStock;
        showMessage(`Maximum available stock is ${maxStock}`, 'warning');
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

// New function to sync quantity changes via AJAX
function syncQuantityChange(input) {
    validateQuantity(input);
    
    const newQuantity = parseInt(input.value);
    const originalQuantity = parseInt(input.dataset.originalValue);
    
    // Only sync if quantity actually changed
    if (newQuantity === originalQuantity) return;
    
    // Clear previous timer
    if (quantityTimer) {
        clearTimeout(quantityTimer);
    }
    
    // Debounce the request (wait 500ms after user stops typing/clicking)
    quantityTimer = setTimeout(() => {
        const form = input.closest('.quantity-form');
        const formData = new FormData(form);
        
        showLoading(true);
        
        fetch('{{ route("update-cart") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the original value
                input.dataset.originalValue = newQuantity;
                
                // Update subtotal for this row
                const row = input.closest('tr');
                const subtotalCell = row.querySelector('.subtotal');
                const price = parseFloat(data.item_price);
                const newSubtotal = price * newQuantity;
                subtotalCell.textContent = `₱${newSubtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                subtotalCell.dataset.subtotal = newSubtotal;
                
                // Update total
                updateCartTotal();
                
                showMessage('Cart updated successfully!', 'success');
            } else {
                // Revert to original quantity on error
                input.value = originalQuantity;
                updateQuantityButtons(input);
                showMessage(data.message || 'Error updating cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert to original quantity on error
            input.value = originalQuantity;
            updateQuantityButtons(input);
            showMessage('Network error. Please try again.', 'error');
        })
        .finally(() => {
            showLoading(false);
        });
    }, 500);
}

// New function to sync item removal via AJAX
function syncRemoveItem(cartId) {
    if (!confirm('Remove this item from cart?')) return;
    
    showLoading(true);
    
    const formData = new FormData();
    formData.append('id', cartId);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("remove-from-cart") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from the table
            const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
            if (row) {
                row.remove();
            }
            
            // Update total
            updateCartTotal();
            
            // Check if cart is empty
            const tbody = document.getElementById('cart-tbody');
            if (tbody.children.length === 0) {
                // Reload page to show empty cart message
                location.reload();
            }
            
            showMessage('Item removed from cart!', 'success');
        } else {
            showMessage(data.message || 'Error removing item', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Network error. Please try again.', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Helper function to update cart total
function updateCartTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal[data-subtotal]').forEach(cell => {
        total += parseFloat(cell.dataset.subtotal);
    });
    
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = `₱${total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
}

// Helper function to show loading overlay
function showLoading(show) {
    const loading = document.getElementById('cart-loading');
    if (loading) {
        loading.style.display = show ? 'block' : 'none';
    }
}

// Helper function to show messages
function showMessage(message, type) {
    // Remove existing messages
    document.querySelectorAll('.temp-message').forEach(msg => msg.remove());
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `temp-message ${type === 'success' ? 'success-message' : 'error-message'}`;
    messageDiv.textContent = message;
    messageDiv.style.marginBottom = '15px';
    
    const cartContainer = document.querySelector('.cart-container');
    const title = cartContainer.querySelector('.cart-title');
    cartContainer.insertBefore(messageDiv, title.nextSibling);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

function proceedToCheckout() {
    // Check for stock issues before checkout
    const warnings = document.querySelectorAll('.stock-warning');
    const outOfStock = document.querySelectorAll('.stock-unavailable');
    
    if (warnings.length > 0 || outOfStock.length > 0) {
        if (confirm('Some items in your cart have stock issues. Continue to checkout anyway?')) {
            // Navigate to checkout PAGE (not submit route)
            window.location.href = '{{ route("checkout_page") }}';
        }
    } else {
        // Navigate to checkout PAGE (not submit route)
        window.location.href = '{{ route("checkout_page") }}';
    }
}

// Initialize quantity buttons on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.qty-input').forEach(input => {
        updateQuantityButtons(input);
    });
    // Make it globally accessible
    
    function showAuthModal() {
        document.getElementById('authModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAuthModal() {
        document.getElementById('authModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside modal content
    document.getElementById('authModal').addEventListener('click', function(e) {
        if (e.target === this) closeAuthModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAuthModal();
    });

    // Make it globally accessible
    window.showAuthModal = showAuthModal;
});
</script>
@endsection