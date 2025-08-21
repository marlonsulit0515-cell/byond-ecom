    document.addEventListener("DOMContentLoaded", function () {
        const decrease = document.getElementById("decrease");
        const increase = document.getElementById("increase");
        const quantity = document.getElementById("quantity");
        const stockMessage = document.getElementById("stock-message");
        
        const hiddenQuantityInputs = [
            document.getElementById("cart-quantity"),
            document.getElementById("cart-quantity-buy")
        ];
        
        const hiddenSizeInputs = [
            document.getElementById("selected-size"),
            document.getElementById("selected-size-buy")
        ];

        function updateHiddenQuantities(val) {
            hiddenQuantityInputs.forEach(input => input.value = val);
        }
        
        function updateHiddenSizes(size) {
            hiddenSizeInputs.forEach(input => input.value = size);
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
            if (val > maxStock) {
                quantity.value = maxStock;
                val = maxStock;
            }
            updateHiddenQuantities(val);
        });
        
        // Initialize stock message
        updateStockMessage();
    });

    // Size selection function
    function selectSize(size, stock) {
        if (stock <= 0) return;
        
        // Update active size button
        document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-size="${size}"]`).classList.add('active');
        
        // Update global variables
        selectedSize = size;
        maxStock = stock;
        
        // Update quantity limits
        const quantityInput = document.getElementById("quantity");
        quantityInput.max = stock;
        
        // Reset quantity if it exceeds new stock limit
        if (parseInt(quantityInput.value) > stock) {
            quantityInput.value = stock;
            document.getElementById("cart-quantity").value = stock;
            document.getElementById("cart-quantity-buy").value = stock;
        }
        
        // Update hidden size inputs
        document.getElementById("selected-size").value = size;
        document.getElementById("selected-size-buy").value = size;
        
        // Update stock message
        const stockMessage = document.getElementById("stock-message");
        stockMessage.textContent = `${stock} units available for size ${size}`;
        stockMessage.className = 'stock-message available';
        
        // Enable/disable action buttons based on stock
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
        document.getElementById('mainProductImage').src = imageSrc;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
        thumbnailElement.classList.add('active');
    }