document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay');
    const closeSidebar = document.getElementById('closeSidebar');
    const sortSelect = document.getElementById('sortSelect');
    const sortForm = document.getElementById('sortForm');

    // Calculate and set header height for sidebar positioning
    function setHeaderHeight() {
        // Try to find common header/navbar elements
        const header = document.querySelector('header') || 
                      document.querySelector('nav') || 
                      document.querySelector('.navbar') ||
                      document.querySelector('[role="banner"]');
        
        if (header) {
            const headerHeight = header.offsetHeight;
            document.documentElement.style.setProperty('--header-height', `${headerHeight}px`);
        } else {
            // Default fallback if no header found
            document.documentElement.style.setProperty('--header-height', '0px');
        }
    }

    // Set header height on load and resize
    setHeaderHeight();
    window.addEventListener('resize', setHeaderHeight);

    // Sidebar toggle functions
    function openSidebar() {
        filterSidebar.classList.remove('-translate-x-full');
        filterOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebarFunc() {
        filterSidebar.classList.add('-translate-x-full');
        filterOverlay.classList.add('hidden');
        document.body.style.overflow = '';
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
        if (e.key === 'Escape') {
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
    document.querySelectorAll('input[type=number][name^=price]').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value !== "") {
                if (this.value < 0) {
                    this.value = 0;
                }
                if (this.value > 10000) {
                    this.value = 10000;
                }
            }
        });
    });
});