<link rel="stylesheet" href="{{ asset('css/auth-modal.css') }}" />

<div class="modal-overlay" id="cartConfirmModal" style="display: none;">
    <div class="modal">
        <!-- Modal Header -->
        <div class="modal-header">
            <button class="modal-close" onclick="closeCartModal()" aria-label="Close">×</button>
            <div class="modal-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2 class="modal-title">Added to Cart!</h2>
            <p class="modal-subtitle" id="cartModalMessage">Product added successfully</p>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <p class="modal-message">
                Your item has been successfully added to your shopping cart.
            </p>

            <div class="modal-actions">
                <button onclick="closeCartModal()" class="btn btn-secondary">
                    Continue Shopping
                </button>
                <a href="{{ url('view-cart') }}" class="btn btn-primary">
                    View Cart
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showCartModal(message = 'Product added successfully') {
        const modal = document.getElementById('cartConfirmModal');
        const messageEl = document.getElementById('cartModalMessage');

        messageEl.textContent = message;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeCartModal() {
        const modal = document.getElementById('cartConfirmModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside the modal
    document.getElementById('cartConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeCartModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCartModal();
    });

    // Make available globally
    window.showCartModal = showCartModal;
</script>
@endpush
