document.addEventListener("DOMContentLoaded", function () {
    // Get all necessary elements
    const decrease = document.getElementById("decrease");
    const increase = document.getElementById("increase");
    const quantity = document.getElementById("quantity");
    const stockMessage = document.getElementById("stock-message");
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const buyNowBtn = document.querySelector(".btn-buy"); // Use class selector instead
    
    const hiddenQuantityInputs = [
        document.getElementById("cart-quantity"),
        document.getElementById("buy-quantity") // Fixed ID
    ];
    
    const hiddenSizeInputs = [
        document.getElementById("selected-size"),
        document.getElementById("buy-size") // Fixed ID
    ];

    // Helper functions
    function updateHiddenQuantities(val) {
        hiddenQuantityInputs.forEach(input => {
            if (input) input.value = val;
        });
    }
    
    function updateHiddenSizes(size) {
        hiddenSizeInputs.forEach(input => {
            if (input) input.value = size;
        });
    }

    function updateStockMessage() {
        if (!stockMessage) return;
        
        if (maxStock > 0) {
            stockMessage.textContent = `${maxStock} units available for size ${selectedSize}`;
            stockMessage.className = 'stock-message available';
        } else {
            stockMessage.textContent = `Size ${selectedSize} is out of stock`;
            stockMessage.className = 'stock-message unavailable';
        }
    }

    function updateActionButtons(stock) {
        // Only disable if stock is actually 0, not if stock is undefined
        const isOutOfStock = stock <= 0;
        
        if (addToCartBtn) {
            addToCartBtn.disabled = isOutOfStock;
            addToCartBtn.textContent = isOutOfStock ? "Out of Stock" : "Add to Cart";
        }
        
        // Only update Buy Now button if it's a submit button (logged in user)
        // Guest users have onclick button, don't disable that
        if (buyNowBtn && buyNowBtn.type === "submit") {
            buyNowBtn.disabled = isOutOfStock;
            buyNowBtn.textContent = isOutOfStock ? "Out of Stock" : "Buy it now";
        }
    }

    // Quantity controls
    if (decrease) {
        decrease.addEventListener("click", () => {
            let val = parseInt(quantity.value);
            if (val > 1) {
                quantity.value = val - 1;
                updateHiddenQuantities(val - 1);
            }
        });
    }

    if (increase) {
        increase.addEventListener("click", () => {
            let val = parseInt(quantity.value);
            if (val < maxStock) {
                quantity.value = val + 1;
                updateHiddenQuantities(val + 1);
            }
        });
    }

    if (quantity) {
        quantity.addEventListener("input", () => {
            let val = parseInt(quantity.value);
            
            // Ensure value is within bounds
            if (val > maxStock) {
                quantity.value = maxStock;
                val = maxStock;
            } else if (val < 1 || isNaN(val)) {
                quantity.value = 1;
                val = 1;
            }
            
            updateHiddenQuantities(val);
        });
    }
    
    // Initialize stock message and buttons on page load
    // Only if maxStock is defined and valid
    if (typeof maxStock !== 'undefined' && maxStock > 0) {
        updateStockMessage();
        updateActionButtons(maxStock);
    }
});

// Global functions (called from HTML onclick events)
function selectSize(size, stock) {
    if (stock <= 0) return;
    
    // Update active size button
    document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
    const selectedBtn = document.querySelector(`[data-size="${size}"]`);
    if (selectedBtn) selectedBtn.classList.add('active');
    
    // Update global variables
    selectedSize = size;
    maxStock = stock;
    
    // Update quantity limits and reset if needed
    const quantityInput = document.getElementById("quantity");
    if (quantityInput) {
        quantityInput.max = stock;
        
        let currentQty = parseInt(quantityInput.value);
        if (currentQty > stock || isNaN(currentQty)) {
            quantityInput.value = Math.min(currentQty, stock);
            currentQty = quantityInput.value;
        }
        
        // Update all hidden inputs with correct IDs
        const cartQty = document.getElementById("cart-quantity");
        const buyQty = document.getElementById("buy-quantity"); // Fixed ID
        const selectedSizeInput = document.getElementById("selected-size");
        const selectedSizeBuyInput = document.getElementById("buy-size"); // Fixed ID
        
        if (cartQty) cartQty.value = currentQty;
        if (buyQty) buyQty.value = currentQty;
        if (selectedSizeInput) selectedSizeInput.value = size;
        if (selectedSizeBuyInput) selectedSizeBuyInput.value = size;
    }
    
    // Update UI elements
    const stockMessage = document.getElementById("stock-message");
    if (stockMessage) {
        stockMessage.textContent = `${stock} units available for size ${size}`;
        stockMessage.className = 'stock-message available';
    }
    
    // Update action buttons
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const buyNowBtn = document.querySelector(".btn-buy"); // Use class selector
    
    const isInStock = stock > 0;
    
    if (addToCartBtn) {
        addToCartBtn.disabled = !isInStock;
        addToCartBtn.textContent = isInStock ? "Add to Cart" : "Out of Stock";
    }
    
    // Only disable submit-type Buy Now buttons
    if (buyNowBtn && buyNowBtn.type === "submit") {
        buyNowBtn.disabled = !isInStock;
        buyNowBtn.textContent = isInStock ? "Buy it now" : "Out of Stock";
    }
}

// Image gallery function
function changeMainImage(imageSrc, thumbnailElement) {
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) {
        mainImage.src = imageSrc;
    }
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    if (thumbnailElement) {
        thumbnailElement.classList.add('active');
    }
}