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
        if (e.key === 'Escape' && filterSidebar.classList.contains('active')) {
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

    // Lazy loading for product images (if not using native loading="lazy")
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