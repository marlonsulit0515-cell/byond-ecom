// --- Image Gallery Logic ---

/**
 * Changes the main product image to the thumbnail image clicked.
 * @param {string} newImageSrc - The new source URL for the main image.
 * @param {HTMLElement} clickedThumbnail - The thumbnail element that was clicked.
 */
function changeMainImage(newImageSrc, clickedThumbnail) {
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = document.querySelectorAll('.thumbnail');

    if (mainImage) {
        mainImage.src = newImageSrc;
    }

    // Update active state on thumbnails
    thumbnails.forEach(thumb => thumb.classList.remove('active'));
    clickedThumbnail.classList.add('active');
}

// --- Size and Quantity Logic ---

// Variables must be initialized globally or passed from the server.
// These variables will be set by the Blade file's inline script section.
let selectedSize = ''; // Will be set by the Blade file
let maxStock = 1;      // Will be set by the Blade file

/**
 * Selects a product size and updates max stock/form values.
 * @param {string} size - The selected size (e.g., 'S', 'M').
 * @param {number} stock - The stock quantity for the selected size.
 */
function selectSize(size, stock) {
    selectedSize = size;
    maxStock = stock;
    
    // Update active class on size buttons
    const sizeButtons = document.querySelectorAll('.size-btn');
    sizeButtons.forEach(btn => btn.classList.remove('active'));
    
    const selectedButton = document.querySelector(`.size-btn[data-size="${size}"]`);
    if (selectedButton) {
        selectedButton.classList.add('active');
    }

    // Update the quantity input and hidden form fields
    const qtyInput = document.getElementById('quantity');
    
    // Set max stock for the quantity input
    qtyInput.max = maxStock;
    
    // Reset quantity if current value exceeds new max stock
    if (parseInt(qtyInput.value) > maxStock) {
        qtyInput.value = maxStock > 0 ? 1 : 0; // Reset to 1 or 0 if out of stock
    }
    
    // Update hidden size fields in forms
    document.getElementById('selected-size').value = size;
    document.getElementById('buy-size').value = size;
}

/**
 * Handles quantity increment and decrement.
 * @param {number} amount - The amount to change quantity by (+1 or -1).
 */
function handleQuantityChange(amount) {
    const qtyInput = document.getElementById('quantity');
    let currentQty = parseInt(qtyInput.value);
    
    // Ensure quantity is a valid number
    if (isNaN(currentQty)) currentQty = 1;

    let newQty = currentQty + amount;
    
    // Clamp the value based on min (1) and maxStock
    if (newQty < 1) newQty = 1;
    if (newQty > maxStock) newQty = maxStock;

    qtyInput.value = newQty;
    
    // Manually update hidden quantity fields
    document.getElementById('cart-quantity').value = newQty;
    document.getElementById('buy-quantity').value = newQty;
}

// --- Utility Functions ---

function showToast(message) {
    const toast = document.getElementById('toast-notification');
    if (toast) {
        toast.textContent = message;
        toast.classList.add('show');
    
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2000);
    }
}

function updateCartCount(count) {
    const cartBadges = document.querySelectorAll('.cart-count, #cart-count, [data-cart-count]');
    cartBadges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
        badge.classList.add('cart-updated');
        setTimeout(() => {
            badge.classList.remove('cart-updated');
        }, 600);
    });
}

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


// --- Event Listeners and Initialization ---

document.addEventListener('DOMContentLoaded', () => {
    const qtyInput = document.getElementById('quantity');

    // Attach quantity change handlers
    document.getElementById('increase')?.addEventListener('click', () => handleQuantityChange(1));
    document.getElementById('decrease')?.addEventListener('click', () => handleQuantityChange(-1));

    // Listen for manual input changes on quantity field
    qtyInput?.addEventListener('input', (e) => {
        let currentQty = parseInt(e.target.value);
        if (isNaN(currentQty) || currentQty < 1) currentQty = 1;
        if (currentQty > maxStock) currentQty = maxStock;
        
        e.target.value = currentQty;
        
        document.getElementById('cart-quantity').value = currentQty;
        document.getElementById('buy-quantity').value = currentQty;
    });

    // Handle Auth Modal closing by clicking outside
    document.getElementById('authModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'authModal') closeAuthModal();
    });

    // --- AJAX Cart Submission ---
    document.getElementById('add-to-cart-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Final validation before submission
        if (selectedSize === '' || maxStock <= 0) {
            showToast("Please select a valid size.");
            return;
        }

        const formData = new FormData(this);
        const actionUrl = this.action;
        const submitButton = this.querySelector('button[type="submit"]');
        
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Adding...';
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                // Check for 422 Unprocessable Entity (Validation error)
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Validation failed.');
                    });
                }
                throw new Error('Network response was not ok, status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateCartCount(data.cartCount);
                showToast(data.message || "Added to cart");
            } else {
                showToast(data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error: ' + error.message);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });
});

// --- Expose functions globally for Blade/inline use ---
window.changeMainImage = changeMainImage;
window.selectSize = selectSize;
window.showAuthModal = showAuthModal;
window.closeAuthModal = closeAuthModal;
window.updateCartCount = updateCartCount;
window.showToast = showToast;