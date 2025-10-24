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
const updateCartTotals = (cartData) => {
    let total = 0;
    cartData.forEach(item => total += item.price * item.quantity);
    document.getElementById('cart-total').textContent = '₱' + total.toFixed(2);
    document.getElementById('cart-total-bottom').textContent = '₱' + total.toFixed(2);
};

document.querySelectorAll('.quantity-increase, .quantity-decrease').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('.cart-row');
        const input = row.querySelector('.quantity-input');
        const id = this.dataset.id;
        const max = parseInt(this.dataset.max || input.max);
        let newQty = parseInt(input.value);

        if (this.classList.contains('quantity-increase') && newQty < max) newQty++;
        if (this.classList.contains('quantity-decrease') && newQty > 1) newQty--;

        if (newQty !== parseInt(input.value)) {
            input.value = newQty;

            fetch('{{ route("update-cart") }}', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ id, quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optionally update subtotal for this row
                    const price = parseFloat(row.querySelector('.subtotal').textContent.replace('₱','')) / parseInt(input.value);
                    row.querySelector('.subtotal').textContent = '₱' + (price * newQty).toFixed(2);
                    // Reload page for flash messages
                    window.location.reload();
                }
            });
        }
    });
});

document.querySelectorAll('.remove-from-cart').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('.cart-row');
        const id = row.dataset.id;

        if (!confirm('Are you sure you want to remove this item?')) return;

        fetch('{{ route("remove-from-cart") }}', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.reload();
        });
    });
});

// Auth modal functions
function showAuthModal() { document.getElementById('authModal').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
function closeAuthModal() { document.getElementById('authModal').style.display = 'none'; document.body.style.overflow = 'auto'; }
window.showAuthModal = showAuthModal;
window.closeAuthModal = closeAuthModal;