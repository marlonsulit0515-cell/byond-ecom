(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const subtotalAmount = window.checkoutData.subtotal;
        const calculateShippingUrl = window.checkoutData.calculateShippingUrl;
        const csrfToken = window.checkoutData.csrfToken;

        const sameAsBillingCheckbox = document.getElementById('same_as_billing');
        const shippingAddressGroup = document.getElementById('shipping_address_group');
        const shippingAddressInput = document.getElementById('shipping_address');
        const form = document.getElementById('checkout-form');
        const submitButton = document.getElementById('submit-btn');
        const paymentOptions = document.querySelectorAll('.payment-option');

        const provinceSelect = document.getElementById('province');
        const deliveryOption = document.getElementById('delivery_option');
        const shippingFeeDisplay = document.getElementById('shipping-fee-display');
        const totalAmountDisplay = document.getElementById('total-amount');
        
        let currentShippingFee = 0;
        let shippingCalculationTimeout = null;

        function updateShippingFee() {
            const province = provinceSelect.value.trim();
            const delivery = deliveryOption.value;
            const provinceInfo = document.getElementById('province-shipping-info');

            if (shippingCalculationTimeout) clearTimeout(shippingCalculationTimeout);

            if (!delivery) {
                shippingFeeDisplay.textContent = 'Select delivery method';
                updateTotal(0);
                if (provinceInfo) provinceInfo.textContent = 'Select a province to see shipping rates';
                submitButton.disabled = true;
                return;
            }

            if (delivery === 'pickup') {
                shippingFeeDisplay.textContent = 'FREE';
                currentShippingFee = 0;
                updateTotal(0);
                if (provinceInfo) provinceInfo.textContent = 'Store pickup selected - no shipping fee';
                submitButton.disabled = false;
                return;
            }

            if (!province) {
                shippingFeeDisplay.textContent = 'Select province';
                updateTotal(0);
                if (provinceInfo) provinceInfo.textContent = 'Select a province to see shipping rates';
                submitButton.disabled = true;
                return;
            }

            shippingFeeDisplay.innerHTML = '<span class="text-blue-600">Calculating...</span>';
            if (provinceInfo) provinceInfo.textContent = 'Calculating shipping fee...';
            submitButton.disabled = true;

            shippingCalculationTimeout = setTimeout(function() {
                fetch(calculateShippingUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ province, delivery_option: delivery })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentShippingFee = parseFloat(data.shipping_fee) || 0;
                        shippingFeeDisplay.textContent = '₱' + data.shipping_fee_formatted;
                        updateTotal(currentShippingFee);
                        if (provinceInfo) provinceInfo.textContent = data.breakdown || `Shipping fee: ₱${data.shipping_fee_formatted}`;
                        submitButton.disabled = false;
                    } else {
                        shippingFeeDisplay.innerHTML = '<span class="text-red-600">Error</span>';
                        if (provinceInfo) provinceInfo.textContent = data.message || 'Error calculating shipping fee';
                        submitButton.disabled = true;
                    }
                })
                .catch(() => {
                    shippingFeeDisplay.innerHTML = '<span class="text-red-600">Error - please refresh</span>';
                    if (provinceInfo) provinceInfo.textContent = 'Error calculating shipping fee.';
                    submitButton.disabled = true;
                });
            }, 300);
        }

        function updateTotal(shippingFee) {
            const total = subtotalAmount + shippingFee;
            totalAmountDisplay.textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
        
        // Listen for changes
        if (provinceSelect) {
            provinceSelect.addEventListener('change', updateShippingFee);
        }
        if (deliveryOption) {
            deliveryOption.addEventListener('change', updateShippingFee);
        }

        // Payment option selection
        paymentOptions.forEach(function(option) {
            const radio = option.querySelector('input[type="radio"]');
            
            option.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });

            radio.addEventListener('change', function() {
                paymentOptions.forEach(function(opt) {
                    opt.classList.remove('border-blue-600', 'bg-blue-50');
                    opt.classList.add('border-gray-300', 'bg-gray-100');
                });
                if (this.checked) {
                    option.classList.remove('border-gray-300', 'bg-gray-100');
                    option.classList.add('border-blue-600', 'bg-blue-50');
                }
            });
        });

        // Form submission with validation and loading state
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                // Validate payment method is selected
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                if (!paymentMethod) {
                    e.preventDefault();
                    alert('Please select a payment method');
                    return false;
                }

                // Validate delivery option and province for shipping
                const delivery = deliveryOption.value;
                const province = provinceSelect.value;
                
                if (!delivery) {
                    e.preventDefault();
                    alert('Please select a delivery option');
                    deliveryOption.focus();
                    return false;
                }
                
                if (delivery === 'ship' && !province) {
                    e.preventDefault();
                    alert('Please select a province for shipping');
                    provinceSelect.focus();
                    return false;
                }

                // Validate phone number format
                const phoneInput = document.getElementById('phone');
                const phoneRegex = /^\+63\s\d{3}\s\d{3}\s\d{4}$/;
                if (phoneInput && !phoneRegex.test(phoneInput.value)) {
                    e.preventDefault();
                    alert('Please enter a valid Philippine phone number (+63 XXX XXX XXXX)');
                    phoneInput.focus();
                    return false;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Processing...';
                
                // Re-enable after 15 seconds as fallback
                setTimeout(function() {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Complete Purchase';
                }, 15000);
            });
        }

        // Phone number formatting (Philippine format) - Matches controller validation
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Handle Philippine phone format
                if (value.length > 0 && !value.startsWith('63')) {
                    if (value.startsWith('0')) {
                        value = '63' + value.substring(1);
                    } else if (value.startsWith('9')) {
                        value = '63' + value;
                    }
                }
                
                // Limit to 12 digits (63 + 10 digits)
                if (value.length > 12) {
                    value = value.substring(0, 12);
                }
                
                // Format: +63 XXX XXX XXXX
                let formatted = '';
                if (value.length >= 2) {
                    formatted = '+' + value.substring(0, 2);
                    if (value.length > 2) {
                        formatted += ' ' + value.substring(2, 5);
                    }
                    if (value.length > 5) {
                        formatted += ' ' + value.substring(5, 8);
                    }
                    if (value.length > 8) {
                        formatted += ' ' + value.substring(8, 12);
                    }
                } else if (value.length > 0) {
                    formatted = '+' + value;
                }
                
                e.target.value = formatted;
            });

            // Validate on blur
            phoneInput.addEventListener('blur', function() {
                const phoneRegex = /^\+63\s\d{3}\s\d{3}\s\d{4}$/;
                if (this.value && !phoneRegex.test(this.value)) {
                    this.setCustomValidity('Please enter a valid Philippine phone number (+63 XXX XXX XXXX)');
                    this.reportValidity();
                } else {
                    this.setCustomValidity('');
                }
            });

            // Clear custom validity on input
            phoneInput.addEventListener('input', function() {
                this.setCustomValidity('');
            });
        }

        // Initialize shipping calculation if province and delivery are pre-selected (from old() input)
        if (provinceSelect && deliveryOption) {
            if (provinceSelect.value && deliveryOption.value) {
                updateShippingFee();
            }
        }

        // Prevent double form submission
        let formSubmitted = false;
        if (form) {
            form.addEventListener('submit', function(e) {
                if (formSubmitted) {
                    e.preventDefault();
                    return false;
                }
                formSubmitted = true;
            });
        }
    });
})();