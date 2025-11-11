document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    const imageLabel = document.getElementById("imageLabel");
    const closeBtn = document.getElementById("closeBtn");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");

    let currentImages = [];
    let currentIndex = 0;
    let productName = "";
    let isModalOpen = false;

    // Improved modal content update with error handling
    function updateModalContent() {
        if (!currentImages.length || !currentImages[currentIndex]) {
            console.error('No valid image data available');
            return;
        }

        const currentImage = currentImages[currentIndex];
        
        // Pre-load image to handle errors
        const img = new Image();
        img.onload = function() {
            modalImg.src = currentImage.src;
            modalImg.alt = `${productName} - ${currentImage.label}`;
        };
        img.onerror = function() {
            console.error('Failed to load image:', currentImage.src);
            modalImg.src = '/path/to/placeholder.jpg'; // Add placeholder path
            modalImg.alt = 'Image not available';
        };
        img.src = currentImage.src;

        // Update label with counter
        const label = currentImage.label || 'Product Image';
        imageLabel.textContent = `${productName} - ${label} (${currentIndex + 1}/${currentImages.length})`;
        
        // Show/hide navigation buttons based on image count
        const showNav = currentImages.length > 1;
        prevBtn.style.display = showNav ? 'flex' : 'none';
        nextBtn.style.display = showNav ? 'flex' : 'none';

        // Update button states for better UX
        prevBtn.disabled = false;
        nextBtn.disabled = false;
        prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    // Enhanced image click handler with validation
    const imageElements = document.querySelectorAll(".table-image");
    
    imageElements.forEach(img => {
        // Add visual feedback
        img.style.cursor = 'pointer';
        img.classList.add('hover:opacity-80', 'transition-opacity');

        img.addEventListener("click", function(e) {
            e.preventDefault();
            
            try {
                // Get product name
                productName = this.dataset.productName || 'Product';
                
                // Parse images data
                const imagesData = this.dataset.images ? JSON.parse(this.dataset.images) : [];
                
                // Filter and validate images
                currentImages = imagesData.filter(img => {
                    return img && 
                           img.src && 
                           typeof img.src === 'string' && 
                           img.src.trim().length > 0;
                });

                // Check if we have valid images
                if (currentImages.length === 0) {
                    console.warn('No valid images found for this product');
                    return;
                }

                // Reset to first image
                currentIndex = 0;
                isModalOpen = true;
                
                // Update content and show modal
                updateModalContent();
                openModal();
                
            } catch (error) {
                console.error('Error loading product images:', error);
                alert('Failed to load product images. Please try again.');
            }
        });
    });

    // Modal open/close functions with animations
    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Fade in animation
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);

        // Focus management for accessibility
        closeBtn.focus();
    }

    function closeModal() {
        isModalOpen = false;
        modal.style.opacity = '0';
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }, 300);
    }

    // Close button handler
    closeBtn.onclick = (e) => {
        e.preventDefault();
        closeModal();
    };

    // Navigation handlers with safety checks
    prevBtn.onclick = (e) => {
        e.preventDefault();
        if (currentImages.length > 1) {
            prevBtn.disabled = true;
            currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
            updateModalContent();
            
            // Re-enable after brief delay to prevent rapid clicking issues
            setTimeout(() => {
                prevBtn.disabled = false;
            }, 300);
        }
    };

    nextBtn.onclick = (e) => {
        e.preventDefault();
        if (currentImages.length > 1) {
            nextBtn.disabled = true;
            currentIndex = (currentIndex + 1) % currentImages.length;
            updateModalContent();
            
            setTimeout(() => {
                nextBtn.disabled = false;
            }, 300);
        }
    };

    // Enhanced click outside to close
    modal.addEventListener('click', function(event) {
        // Only close if clicking the backdrop, not the content
        if (event.target === modal) {
            closeModal();
        }
    });

    // Improved keyboard navigation
    let keyDebounce = false;
    
    document.addEventListener('keydown', function(e) {
        if (!isModalOpen) return;
        
        // Prevent rapid key presses
        if (keyDebounce) return;
        
        switch(e.key) {
            case 'Escape':
                e.preventDefault();
                closeModal();
                break;
                
            case 'ArrowLeft':
                if (currentImages.length > 1) {
                    e.preventDefault();
                    keyDebounce = true;
                    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                    updateModalContent();
                    setTimeout(() => { keyDebounce = false; }, 300);
                }
                break;
                
            case 'ArrowRight':
                if (currentImages.length > 1) {
                    e.preventDefault();
                    keyDebounce = true;
                    currentIndex = (currentIndex + 1) % currentImages.length;
                    updateModalContent();
                    setTimeout(() => { keyDebounce = false; }, 300);
                }
                break;
                
            case 'Home':
                if (currentImages.length > 1) {
                    e.preventDefault();
                    currentIndex = 0;
                    updateModalContent();
                }
                break;
                
            case 'End':
                if (currentImages.length > 1) {
                    e.preventDefault();
                    currentIndex = currentImages.length - 1;
                    updateModalContent();
                }
                break;
        }
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    const minSwipeDistance = 50;

    modalImg.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    modalImg.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        if (currentImages.length <= 1) return;
        
        const swipeDistance = touchEndX - touchStartX;
        
        if (Math.abs(swipeDistance) > minSwipeDistance) {
            if (swipeDistance > 0) {
                // Swipe right - go to previous
                currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
            } else {
                // Swipe left - go to next
                currentIndex = (currentIndex + 1) % currentImages.length;
            }
            updateModalContent();
        }
    }

    // Image loading state
    modalImg.addEventListener('loadstart', function() {
        this.style.opacity = '0.5';
    });

    modalImg.addEventListener('load', function() {
        this.style.opacity = '1';
    });

    modalImg.addEventListener('error', function() {
        console.error('Failed to load modal image');
        this.alt = 'Image failed to load';
        this.style.opacity = '1';
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (isModalOpen) {
            document.body.style.overflow = '';
        }
    });

    console.log('Product image gallery initialized successfully');
});
