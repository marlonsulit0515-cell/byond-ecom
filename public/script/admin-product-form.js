// Product Form Utilities
const ProductForm = {
    // Image preview function
    previewImage: function(input, previewId) {
        const preview = document.getElementById(previewId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
        }
    },

    // Validate discount price
    validateDiscountPrice: function() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const disPrice = parseFloat(document.getElementById('dis_price').value) || 0;
        const errorMsg = document.getElementById('discountError');
        const disPriceInput = document.getElementById('dis_price');
        
        if (disPrice > 0) {
            if (price === 0) {
                errorMsg.textContent = 'Please enter regular price first';
                errorMsg.classList.remove('hidden');
                disPriceInput.classList.add('border-red-500');
                return false;
            } else if (disPrice >= price) {
                errorMsg.textContent = 'Discount price must be lower than regular price';
                errorMsg.classList.remove('hidden');
                disPriceInput.classList.add('border-red-500');
                return false;
            } else {
                errorMsg.classList.add('hidden');
                disPriceInput.classList.remove('border-red-500');
                return true;
            }
        } else {
            errorMsg.classList.add('hidden');
            disPriceInput.classList.remove('border-red-500');
            return true;
        }
    },

    // Initialize form validation
    initFormValidation: function(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!ProductForm.validateDiscountPrice()) {
                    e.preventDefault();
                    alert('Please fix the pricing errors before submitting.');
                    return false;
                }
            });
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check which form exists and initialize it
    if (document.getElementById('productForm')) {
        ProductForm.initFormValidation('productForm');
    }
    if (document.getElementById('editProductForm')) {
        ProductForm.initFormValidation('editProductForm');
    }
});

// Make functions globally accessible for inline handlers
window.previewImage = ProductForm.previewImage;
window.validateDiscountPrice = ProductForm.validateDiscountPrice;