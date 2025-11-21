// ============================================
// SLIDESHOW FUNCTIONALITY
// ============================================
let slideIndex = 0;
const slides = document.querySelectorAll('.mySlides');
const container = document.querySelector('.slideshow-container');

function showSlides() {
    if (container && slides.length > 0) {
        container.style.transform = `translateX(-${slideIndex * 100}%)`;
        slideIndex++;
        if (slideIndex >= slides.length) slideIndex = 0;
        setTimeout(showSlides, 5000);
    }
}

if (slides.length > 0) {
    showSlides();
}

// ============================================
// SCROLL ANIMATIONS (jQuery)
// ============================================
jQuery(function($) {
    function doAnimations() {
        var offset = $(window).scrollTop() + $(window).height();
        var $animatables = $('.animatable');

        if ($animatables.length == 0) {
            $(window).off('scroll', doAnimations);
        }

        $animatables.each(function() {
            var $elem = $(this);
            if (($elem.offset().top + $elem.height() - 20) < offset) {
                $elem.removeClass('animatable').addClass('animated');
            }
        });
    }

    $(window).on('scroll', doAnimations);
    $(window).trigger('scroll');
});

// ============================================
// NAVIGATION MENU (Desktop & Mobile)
// ============================================

// Desktop dropdown toggle
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = event.target.closest('.dropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', (event) => {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
    }
});

// Mobile menu functionality
(function() {
    const toggle = document.querySelector('.menu-toggle');
    const overlay = document.querySelector('.sidebar-overlay');
    const mobileParent = document.querySelector('.mobile-menu-parent');

    if (!toggle || !overlay || !mobileParent) return;

    // Helper function to toggle body scroll
    function toggleBodyScroll(shouldLock) {
        if (shouldLock) {
            document.body.classList.add('menu-open');
        } else {
            document.body.classList.remove('menu-open');
        }
    }

    // Mobile hamburger toggle
    toggle.addEventListener('click', () => {
        const isOpening = !toggle.classList.contains('active');
        
        toggle.classList.toggle('active');
        mobileParent.classList.toggle('active');
        overlay.classList.toggle('active');
        
        toggleBodyScroll(isOpening);

        // Close any open dropdowns when closing menu
        if (!isOpening) {
            document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
                d.classList.remove('active');
            });
        }
    });

    // Mobile dropdown toggle (accordion style)
    window.openMobileSubmenu = function(event) {
        event.preventDefault();
        const dropdown = event.target.closest('.dropdown');
        
        if (!dropdown) return;

        // Close other dropdowns
        document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('active');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('active');
    };

    // Overlay click closes mobile menu
    overlay.addEventListener('click', () => {
        toggle.classList.remove('active');
        mobileParent.classList.remove('active');
        overlay.classList.remove('active');
        toggleBodyScroll(false);
        
        document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
            d.classList.remove('active');
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.menuMain') && 
            !e.target.closest('.mobile-menu-parent') && 
            !e.target.closest('.sidebar-overlay')) {
            toggle.classList.remove('active');
            mobileParent.classList.remove('active');
            overlay.classList.remove('active');
            toggleBodyScroll(false);
            
            document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
                d.classList.remove('active');
            });
        }
    });

    // Reset on window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            toggle.classList.remove('active');
            mobileParent.classList.remove('active');
            overlay.classList.remove('active');
            toggleBodyScroll(false);
            
            document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
                d.classList.remove('active');
            });
        }
    });
})();

