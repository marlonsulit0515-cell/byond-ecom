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

        function updateModalContent() {
            if (currentImages.length > 0 && currentImages[currentIndex]) {
                modalImg.src = currentImages[currentIndex].src;
                const label = currentImages[currentIndex].label;
                imageLabel.textContent = `${productName} - ${label} (${currentIndex + 1}/${currentImages.length})`;
                
                // Show/hide navigation buttons
                prevBtn.style.display = currentImages.length > 1 ? 'flex' : 'none';
                nextBtn.style.display = currentImages.length > 1 ? 'flex' : 'none';
            }
        }

        document.querySelectorAll(".clickable-image").forEach(img => {
            img.addEventListener("click", function() {
                productName = this.dataset.productName;
                
                const imagesData = JSON.parse(this.dataset.images);
                // Filter out null values just in case a product model only has one image
                currentImages = imagesData.filter(img => img.src && img.src.length > 0); 

                if (currentImages.length > 0) {
                    currentIndex = 0;
                    updateModalContent();
                    modal.style.display = 'flex'; // Use 'flex' to center the content
                }
            });
        });

        closeBtn.onclick = () => {
            modal.style.display = 'none';
        };

        prevBtn.onclick = () => {
            if (currentImages.length > 1) {
                currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                updateModalContent();
            }
        };

        nextBtn.onclick = () => {
            if (currentImages.length > 1) {
                currentIndex = (currentIndex + 1) % currentImages.length;
                updateModalContent();
            }
        };

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (modal.style.display === 'flex') { // Check for 'flex' display
                if (e.key === 'Escape') {
                    modal.style.display = 'none';
                } else if (e.key === 'ArrowLeft' && currentImages.length > 1) {
                    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                    updateModalContent();
                } else if (e.key === 'ArrowRight' && currentImages.length > 1) {
                    currentIndex = (currentIndex + 1) % currentImages.length;
                    updateModalContent();
                }
            }
        });
    });
