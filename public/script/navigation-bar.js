// Desktop dropdown toggle
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = event.target.closest('.dropdown');
    dropdown.classList.toggle('active');
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
    }
});

// Mobile menu variables
const toggle = document.querySelector('.menu-toggle');
const overlay = document.querySelector('.sidebar-overlay');
const mobileParent = document.querySelector('.mobile-menu-parent');

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
    
    // Toggle body scroll based on menu state
    toggleBodyScroll(isOpening);

    // Close any open dropdowns when closing menu
    if (!isOpening) {
        document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
            d.classList.remove('active');
        });
    }
});

// Mobile dropdown toggle (accordion style)
function openMobileSubmenu(event) {
    event.preventDefault();
    const dropdown = event.target.closest('.dropdown');
    
    // Close other dropdowns
    document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
        if (d !== dropdown) {
            d.classList.remove('active');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('active');
}

// Overlay click closes mobile menu
overlay.addEventListener('click', () => {
    toggle.classList.remove('active');
    mobileParent.classList.remove('active');
    overlay.classList.remove('active');
    toggleBodyScroll(false);
    
    // Close all dropdowns
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
        
        // Close all dropdowns
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
        
        // Close all dropdowns
        document.querySelectorAll('.mobile-menu-parent .dropdown').forEach(d => {
            d.classList.remove('active');
        });
    }
});