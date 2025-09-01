document.addEventListener("DOMContentLoaded", function () {
    // Get all necessary elements
    const decrease = document.getElementById("decrease");
    const increase = document.getElementById("increase");
    const quantity = document.getElementById("quantity");
    const stockMessage = document.getElementById("stock-message");
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const buyNowBtn = document.getElementById("buy-now-btn");
    
    const hiddenQuantityInputs = [
        document.getElementById("cart-quantity"),
        document.getElementById("cart-quantity-buy")
    ];
    
    const hiddenSizeInputs = [
        document.getElementById("selected-size"),
        document.getElementById("selected-size-buy")
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
        if (maxStock > 0) {
            stockMessage.textContent = `${maxStock} units available for size ${selectedSize}`;
            stockMessage.className = 'stock-message available';
        } else {
            stockMessage.textContent = `Size ${selectedSize} is out of stock`;
            stockMessage.className = 'stock-message unavailable';
        }
    }

    function updateActionButtons(stock) {
        if (stock > 0) {
            addToCartBtn.disabled = false;
            buyNowBtn.disabled = false;
            addToCartBtn.textContent = "Add to Cart";
            buyNowBtn.textContent = "Buy it now";
        } else {
            addToCartBtn.disabled = true;
            buyNowBtn.disabled = true;
            addToCartBtn.textContent = "Out of Stock";
            buyNowBtn.textContent = "Out of Stock";
        }
    }

    // Quantity controls
    decrease.addEventListener("click", () => {
        let val = parseInt(quantity.value);
        if (val > 1) {
            quantity.value = val - 1;
            updateHiddenQuantities(val - 1);
        }
    });

    increase.addEventListener("click", () => {
        let val = parseInt(quantity.value);
        if (val < maxStock) {
            quantity.value = val + 1;
            updateHiddenQuantities(val + 1);
        }
    });

    quantity.addEventListener("input", () => {
        let val = parseInt(quantity.value);
        
        // Ensure value is within bounds
        if (val > maxStock) {
            quantity.value = maxStock;
            val = maxStock;
        } else if (val < 1) {
            quantity.value = 1;
            val = 1;
        }
        
        updateHiddenQuantities(val);
    });
    
    // Initialize stock message on page load
    updateStockMessage();
    updateActionButtons(maxStock);
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
    quantityInput.max = stock;
    
    let currentQty = parseInt(quantityInput.value);
    if (currentQty > stock) {
        quantityInput.value = stock;
        currentQty = stock;
    }
    
    // Update all hidden inputs
    document.getElementById("cart-quantity").value = currentQty;
    document.getElementById("cart-quantity-buy").value = currentQty;
    document.getElementById("selected-size").value = size;
    document.getElementById("selected-size-buy").value = size;
    
    // Update UI elements
    const stockMessage = document.getElementById("stock-message");
    stockMessage.textContent = `${stock} units available for size ${size}`;
    stockMessage.className = 'stock-message available';
    
    // Update action buttons
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const buyNowBtn = document.getElementById("buy-now-btn");
    
    if (stock > 0) {
        addToCartBtn.disabled = false;
        buyNowBtn.disabled = false;
        addToCartBtn.textContent = "Add to Cart";
        buyNowBtn.textContent = "Buy it now";
    } else {
        addToCartBtn.disabled = true;
        buyNowBtn.disabled = true;
        addToCartBtn.textContent = "Out of Stock";
        buyNowBtn.textContent = "Out of Stock";
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