// ==================== CONFIGURATION ====================

const CONFIG = {
    ANIMATION_DURATION: 600,
    FADE_DURATION: 300,
    DEBOUNCE_DELAY: 300,
    REQUEST_TIMEOUT: 10000,
    MAX_RETRIES: 2,
    STOCK_REFRESH_INTERVAL: 30000 // Refresh stock every 30 seconds
};

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/**
 * Debounce function to limit rapid calls
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ==================== REQUEST QUEUE MANAGER ====================

class RequestQueue {
    constructor() {
        this.queue = [];
        this.processing = false;
    }

    async add(requestFn) {
        return new Promise((resolve, reject) => {
            this.queue.push({ requestFn, resolve, reject });
            this.process();
        });
    }

    async process() {
        if (this.processing || this.queue.length === 0) return;

        this.processing = true;
        const { requestFn, resolve, reject } = this.queue.shift();

        try {
            const result = await requestFn();
            resolve(result);
        } catch (error) {
            reject(error);
        } finally {
            this.processing = false;
            this.process(); // Process next in queue
        }
    }
}

const requestQueue = new RequestQueue();

// ==================== CART COUNT MANAGEMENT ====================

function updateCartCount(count) {
    const cartBadges = document.querySelectorAll('.cart-count, #cart-count, [data-cart-count], .util__badge');
    
    cartBadges.forEach(badge => {
        // Announce to screen readers
        badge.setAttribute('aria-live', 'polite');
        badge.setAttribute('aria-atomic', 'true');
        
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
        
        // Add animation
        badge.classList.add('cart-updated');
        setTimeout(() => badge.classList.remove('cart-updated'), CONFIG.ANIMATION_DURATION);
    });
    
    // Update items count text
    const itemsCount = document.getElementById('cart-items-count');
    if (itemsCount) {
        itemsCount.textContent = `${count} ${count === 1 ? 'Item' : 'Items'}`;
    }
}

function initializeCartCount() {
    fetch('/cart/count', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        signal: AbortSignal.timeout(CONFIG.REQUEST_TIMEOUT)
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to fetch cart count');
        return response.json();
    })
    .then(data => {
        if (data && typeof data.cartCount !== 'undefined') {
            updateCartCount(data.cartCount);
        }
    })
    .catch(error => {
        console.error('Cart count initialization failed:', error);
        // Silently fail - don't disrupt user experience
    });
}

// ==================== CART TOTALS ====================

function updateCartTotals(total) {
    if (typeof total === 'undefined' || total === null) return;
    
    const formattedTotal = formatCurrency(total);
    
    ['cart-total', 'cart-total-bottom'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = formattedTotal;
        }
    });
}

// ==================== API REQUEST HANDLER WITH RETRY ====================

async function makeCartRequest(url, options = {}, retryCount = 0) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), CONFIG.REQUEST_TIMEOUT);

        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCSRFToken(),
                ...options.headers
            },
            signal: controller.signal,
            ...options
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        // Validate response structure
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response format');
        }

        return data;

    } catch (error) {
        // Retry logic for network errors
        if (retryCount < CONFIG.MAX_RETRIES && 
            (error.name === 'AbortError' || error.message.includes('fetch'))) {
            console.log(`Retrying request... (${retryCount + 1}/${CONFIG.MAX_RETRIES})`);
            await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1)));
            return makeCartRequest(url, options, retryCount + 1);
        }
        throw error;
    }
}

// ==================== BUTTON STATE MANAGEMENT ====================

function disableButton(button, loadingText = '...') {
    if (!button) return () => {};

    const originalText = button.textContent;
    const originalDisabled = button.disabled;
    
    button.disabled = true;
    button.textContent = loadingText;
    button.style.opacity = '0.5';
    button.style.cursor = 'not-allowed';
    button.setAttribute('aria-busy', 'true');
    
    return function restore() {
        button.textContent = originalText;
        button.disabled = originalDisabled;
        button.style.opacity = '';
        button.style.cursor = '';
        button.removeAttribute('aria-busy');
    };
}

// ==================== QUANTITY MANAGEMENT ====================

function updateQuantityButtons(row, quantity, maxStock) {
    const increaseBtn = row.querySelector('.quantity-increase');
    const decreaseBtn = row.querySelector('.quantity-decrease');
    
    if (decreaseBtn) {
        decreaseBtn.disabled = quantity <= 1;
        decreaseBtn.style.opacity = quantity <= 1 ? '0.5' : '1';
        decreaseBtn.style.cursor = quantity <= 1 ? 'not-allowed' : 'pointer';
        decreaseBtn.setAttribute('aria-label', `Decrease quantity (current: ${quantity})`);
    }
    
    if (increaseBtn) {
        const shouldDisable = quantity >= maxStock;
        increaseBtn.disabled = shouldDisable;
        increaseBtn.style.opacity = shouldDisable ? '0.5' : '1';
        increaseBtn.style.cursor = shouldDisable ? 'not-allowed' : 'pointer';
        increaseBtn.setAttribute('aria-label', 
            shouldDisable
                ? `Maximum stock reached (${maxStock})` 
                : `Increase quantity (current: ${quantity})`
        );
    }
}

function handleQuantityChange(row, isIncrease) {
    const input = row.querySelector('.quantity-input');
    const id = row.dataset.id;
    const price = parseFloat(row.dataset.price);
    const maxStock = parseInt(row.dataset.maxStock);
    let currentQty = parseInt(input.value);
    
    if (isNaN(currentQty) || currentQty < 1) currentQty = 1;
    
    let newQty = isIncrease ? currentQty + 1 : currentQty - 1;
    
    // Validate boundaries
    if (newQty < 1 || newQty > maxStock) return;
    
    // Queue the request to prevent race conditions
    requestQueue.add(() => updateQuantity(row, id, currentQty, newQty, price, maxStock));
}

// Debounced quantity update
const debouncedQuantityUpdate = debounce((row, id, oldQty, newQty, price, maxStock) => {
    requestQueue.add(() => updateQuantity(row, id, oldQty, newQty, price, maxStock));
}, CONFIG.DEBOUNCE_DELAY);

// ==================== STOCK REFRESH FOR CART ====================

async function refreshCartItemStock(row) {
    const id = row.dataset.id;
    const size = row.dataset.size;
    const productId = row.dataset.productId;
    
    if (!productId || !size) {
        console.log('Missing product ID or size for stock refresh');
        return;
    }
    
    try {
        const response = await fetch(`/products/${productId}/stock?size=${encodeURIComponent(size)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            signal: AbortSignal.timeout(CONFIG.REQUEST_TIMEOUT)
        });

        if (!response.ok) return;

        const data = await response.json();
        
        if (data && typeof data.stock !== 'undefined') {
            // Update the max stock in the row
            const oldMaxStock = parseInt(row.dataset.maxStock);
            row.dataset.maxStock = data.stock;
            
            const input = row.querySelector('.quantity-input');
            if (input) {
                input.setAttribute('max', data.stock);
            }
            
            const currentQty = parseInt(input?.value || 1);
            
            // Force re-enable buttons first
            const increaseBtn = row.querySelector('.quantity-increase');
            const decreaseBtn = row.querySelector('.quantity-decrease');
            
            if (increaseBtn) {
                increaseBtn.disabled = false;
                increaseBtn.style.opacity = '1';
                increaseBtn.style.cursor = 'pointer';
            }
            if (decreaseBtn) {
                decreaseBtn.disabled = false;
                decreaseBtn.style.opacity = '1';
                decreaseBtn.style.cursor = 'pointer';
            }
            
            // Small delay then update properly
            setTimeout(() => {
                updateQuantityButtons(row, currentQty, data.stock);
            }, 10);
            
            // Log stock changes for debugging
            if (oldMaxStock !== data.stock) {
                console.log(`Stock updated for cart item ${id}: ${oldMaxStock} → ${data.stock}`);
            }
        }
    } catch (error) {
        console.log('Stock refresh failed for item:', id, error.message);
    }
}

function refreshAllCartStocks() {
    const cartRows = document.querySelectorAll('.cart-row[data-product-id][data-size]');
    console.log(`Refreshing stock for ${cartRows.length} cart items`);
    cartRows.forEach(row => refreshCartItemStock(row));
}

async function updateQuantity(row, id, oldQty, newQty, price, maxStock) {
    const input = row.querySelector('.quantity-input');
    const increaseBtn = row.querySelector('.quantity-increase');
    const decreaseBtn = row.querySelector('.quantity-decrease');
    const subtotalElement = row.querySelector('.subtotal');
    
    const restoreIncrease = disableButton(increaseBtn, '...');
    const restoreDecrease = disableButton(decreaseBtn, '...');

    try {
        const data = await makeCartRequest('/cart/update', {
            method: 'POST',
            body: JSON.stringify({ 
                id: id, 
                quantity: newQty,
                _method: 'PATCH'
            })
        });

        if (!data.success) {
            throw new Error(data.message || 'Unable to update quantity');
        }
        
        // Update price if changed (for dynamic pricing)
        if (typeof data.price !== 'undefined') {
            row.dataset.price = data.price;
        }
        
        // Update stock with server response
        const updatedMaxStock = typeof data.maxStock !== 'undefined' ? data.maxStock : maxStock;
        row.dataset.maxStock = updatedMaxStock;
        input.setAttribute('max', updatedMaxStock);
        
        // Update UI
        input.value = newQty;
        if (subtotalElement && typeof data.subtotal !== 'undefined') {
            subtotalElement.textContent = formatCurrency(data.subtotal);
        }
        
        // Force re-enable buttons before updating state
        if (increaseBtn) {
            increaseBtn.disabled = false;
            increaseBtn.style.opacity = '1';
            increaseBtn.style.cursor = 'pointer';
        }
        if (decreaseBtn) {
            decreaseBtn.disabled = false;
            decreaseBtn.style.opacity = '1';
            decreaseBtn.style.cursor = 'pointer';
        }
        
        // Small delay then update properly
        setTimeout(() => {
            updateQuantityButtons(row, newQty, updatedMaxStock);
        }, 10);
        
        if (typeof data.total !== 'undefined') {
            updateCartTotals(data.total);
        }
        
        if (typeof data.cartCount !== 'undefined') {
            updateCartCount(data.cartCount);
        }

        // Announce to screen readers
        announceToScreenReader(`Quantity updated to ${newQty}`);
        
        // CRITICAL: Refresh actual stock from database after update
        // This ensures we have the most up-to-date stock information
        await refreshCartItemStock(row);

    } catch (error) {
        console.error('Error updating quantity:', error);
        
        // Show user-friendly error message
        showNotification('error', error.message || 'Failed to update quantity. Please try again.');
        
        // Revert to old quantity
        input.value = oldQty;
        updateQuantityButtons(row, oldQty, maxStock);
        
    } finally {
        restoreIncrease();
        restoreDecrease();
    }
}

// ==================== REMOVE FROM CART ====================

async function handleRemoveItem(button) {
    const row = button.closest('.cart-row');
    if (!row) return;
    
    const id = row.dataset.id;
    const productName = row.querySelector('.product-name')?.textContent || 'this item';

    if (!confirm(`Are you sure you want to remove ${productName}?`)) return;

    const restoreButton = disableButton(button, 'Removing...');

    try {
        const data = await requestQueue.add(() => 
            makeCartRequest('/cart/remove', {
                method: 'POST',
                body: JSON.stringify({ 
                    id: id,
                    _method: 'DELETE'
                })
            })
        );

        if (!data.success) {
            throw new Error(data.message || 'Unable to remove item');
        }
        
        // Fade out animation
        row.style.transition = `opacity ${CONFIG.FADE_DURATION}ms ease`;
        row.style.opacity = '0';
        
        setTimeout(() => {
            row.remove();
            
            if (typeof data.total !== 'undefined') {
                updateCartTotals(data.total);
            }
            
            if (typeof data.cartCount !== 'undefined') {
                updateCartCount(data.cartCount);
            }
            
            announceToScreenReader(`${productName} removed from cart`);
            
            // Refresh stock for remaining items
            refreshAllCartStocks();
            
            // Reload if cart is empty
            if (data.cartEmpty || data.itemCount === 0) {
                setTimeout(() => window.location.reload(), 500);
            }
        }, CONFIG.FADE_DURATION);

    } catch (error) {
        console.error('Error removing item:', error);
        showNotification('error', error.message || 'Failed to remove item. Please try again.');
        restoreButton();
    }
}

// ==================== NOTIFICATION SYSTEM ====================

function showNotification(type, message) {
    // Try to use existing toast system
    if (typeof window.showToast === 'function') {
        window.showToast(message);
        return;
    }
    
    // Fallback to alert
    alert(message);
}

function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('role', 'status');
    announcement.setAttribute('aria-live', 'polite');
    announcement.className = 'sr-only';
    announcement.textContent = message;
    document.body.appendChild(announcement);
    
    setTimeout(() => announcement.remove(), 3000);
}

// ==================== AUTH MODAL ====================

function showAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        modal.setAttribute('aria-hidden', 'false');
        
        // Focus trap
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        modal.setAttribute('aria-hidden', 'true');
    }
}

// ==================== EVENT LISTENERS INITIALIZATION ====================

function initQuantityButtons() {
    // Use event delegation for better performance
    document.addEventListener('click', (e) => {
        if (e.target.closest('.quantity-increase')) {
            const row = e.target.closest('.cart-row');
            if (row) handleQuantityChange(row, true);
        } else if (e.target.closest('.quantity-decrease')) {
            const row = e.target.closest('.cart-row');
            if (row) handleQuantityChange(row, false);
        }
    });
}

function initRemoveButtons() {
    // Use event delegation
    document.addEventListener('click', (e) => {
        if (e.target.closest('.remove-from-cart')) {
            handleRemoveItem(e.target.closest('.remove-from-cart'));
        }
    });
}

function initAuthModal() {
    const authModal = document.getElementById('authModal');
    if (authModal) {
        authModal.addEventListener('click', (e) => {
            if (e.target === authModal) closeAuthModal();
        });
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAuthModal();
    });
}

// ==================== INITIALIZATION ====================

document.addEventListener('DOMContentLoaded', function() {
    initializeCartCount();
    initQuantityButtons();
    initRemoveButtons();
    initAuthModal();
    
    // Initial stock refresh for all cart items
    setTimeout(() => {
        refreshAllCartStocks();
    }, 500); // Small delay to ensure DOM is fully ready
    
    // Periodic stock refresh (every 30 seconds)
    setInterval(refreshAllCartStocks, CONFIG.STOCK_REFRESH_INTERVAL);
    
    console.log('Cart page initialized with stock refresh');
});

// Handle browser back/forward navigation
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        initializeCartCount();
        refreshAllCartStocks();
    }
});

// Refresh stocks when page becomes visible again
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        console.log('Page visible - refreshing cart stocks');
        refreshAllCartStocks();
    }
});

// Refresh stocks when window regains focus
window.addEventListener('focus', () => {
    console.log('Window focused - refreshing cart stocks');
    refreshAllCartStocks();
});

// Expose only essential functions globally
window.cartAPI = {
    updateCartCount,
    showAuthModal,
    closeAuthModal,
    initializeCartCount,
    refreshCart: initializeCartCount,
    refreshStocks: refreshAllCartStocks // Added for manual stock refresh
};