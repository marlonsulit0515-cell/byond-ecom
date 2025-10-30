document.addEventListener('DOMContentLoaded', function() {

    function filterByStatus(status) {
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) statusFilter.value = status;

        const searchInput = document.querySelector('input[name="search"]');
        const currentSearch = searchInput ? searchInput.value : "";

        // Get current sort value to preserve it
        const sortSelect = document.querySelector('select[name="sort"]');
        const currentSort = sortSelect ? sortSelect.value : "";

        const url = new URL(window.location.href);
        url.searchParams.set('page', '1'); // reset pagination

        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }

        if (currentSearch) {
            url.searchParams.set('search', currentSearch);
        }

        // Preserve current sort when filtering by status
        if (currentSort) {
            url.searchParams.set('sort', currentSort);
        }

        window.location.href = url.toString();
    }
    window.filterByStatus = filterByStatus;

    /**
     * ---------------------------
     * SORTING FUNCTIONALITY
     * ---------------------------
     */
    function handleSortChange() {
        const sortSelect = document.querySelector('select[name="sort"]');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const form = document.getElementById('filtersForm');
                if (form) {
                    // The form will automatically include all current filters
                    form.submit();
                }
            });
        }
    }

    // Initialize sorting
    handleSortChange();

    // Hover effects for stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('stat-card--active')) {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            }
        });
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('stat-card--active')) {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            }
        });
    });


    /**
     * ---------------------------
     * CLOSE DROPDOWN ON OUTSIDE CLICK
     * ---------------------------
     */
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});