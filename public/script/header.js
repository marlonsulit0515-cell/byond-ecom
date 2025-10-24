    document.addEventListener('DOMContentLoaded', function() {
        const profileIcon = document.getElementById('profile-icon');
        const dropdownMenu = document.getElementById('dropdown-menu');
        
        if(profileIcon && dropdownMenu) {
            profileIcon.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
            });
            
            document.addEventListener('click', function(e) {
                if (!profileIcon.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        }
    });

    // Function to update cart badge (called from AJAX)
    function updateCartCount(count) {
        const cartBadge = document.querySelector('.cart-badge');
        
        if (count > 0) {
            if (cartBadge) {
                // Update existing badge
                cartBadge.textContent = count;
                cartBadge.classList.add('updated');
                setTimeout(() => cartBadge.classList.remove('updated'), 500);
            } else {
                // Create new badge if it doesn't exist
                const cartIcon = document.querySelector('.cart-icon');
                const newBadge = document.createElement('span');
                newBadge.className = 'cart-badge updated';
                newBadge.textContent = count;
                cartIcon.appendChild(newBadge);
            }
        } else if (cartBadge) {
            // Remove badge if count is 0
            cartBadge.remove();
        }
    }

    // Make function globally accessible
    window.updateCartCount = updateCartCount;