<div id="toast-container">
    {{-- Toast Notification Structure --}}
    <div class="toast" id="cartConfirmToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-content">
            {{-- Icon (Checkmark) --}}
            <div class="toast-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            
            <div class="toast-text-group">
                <h3 class="toast-title">Added to Cart!</h3>
                {{-- The message will be updated here --}}
                <p class="toast-message" id="cartToastMessage">Product added successfully</p>
            </div>
            
            {{-- Call-to-action buttons --}}
            <div class="toast-actions">
                <a href="{{ url('view-cart') }}" class="toast-btn-primary" onclick="closeCartToast()">
                    View Cart
                </a>
            </div>
        </div>
        
        {{-- Close Button --}}
        <button type="button" class="toast-close" onclick="closeCartToast()" aria-label="Close notification">
            &times;
        </button>
    </div>
</div>