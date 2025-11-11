<div class="modal-overlay" id="authModal" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <button class="modal-close" onclick="closeAuthModal()" aria-label="Close">×</button>
            <div class="modal-icon"></div>
            <h2 class="modal-title">Login Required</h2>
            <p class="modal-subtitle">Authentication needed to proceed</p>
        </div>
        
        <div class="modal-body">
            <p class="modal-message">
                You must be logged in to proceed to checkout.
            </p>
            <p class="modal-description">
                Please login to your existing account or create a new one to complete your purchase.
            </p>
            
            <div class="modal-actions">
                <a href="{{ route('login') }}" class="btn-primary-color btn-md">
                     Login
                </a>
                <a href="{{ route('register') }}" class="btn-secondary-color btn-md">
                     Sign Up
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showAuthModal() {
        const modal = document.getElementById('authModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAuthModal() {
        const modal = document.getElementById('authModal');
        modal.style.display = 'none';
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
</script>
@endpush
