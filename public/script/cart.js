// Utility Functions
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function updateCartCount(count) {
    // Update all cart badge elements
    const cartBadges = document.querySelectorAll('.cart-count, #cart-count, [data-cart-count], .util__badge');
    
    cartBadges.forEach(badge => {
        badge.textContent = count;
        
        // Show/hide based on count
        if (count > 0) {
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
        
        // Add animation
        badge.classList.add('cart-updated');
        setTimeout(() => badge.classList.remove('cart-updated'), 600);
    });
    
    // Update items count text
    const itemsCount = document.getElementById('cart-items-count');
    if (itemsCount) {
        itemsCount.textContent = `${count} ${count === 1 ? 'Item' : 'Items'}`;
    }
}

function updateCartTotals(total) {
    const formattedTotal = formatCurrency(total);
    
    const cartTotal = document.getElementById('cart-total');
    if (cartTotal) cartTotal.textContent = formattedTotal;
    
    const cartTotalBottom = document.getElementById('cart-total-bottom');
    if (cartTotalBottom) cartTotalBottom.textContent = formattedTotal;
}

function disableButtonTemporarily(button, loadingText = '...') {
    const originalText = button.textContent;
    const originalDisabled = button.disabled;
    
    button.disabled = true;
    button.textContent = loadingText;
    button.style.opacity = '0.5';
    button.style.cursor = 'not-allowed';
    
    return function restore() {
        button.disabled = originalDisabled;
        button.textContent = originalText;
        button.style.opacity = '';
        button.style.cursor = '';
    };
}

function makeCartRequest(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    return fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            ...options.headers
        },
        ...options
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    });
}

// Quantity Update Handler
function initQuantityButtons() {
    document.querySelectorAll('.quantity-increase, .quantity-decrease').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('.cart-row');
            const input = row.querySelector('.quantity-input');
            const id = row.dataset.id;
            const price = parseFloat(row.dataset.price);
            const maxStock = parseInt(row.dataset.maxStock);
            let currentQty = parseInt(input.value);
            let newQty = currentQty;

            if (this.classList.contains('quantity-increase') && currentQty < maxStock) {
                newQty++;
            } else if (this.classList.contains('quantity-decrease') && currentQty > 1) {
                newQty--;
            }

            if (newQty !== currentQty) {
                updateQuantity(row, id, newQty, price, maxStock);
            }
        });
    });
}

function updateQuantity(row, id, newQty, price, maxStock) {
    const input = row.querySelector('.quantity-input');
    const increaseBtn = row.querySelector('.quantity-increase');
    const decreaseBtn = row.querySelector('.quantity-decrease');
    const subtotalElement = row.querySelector('.subtotal');
    
    // Disable buttons during update
    const restoreIncrease = disableButtonTemporarily(increaseBtn, '...');
    const restoreDecrease = disableButtonTemporarily(decreaseBtn, '...');

    // Use global route or fallback
    const updateRoute = window.cartRoutes?.update || '/cart/update';

    makeCartRequest(updateRoute, {
        method: 'POST',
        body: JSON.stringify({ 
            id: id, 
            quantity: newQty,
            _method: 'PATCH'
        })
    })
    .then(data => {
        if (data.success) {
            // Update price if it changed
            const currentPrice = data.price || price;
            
            if (data.price) {
                row.dataset.price = data.price;
            }
            
            // Update UI
            input.value = newQty;
            subtotalElement.textContent = formatCurrency(data.subtotal);
            
            // Update button states
            decreaseBtn.disabled = newQty <= 1;
            increaseBtn.disabled = newQty >= maxStock;
            
            // Update totals
            updateCartTotals(data.total);
            if (data.cartCount !== undefined) {
                updateCartCount(data.cartCount);
            }
        } else {
            alert(data.message || 'Unable to update quantity');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the cart');
    })
    .finally(() => {
        restoreIncrease();
        restoreDecrease();
    });
}

// Remove Item Handler
function initRemoveButtons() {
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('.cart-row');
            const id = row.dataset.id;

            if (!confirm('Are you sure you want to remove this item?')) return;

            const restoreButton = disableButtonTemporarily(button, 'Removing...');

            const removeRoute = window.cartRoutes?.remove || '/cart/remove';

            makeCartRequest(removeRoute, {
                method: 'POST',
                body: JSON.stringify({ 
                    id: id,
                    _method: 'DELETE'
                })
            })
            .then(data => {
                if (data.success) {
                    // Fade out animation
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        // Update totals and counts
                        updateCartTotals(data.total);
                        if (data.cartCount !== undefined) {
                            updateCartCount(data.cartCount);
                        }
                        
                        // Reload if cart is empty
                        if (data.cartEmpty || data.itemCount === 0) {
                            window.location.reload();
                        }
                    }, 300);
                } else {
                    alert(data.message || 'Unable to remove item');
                    restoreButton();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item');
                restoreButton();
            });
        });
    });
}

// Auth Modal Functions
function showAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initQuantityButtons();
    initRemoveButtons();
    
    // Auth modal event listeners
    const authModal = document.getElementById('authModal');
    if (authModal) {
        authModal.addEventListener('click', function(e) {
            if (e.target === this) closeAuthModal();
        });
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAuthModal();
    });
    
    // Make functions globally accessible
    window.showAuthModal = showAuthModal;
    window.closeAuthModal = closeAuthModal;
    window.updateCartCount = updateCartCount;
});