// ============================================
// FILTER SIDEBAR & PRODUCT FILTERS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
    const filterToggle = document.getElementById('filterToggle');
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay');
    const closeSidebar = document.getElementById('closeSidebar');
    const sortSelect = document.getElementById('sortSelect');
    const sortForm = document.getElementById('sortForm');
    const filterForm = document.getElementById('filterForm');

    // Calculate and set header height for sidebar positioning
    function setHeaderHeight() {
        const header = document.querySelector('header') || 
                      document.querySelector('nav') || 
                      document.querySelector('.navbar') ||
                      document.querySelector('[role="banner"]');
        
        const headerHeight = header ? header.offsetHeight : 0;
        document.documentElement.style.setProperty('--header-height', `${headerHeight}px`);
    }

    // Set header height on load and resize with debouncing
    setHeaderHeight();
    
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(setHeaderHeight, 150);
    });

    // Sidebar toggle functions
    function openSidebar() {
        if (filterSidebar && filterOverlay) {
            filterSidebar.classList.add('active');
            filterOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebarFunc() {
        if (filterSidebar && filterOverlay) {
            filterSidebar.classList.remove('active');
            filterOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Event listeners for sidebar
    if (filterToggle) {
        filterToggle.addEventListener('click', openSidebar);
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', closeSidebarFunc);
    }

    if (filterOverlay) {
        filterOverlay.addEventListener('click', closeSidebarFunc);
    }

    // ESC key to close sidebar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && filterSidebar && filterSidebar.classList.contains('active')) {
            closeSidebarFunc();
        }
    });

    // Sort dropdown handler
    if (sortSelect && sortForm) {
        sortSelect.addEventListener('change', function() {
            sortForm.submit();
        });
    }

    // Price input validation (0–10000 only)
    const priceInputs = document.querySelectorAll('input[type=number][name^=price]');
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value !== "") {
                let value = parseFloat(this.value);
                
                if (isNaN(value) || value < 0) {
                    this.value = 0;
                } else if (value > 10000) {
                    this.value = 10000;
                }
            }
        });

        // Prevent entering invalid characters
        input.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9]/.test(char)) {
                e.preventDefault();
            }
        });
    });

    // Handle size selection styling
    const sizeRadios = document.querySelectorAll('.size-radio');
    sizeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active class from all options
            document.querySelectorAll('.size-option').forEach(option => {
                option.classList.remove('active');
            });
            
            // Add active class to selected option
            if (this.checked) {
                const sizeOption = this.nextElementSibling;
                if (sizeOption && sizeOption.classList.contains('size-option')) {
                    sizeOption.classList.add('active');
                }
            }
        });
    });

    // Handle availability radio button changes
    const availabilityRadios = document.querySelectorAll('input[name="availability"]');
    availabilityRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (filterForm) {
                filterForm.submit();
            }
        });
    });

    // Smooth scroll to top when filters are applied
    if (window.location.search) {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('price_from') || urlParams.has('price_to') || 
            urlParams.has('availability') || urlParams.has('size') || 
            urlParams.has('sort')) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Lazy loading for product images
    const images = document.querySelectorAll('.product-image, .product-hover-image');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        images.forEach(img => {
            if (img.dataset.src) {
                imageObserver.observe(img);
            }
        });
    }

    // Add loading state to filter form
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = filterForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Applying...';
            }
        });
    }

    // Add loading state to sort form
    if (sortForm) {
        sortForm.addEventListener('submit', function() {
            if (sortSelect) {
                sortSelect.disabled = true;
            }
        });
    }

    // Prevent double submission
    let isSubmitting = false;
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
        });
    });
});

// ============================================
// TOAST NOTIFICATION SYSTEM
// ============================================
window.showToast = function(message, duration = 2500) {
    const toast = document.getElementById('toast');
    const msg = document.getElementById('toast-message');

    if (!toast || !msg) return;

    msg.innerText = message;
    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.display = 'none';
    }, duration);
};

