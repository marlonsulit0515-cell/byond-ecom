document.addEventListener('DOMContentLoaded', function() {
    /**
     * ---------------------------
     * FILTER BY STATUS (Stat Cards)
     * ---------------------------
     */
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
     * BULK SELECTION FUNCTIONS
     * ---------------------------
     */
    function updateSelection() {
        const checkboxes = document.querySelectorAll('.order-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => cb.value);

        // Reset hidden inputs
        document.querySelectorAll('input[name="order_ids[]"]').forEach(el => el.remove());

        // Append hidden inputs
        const form = document.querySelector('.bulk-actions-form');
        if (form) {
            selectedIds.forEach(id => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'order_ids[]';
                hiddenInput.value = id;
                form.appendChild(hiddenInput);
            });
        }

        // Update UI count
        const countEl = document.getElementById('selectedCount');
        if (countEl) countEl.textContent = selectedIds.length;

        // Show/hide bulk actions
        const bulkActions = document.getElementById('bulkActions');
        if (bulkActions) {
            bulkActions.style.display = selectedIds.length > 0 ? 'block' : 'none';
        }
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        if (!selectAll) return;

        document.querySelectorAll('.order-checkbox').forEach(cb => {
            cb.checked = selectAll.checked;
        });
        updateSelection();
    }

    function clearSelection() {
        document.querySelectorAll('.order-checkbox, #selectAll').forEach(cb => {
            cb.checked = false;
        });
        document.querySelectorAll('input[name="order_ids[]"]').forEach(el => el.remove());
        updateSelection();
    }

    function toggleStatusMenu(orderId) {
        const menu = document.getElementById(`statusMenu-${orderId}`);
        const isShown = menu.classList.contains('show');
        
        // Close all menus
        document.querySelectorAll('.dropdown-menu').forEach(m => {
            m.classList.remove('show');
        });
        
        // Toggle current menu
        if (!isShown) {
            menu.classList.add('show');
        }
    }

    // Expose globally
    window.updateSelection = updateSelection;
    window.toggleSelectAll = toggleSelectAll;
    window.clearSelection = clearSelection;
    window.toggleStatusMenu = toggleStatusMenu;

    /**
     * ---------------------------
     * BULK FORM VALIDATION
     * ---------------------------
     */
    const bulkForm = document.getElementById('bulkForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const selectedInputs = document.querySelectorAll('input[name="order_ids[]"]');
            const statusSelect = bulkForm.querySelector('select[name="status"]');

            if (selectedInputs.length === 0) {
                e.preventDefault();
                alert('Please select at least one order.');
                return;
            }
            if (statusSelect && !statusSelect.value) {
                e.preventDefault();
                alert('Please select a status.');
                return;
            }
            if (!confirm(`Are you sure you want to update ${selectedInputs.length} order(s)?`)) {
                e.preventDefault();
            }
        });
    }

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