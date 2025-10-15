document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay');
    const closeSidebar = document.getElementById('closeSidebar');
    const sortSelect = document.getElementById('sortSelect');
    const sortForm = document.getElementById('sortForm');

    // Sidebar toggle
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

    filterToggle.addEventListener('click', openSidebar);
    closeSidebar.addEventListener('click', closeSidebarFunc);
    filterOverlay.addEventListener('click', closeSidebarFunc);

    // ESC key to close sidebar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebarFunc();
    });

    // Sort dropdown handler
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortForm.submit();
        });
    }

    // Price input validation (0–10000 only)
    document.querySelectorAll('input[type=number][name^=price]').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value !== "") {
                if (this.value < 0) this.value = 0;
                if (this.value > 10000) this.value = 10000;
            }
        });
    });
});