// ============================================
// PRODUCT PAGE FUNCTIONALITY
// ============================================
(function() {
    'use strict';

    const CONFIG = {
        DEBOUNCE_DELAY: 300,
        REQUEST_TIMEOUT: 10000,
        STOCK_REFRESH_INTERVAL: 30000,
        MAX_RETRIES: 2
    };

    let state = {
        selectedSize: null,
        maxStock: 0,
        isProcessing: false,
        stockRefreshTimer: null,
        productId: null
    };

    const elements = {
        mainImage: document.getElementById('mainProductImage'),
        quantity: document.getElementById('quantity'),
        increaseBtn: document.getElementById('increase'),
        decreaseBtn: document.getElementById('decrease'),
        addToCartBtn: document.getElementById('add-to-cart-btn'),
        buyNowBtn: document.getElementById('buy-now-btn'),
        guestBuyBtn: document.getElementById('guest-buy-btn'),
        stockMessage: document.getElementById('stock-message'),
        cartQuantity: document.getElementById('cart-quantity'),
        buyQuantity: document.getElementById('buy-quantity'),
        selectedSizeInput: document.getElementById('selected-size'),
        buySizeInput: document.getElementById('buy-size'),
        addToCartForm: document.getElementById('add-to-cart-form')
    };

    // Utility Functions
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

    function showNotification(type, message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
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

    // Image Management
    function changeMainImage(newImageSrc, clickedThumbnail) {
        if (elements.mainImage && newImageSrc) {
            elements.mainImage.src = newImageSrc;
            announceToScreenReader('Product image changed');
        }
        
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
            thumb.setAttribute('aria-selected', 'false');
        });
        
        if (clickedThumbnail) {
            clickedThumbnail.classList.add('active');
            clickedThumbnail.setAttribute('aria-selected', 'true');
        }
    }

    // Quantity Management
    function syncQuantityInputs(value) {
        if (elements.quantity) elements.quantity.value = value;
        if (elements.cartQuantity) elements.cartQuantity.value = value;
        if (elements.buyQuantity) elements.buyQuantity.value = value;
    }

    function updateQuantityControls(stock) {
        if (!elements.quantity) return;

        const currentQty = parseInt(elements.quantity.value) || 1;
        const newQty = Math.max(1, Math.min(currentQty, stock));
        
        syncQuantityInputs(newQty);
        elements.quantity.max = stock;

        const isInStock = stock > 0;
        
        if (elements.increaseBtn) {
            elements.increaseBtn.disabled = !isInStock || newQty >= stock;
            elements.increaseBtn.setAttribute('aria-label', 
                !isInStock ? 'Out of stock' : 
                newQty >= stock ? `Maximum stock reached (${stock})` :
                `Increase quantity (current: ${newQty})`
            );
        }
        
        if (elements.decreaseBtn) {
            elements.decreaseBtn.disabled = !isInStock || newQty <= 1;
            elements.decreaseBtn.setAttribute('aria-label', 
                !isInStock ? 'Out of stock' : 
                newQty <= 1 ? 'Minimum quantity reached' :
                `Decrease quantity (current: ${newQty})`
            );
        }
    }

    const handleQuantityChange = debounce((amount) => {
        if (!elements.quantity || state.isProcessing) return;
        
        let currentQty = parseInt(elements.quantity.value) || 1;
        let newQty = currentQty + amount;
        
        newQty = Math.max(1, Math.min(newQty, state.maxStock));

        if (newQty !== currentQty) {
            syncQuantityInputs(newQty);
            updateQuantityControls(state.maxStock);
            announceToScreenReader(`Quantity changed to ${newQty}`);
        }
    }, 100);

    function updateActionButtons(stock) {
        const isOutOfStock = stock <= 0;
        const buttonText = isOutOfStock ? 'Out of Stock' : null;

        const buttons = [
            { element: elements.addToCartBtn, defaultText: 'Add to Cart' },
            { element: elements.buyNowBtn, defaultText: 'Buy it now' },
            { element: elements.guestBuyBtn, defaultText: 'Buy it now' }
        ];

        buttons.forEach(({ element, defaultText }) => {
            if (!element) return;
            
            element.disabled = isOutOfStock;
            element.setAttribute('aria-disabled', isOutOfStock ? 'true' : 'false');
            
            if (buttonText) {
                element.textContent = buttonText;
            } else if (element.textContent === 'Out of Stock') {
                element.textContent = defaultText;
            }
        });
    }

    // Size Selection
    function selectSize(size, stock) {
        if (!size || typeof stock !== 'number') {
            console.error('Invalid size or stock value');
            return;
        }

        state.selectedSize = size;
        state.maxStock = stock;
        
        document.querySelectorAll('.size-btn').forEach(btn => {
            const isSelected = btn.dataset.size === size;
            btn.classList.toggle('active', isSelected);
            btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
        
        if (elements.selectedSizeInput) elements.selectedSizeInput.value = size;
        if (elements.buySizeInput) elements.buySizeInput.value = size;
        
        updateQuantityControls(stock);
        updateActionButtons(stock);
        
        if (elements.stockMessage) {
            if (stock > 0) {
                elements.stockMessage.textContent = `${stock} unit${stock !== 1 ? 's' : ''} available for size ${size}`;
                elements.stockMessage.className = 'product-note mt-2 text-sm text-center text-green-600';
                elements.stockMessage.setAttribute('role', 'status');
                elements.stockMessage.setAttribute('aria-live', 'polite');
            } else {
                elements.stockMessage.textContent = `Size ${size} is out of stock`;
                elements.stockMessage.className = 'product-note mt-2 text-sm text-center text-red-600';
                elements.stockMessage.setAttribute('role', 'alert');
            }
        }

        announceToScreenReader(`Size ${size} selected. ${stock} units available.`);
        
        if (state.productId) {
            refreshStockForSize(size);
        }
    }

    // Stock Management
    async function refreshStockForSize(size) {
        if (!state.productId || !size) return;

        try {
            const response = await fetch(`/products/${state.productId}/stock?size=${size}`, {
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
                if (data.stock !== state.maxStock) {
                    state.maxStock = data.stock;
                    updateQuantityControls(data.stock);
                    updateActionButtons(data.stock);
                    
                    if (elements.stockMessage && state.selectedSize === size) {
                        if (data.stock > 0) {
                            elements.stockMessage.textContent = `${data.stock} unit${data.stock !== 1 ? 's' : ''} available for size ${size}`;
                            elements.stockMessage.className = 'product-note mt-2 text-sm text-center text-green-600';
                        } else {
                            elements.stockMessage.textContent = `Size ${size} is out of stock`;
                            elements.stockMessage.className = 'product-note mt-2 text-sm text-center text-red-600';
                        }
                    }
                }
            }
        } catch (error) {
            console.log('Stock refresh failed:', error.message);
        }
    }

    function startStockRefresh() {
        if (state.stockRefreshTimer) {
            clearInterval(state.stockRefreshTimer);
        }

        state.stockRefreshTimer = setInterval(() => {
            if (state.selectedSize) {
                refreshStockForSize(state.selectedSize);
            }
        }, CONFIG.STOCK_REFRESH_INTERVAL);
    }

    function stopStockRefresh() {
        if (state.stockRefreshTimer) {
            clearInterval(state.stockRefreshTimer);
            state.stockRefreshTimer = null;
        }
    }

    // Cart Management
    function validateAddToCart() {
        if (!state.selectedSize) {
            showNotification('error', 'Please select a size before adding to cart.');
            
            const sizeContainer = document.querySelector('.size-container, .size-selector');
            if (sizeContainer) {
                sizeContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                sizeContainer.classList.add('highlight-required');
                setTimeout(() => sizeContainer.classList.remove('highlight-required'), 2000);
            }
            
            return false;
        }

        if (state.maxStock <= 0) {
            showNotification('error', 'Selected size is out of stock.');
            return false;
        }

        const quantity = parseInt(elements.quantity?.value || 0);
        if (quantity <= 0 || quantity > state.maxStock) {
            showNotification('error', `Please select a quantity between 1 and ${state.maxStock}.`);
            return false;
        }

        return true;
    }

    async function handleAddToCart(e) {
        e.preventDefault();
        
        if (state.isProcessing) {
            return;
        }

        if (!validateAddToCart()) {
            return;
        }

        state.isProcessing = true;
        
        const formData = new FormData(elements.addToCartForm);
        const submitButton = elements.addToCartBtn;
        
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Adding...';
        submitButton.setAttribute('aria-busy', 'true');
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), CONFIG.REQUEST_TIMEOUT);

            const response = await fetch(elements.addToCartForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Server error.');
            }
            
            if (data.success) {
                if (typeof window.cartAPI?.updateCartCount === 'function') {
                    window.cartAPI.updateCartCount(data.cartCount);
                } else if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount(data.cartCount);
                }
                
                if (state.selectedSize) {
                    await refreshStockForSize(state.selectedSize);
                }
                
                showNotification('success', data.message || 'Added to cart successfully!');
                announceToScreenReader('Product added to cart');
                
            } else {
                throw new Error(data.message || 'Unable to add to cart');
            }

        } catch (error) {
            console.error('Add to cart error:', error);
            
            let errorMessage = 'An error occurred. Please try again.';
            
            if (error.name === 'AbortError') {
                errorMessage = 'Request timed out. Please check your connection.';
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            showNotification('error', errorMessage);

        } finally {
            state.isProcessing = false;
            submitButton.disabled = false;
            submitButton.textContent = originalText;
            submitButton.removeAttribute('aria-busy');
        }
    }

    // Event Listeners
    function initEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('#increase')) {
                e.preventDefault();
                e.stopPropagation();
                handleQuantityChange(1);
            } else if (e.target.closest('#decrease')) {
                e.preventDefault();
                e.stopPropagation();
                handleQuantityChange(-1);
            }
        });

        if (elements.quantity) {
            elements.quantity.addEventListener('input', debounce((e) => {
                let currentQty = parseInt(e.target.value) || 1;
                currentQty = Math.max(1, Math.min(currentQty, state.maxStock));
                
                syncQuantityInputs(currentQty);
                updateQuantityControls(state.maxStock);
            }, 200));

            elements.quantity.addEventListener('keydown', (e) => {
                if (e.key === '-' || e.key === 'e' || e.key === 'E') {
                    e.preventDefault();
                }
            });
        }

        if (elements.addToCartForm) {
            elements.addToCartForm.addEventListener('submit', handleAddToCart);
        }

        const authModal = document.getElementById('authModal');
        if (authModal) {
            authModal.addEventListener('click', (e) => {
                if (e.target.id === 'authModal') {
                    if (typeof window.cartAPI?.closeAuthModal === 'function') {
                        window.cartAPI.closeAuthModal();
                    } else if (typeof window.closeAuthModal === 'function') {
                        window.closeAuthModal();
                    }
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && authModal && authModal.style.display !== 'none') {
                if (typeof window.cartAPI?.closeAuthModal === 'function') {
                    window.cartAPI.closeAuthModal();
                } else if (typeof window.closeAuthModal === 'function') {
                    window.closeAuthModal();
                }
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopStockRefresh();
            } else if (state.selectedSize) {
                startStockRefresh();
                refreshStockForSize(state.selectedSize);
            }
        });

        window.addEventListener('beforeunload', () => {
            stopStockRefresh();
        });
    }

    // Initialization
    function init() {
        if (elements.addToCartForm) {
            const actionUrl = elements.addToCartForm.action;
            const matches = actionUrl.match(/\/cart\/add\/(\d+)/);
            if (matches) {
                state.productId = matches[1];
            }
        }

        if (elements.selectedSizeInput?.value) {
            state.selectedSize = elements.selectedSizeInput.value;
        }
        
        if (elements.quantity) {
            state.maxStock = parseInt(elements.quantity.max) || 0;
        }
        
        updateQuantityControls(state.maxStock);
        updateActionButtons(state.maxStock);
        initEventListeners();

        if (typeof window.cartAPI?.initializeCartCount === 'function') {
            window.cartAPI.initializeCartCount();
        } else if (typeof window.initializeCartCount === 'function') {
            window.initializeCartCount();
        }

        if (state.selectedSize && state.productId) {
            startStockRefresh();
        }
    }

    // Public API
    window.productPage = {
        changeMainImage,
        selectSize,
        refreshStock: () => {
            if (state.selectedSize) {
                refreshStockForSize(state.selectedSize);
            }
        }
    };

    // Legacy support (keep these for backward compatibility)
    window.changeMainImage = changeMainImage;
    window.selectSize = selectSize;

    // Page cache handling
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            console.log('Page loaded from cache - refreshing cart count');
            if (typeof window.cartAPI?.initializeCartCount === 'function') {
                window.cartAPI.initializeCartCount();
            } else if (typeof window.initializeCartCount === 'function') {
                window.initializeCartCount();
            }
        }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();