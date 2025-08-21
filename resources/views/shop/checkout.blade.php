@extends('layouts.default')
<link href="{{ asset('css/checkout.css') }}" rel="stylesheet" />

@section('maincontent')
<div class="checkout-container">
    <div class="checkout-header">
        <h1 class="checkout-title">Checkout</h1>
        <div class="checkout-steps">
            <div class="step active">
                <span class="step-number">1</span>
                <span class="step-label">Information</span>
            </div>
            <div class="step">
                <span class="step-number">2</span>
                <span class="step-label">Payment</span>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-label">Confirmation</span>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    @if(empty($cart))
        <div class="empty-cart-checkout">
            <div class="empty-cart-icon"></div>
            <h2>Your cart is empty</h2>
            <p>Add some products before proceeding to checkout.</p>
            <a href="{{ url('/home') }}" class="btn-shop">Continue Shopping</a>
        </div>
    @else
        <form action="{{ route('checkout') }}" method="POST" id="checkout-form">
            @csrf
            <div class="checkout-content">
                <!-- Left Column: Customer Information -->
                <div class="checkout-form">
                    <!-- Contact Information -->
                    <div class="form-section">
                        <h3 class="section-title">Contact Information</h3>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required 
                                       value="{{ old('email') }}" 
                                       placeholder="your@email.com">
                                @error('email')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required 
                                       value="{{ old('first_name') }}" 
                                       placeholder="First Name">
                                @error('first_name')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required 
                                       value="{{ old('last_name') }}" 
                                       placeholder="Last Name">
                                @error('last_name')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="phone">Phone Number *</label>
                                <input type="number" id="phone" name="phone" required 
                                       value="{{ old('phone') }}" 
                                       placeholder="+63 912 345 6789">
                                @error('phone')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="form-section">
                        <h3 class="section-title">Shipping Address</h3>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="address_line_1">Address Line 1 *</label>
                                <input type="text" id="address_line_1" name="address_line_1" required 
                                       value="{{ old('address_line_1') }}" 
                                       placeholder="House number, street name">
                                @error('address_line_1')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="address_line_2">Address Line 2 (Optional)</label>
                                <input type="text" id="address_line_2" name="address_line_2" 
                                       value="{{ old('address_line_2') }}" 
                                       placeholder="Apartment, suite, building">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" required 
                                       value="{{ old('city') }}" 
                                       placeholder="Quezon City">
                                @error('city')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="province">Province *</label>
                                <select id="province" name="province" required>
                                    <option value="">Select Province</option>
                                    <option value="Metro Manila" {{ old('province') == 'Metro Manila' ? 'selected' : '' }}>Metro Manila</option>
                                    <option value="Calabarzon" {{ old('province') == 'Calabarzon' ? 'selected' : '' }}>Calabarzon</option>
                                    <option value="Central Luzon" {{ old('province') == 'Central Luzon' ? 'selected' : '' }}>Central Luzon</option>
                                    <option value="Ilocos Region" {{ old('province') == 'Ilocos Region' ? 'selected' : '' }}>Ilocos Region</option>
                                    <option value="Cagayan Valley" {{ old('province') == 'Cagayan Valley' ? 'selected' : '' }}>Cagayan Valley</option>
                                    <option value="Central Visayas" {{ old('province') == 'Central Visayas' ? 'selected' : '' }}>Central Visayas</option>
                                    <option value="Western Visayas" {{ old('province') == 'Western Visayas' ? 'selected' : '' }}>Western Visayas</option>
                                    <option value="Eastern Visayas" {{ old('province') == 'Eastern Visayas' ? 'selected' : '' }}>Eastern Visayas</option>
                                    <option value="Northern Mindanao" {{ old('province') == 'Northern Mindanao' ? 'selected' : '' }}>Northern Mindanao</option>
                                    <option value="Davao Region" {{ old('province') == 'Davao Region' ? 'selected' : '' }}>Davao Region</option>
                                    <option value="Soccsksargen" {{ old('province') == 'Soccsksargen' ? 'selected' : '' }}>Soccsksargen</option>
                                    <option value="Other" {{ old('province') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('province')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="postal_code">Postal Code *</label>
                                <input type="text" id="postal_code" name="postal_code" required 
                                       value="{{ old('postal_code') }}" 
                                       placeholder="1100">
                                @error('postal_code')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="country">Country *</label>
                                <select id="country" name="country" required>
                                    <option value="Philippines" {{ old('country', 'Philippines') == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                                </select>
                                @error('country')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="form-section">
                        <h3 class="section-title">Shipping Method</h3>
                        <div class="shipping-options">
                            <div class="shipping-option">
                                <input type="radio" id="standard" name="shipping_method" value="standard" 
                                       data-price="50" {{ old('shipping_method', 'standard') == 'standard' ? 'checked' : '' }}>
                                <label for="standard">
                                    <div class="shipping-info">
                                        <span class="shipping-name">Standard Delivery</span>
                                        <span class="shipping-time">5-7 business days</span>
                                    </div>
                                    <span class="shipping-price">₱50.00</span>
                                </label>
                            </div>
                            <div class="shipping-option">
                                <input type="radio" id="express" name="shipping_method" value="express" 
                                       data-price="120" {{ old('shipping_method') == 'express' ? 'checked' : '' }}>
                                <label for="express">
                                    <div class="shipping-info">
                                        <span class="shipping-name">Express Delivery</span>
                                        <span class="shipping-time">2-3 business days</span>
                                    </div>
                                    <span class="shipping-price">₱120.00</span>
                                </label>
                            </div>
                            <div class="shipping-option">
                                <input type="radio" id="overnight" name="shipping_method" value="overnight" 
                                       data-price="200" {{ old('shipping_method') == 'overnight' ? 'checked' : '' }}>
                                <label for="overnight">
                                    <div class="shipping-info">
                                        <span class="shipping-name">Overnight Delivery</span>
                                        <span class="shipping-time">Next business day</span>
                                    </div>
                                    <span class="shipping-price">₱200.00</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h3 class="section-title">Payment Method</h3>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="cod" name="payment_method" value="cod" 
                                       {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                <label for="cod">
                                    <span class="payment-icon">💵</span>
                                    <span>Cash on Delivery</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="gcash" name="payment_method" value="gcash" 
                                       {{ old('payment_method') == 'gcash' ? 'checked' : '' }}>
                                <label for="gcash">
                                    <span class="payment-icon">📱</span>
                                    <span>GCash</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" 
                                       {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                <label for="bank_transfer">
                                    <span class="payment-icon">🏦</span>
                                    <span>Bank Transfer</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="form-section">
                        <h3 class="section-title">Special Instructions (Optional)</h3>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="special_instructions">Delivery Notes</label>
                                <textarea id="special_instructions" name="special_instructions" rows="3" 
                                          placeholder="Any special delivery instructions...">{{ old('special_instructions') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="order-summary">
                    <h3 class="summary-title">Order Summary</h3>
                    
                    <div class="cart-items">
                        @php $subtotal = 0; @endphp
                        @foreach($cart as $id => $item)
                            @php 
                                $itemTotal = $item['price'] * $item['quantity'];
                                $subtotal += $itemTotal;
                            @endphp
                            <div class="cart-item">
                                <div class="item-image">
                                    <img src="/product/{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                    <span class="item-quantity">{{ $item['quantity'] }}</span>
                                </div>
                                <div class="item-details">
                                    <div class="item-name">{{ $item['name'] }}</div>
                                    <div class="item-size">Size: {{ $item['size'] ?? 'M' }}</div>
                                    @if(isset($item['discount_price']) && $item['discount_price'] > 0 && $item['discount_price'] < $item['original_price'])
                                        <div class="item-price">
                                            <span class="price-discounted">₱{{ number_format($item['discount_price'], 2) }}</span>
                                            <span class="price-original">₱{{ number_format($item['original_price'], 2) }}</span>
                                        </div>
                                    @else
                                        <div class="item-price">₱{{ number_format($item['price'], 2) }}</div>
                                    @endif
                                </div>
                                <div class="item-total">₱{{ number_format($itemTotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="order-totals">
                        <div class="total-line">
                            <span>Subtotal</span>
                            <span id="subtotal-amount">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="total-line">
                            <span>Shipping</span>
                            <span id="shipping-amount">₱50.00</span>
                        </div>
                        <div class="total-line total">
                            <span>Total</span>
                            <span id="total-amount">₱{{ number_format($subtotal + 50, 2) }}</span>
                        </div>
                    </div>

                    <div class="checkout-actions">
                        <a href="{{ route('view-cart') }}" class="btn-back">← Back to Cart</a>
                        <button type="submit" class="btn-place-order">Place Order</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotal = {{ $subtotal ?? 0 }};
    const shippingOptions = document.querySelectorAll('input[name="shipping_method"]');
    const subtotalElement = document.getElementById('subtotal-amount');
    const shippingElement = document.getElementById('shipping-amount');
    const totalElement = document.getElementById('total-amount');

    function updateOrderTotal() {
        const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
        const shippingPrice = selectedShipping ? parseFloat(selectedShipping.dataset.price) : 50;
        const total = subtotal + shippingPrice;

        shippingElement.textContent = '₱' + shippingPrice.toFixed(2);
        totalElement.textContent = '₱' + total.toFixed(2);
    }

    // Update totals when shipping method changes
    shippingOptions.forEach(option => {
        option.addEventListener('change', updateOrderTotal);
    });

    // Form validation
    const checkoutForm = document.getElementById('checkout-form');
    checkoutForm.addEventListener('submit', function(e) {
        const requiredFields = checkoutForm.querySelectorAll('[required]');
        let hasErrors = false;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                hasErrors = true;
            } else {
                field.classList.remove('error');
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }

        // Show loading state
        const submitBtn = checkoutForm.querySelector('.btn-place-order');
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
    });

    // Remove error styling on input
    const inputs = checkoutForm.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
        });
    });
});
</script>
@endsection