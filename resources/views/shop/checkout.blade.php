@extends('layouts.default')
<link href="{{ asset('css/checkout.css') }}" rel="stylesheet" />

@section('maincontent')
<div class="checkout-container">
    <h1 class="checkout-title">Checkout</h1>

    @if(session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    @if(empty($cart))
        <div class="empty-cart-checkout">
            <h2>Your cart is empty</h2>
            <p>Add some products before proceeding to checkout.</p>
            <a href="{{ url('/home') }}" class="btn-shop">Continue Shopping</a>
        </div>
    @else
        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            <div class="checkout-content">
                <!-- Customer Information -->
                <div class="checkout-form">
                    <h3>Contact Information</h3>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" id="phone" name="phone" required placeholder="+63 912 345 6789">
                    </div>

                    <h3>Billing Address</h3>
                    
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country" required>
                            <option value="Philippines" selected>Philippines</option>
                        </select>
                    </div>

                    <div class="address-grid">
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" id="province" name="province" required placeholder="e.g. Cavite, Laguna, Cebu">
                        </div>
                        <div class="form-group">
                            <label for="city">City / Municipality</label>
                            <input type="text" id="city" name="city" required placeholder="e.g. Quezon City, Taguig">
                        </div>
                    </div>

                    <div class="address-grid">
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <input type="text" id="barangay" name="barangay" required placeholder="e.g. Barangay 123">
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" required placeholder="e.g. 1100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="billing_address">Street Address</label>
                        <input type="text" id="billing_address" name="billing_address" required placeholder="House number, street name, building">
                    </div>

                    <h3>Shipping Options</h3>
                    <div class="form-group">
                        <label for="delivery_option">Delivery Options</label>
                        <select name="delivery_option" id="delivery_option" required>
                            <option value="">Select delivery option</option>
                            <option value="ship">Ship to Address</option>
                            <option value="pickup">Store Pickup</option>
                        </select>
                    </div>

                    <h3>Shipping Address</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="same_as_billing" name="same_as_billing" checked>
                            Same as Billing Address
                        </label>
                    </div>
                    <div class="form-group" id="shipping_address_group">
                        <label for="shipping_address">Complete Shipping Address</label>
                        <input type="text" id="shipping_address" name="shipping_address" placeholder="Enter complete shipping address if different">
                    </div>

                    <h3>Payment Method</h3>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="paypal">Paypal</option>
                            <option value="maya">Maya Digital Wallet</option>
                            <option value="gcash">GCash Digital Wallet</option>
                        </select>
                    </div>
                </div>

                <div class="order-summary">
                <h3>Order Summary</h3>
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
                                <div class="item-name">
                                    {{ $item['name'] }}<br>
                                    @if(isset($item['size']) && $item['size'])
                                        <span class="item-size">Size: {{ $item['size'] }}</span>
                                    @endif
                                </div>
                                @if(isset($item['discount_price']) && $item['discount_price'] < $item['price'])
                                    <div class="item-price">
                                        <span class="price-discounted">₱{{ number_format($item['discount_price'], 2) }}</span>
                                        <span class="price-original">₱{{ number_format($item['price'], 2) }}</span>
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
                            <span id="shipping-amount">Calculated at next step</span>
                        </div>
                        <div class="total-line total">
                            <span>Total</span>
                            <span id="total-amount">₱{{ number_format($subtotal, 2) }}</span>
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
    const sameAsBillingCheckbox = document.getElementById('same_as_billing');
    const shippingAddressGroup = document.getElementById('shipping_address_group');
    const form = document.getElementById('checkout-form');
    const submitButton = document.querySelector('.btn-place-order');

    // Toggle shipping address visibility based on checkbox
    sameAsBillingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            shippingAddressGroup.style.display = 'none';
            document.getElementById('shipping_address').removeAttribute('required');
        } else {
            shippingAddressGroup.style.display = 'block';
            document.getElementById('shipping_address').setAttribute('required', 'required');
        }
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        submitButton.classList.add('loading');
        submitButton.disabled = true;
        
        // Re-enable button after 5 seconds as fallback
        setTimeout(() => {
            submitButton.classList.remove('loading');
            submitButton.disabled = false;
        }, 5000);
    });

    // Enhanced form validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
    });

    // Phone number formatting (Philippine format)
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Ensure it starts with +63 for Philippine numbers
        if (value.length > 0 && !value.startsWith('63')) {
            if (value.startsWith('0')) {
                value = '63' + value.substring(1);
            } else if (value.startsWith('9')) {
                value = '63' + value;
            }
        }
        
        // Format the number
        if (value.length >= 2) {
            value = '+' + value.substring(0, 2) + ' ' + value.substring(2);
        }
        if (value.length > 6) {
            value = value.substring(0, 6) + ' ' + value.substring(6);
        }
        if (value.length > 10) {
            value = value.substring(0, 10) + ' ' + value.substring(10, 14);
        }
        
        e.target.value = value;
    });
});
</script>
@endsection