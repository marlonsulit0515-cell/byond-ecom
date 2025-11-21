(function() {
    'use strict';

    let currentOrderId = null;
    let currentOrderNumber = null;
    let savedFormData = null;
    let isLoadingOrders = false;

    const cancellationModal = document.getElementById('cancellationModal');
    const confirmationModal = document.getElementById('confirmationModal');
    const cancellationForm = document.getElementById('cancellationForm');
    const ordersContainer = document.getElementById('ordersContainer');
    const loadingState = document.getElementById('loadingState');

    document.addEventListener('DOMContentLoaded', function() {
        setupCancellationListeners();
        setupOrderButtons();
        setupStatusNavigation();
    });

    // ============ STATUS NAVIGATION ============
    function setupStatusNavigation() {
        // Desktop tabs
        document.querySelectorAll('.status-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const status = this.getAttribute('data-status');
                
                // Update active tab styling
                document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('is-active'));
                this.classList.add('is-active');
                
                // Update mobile dropdown to match
                const dropdown = document.getElementById('statusFilter');
                if (dropdown) dropdown.value = status;
                
                // Fetch orders
                fetchOrders(status);
            });
        });

        // Mobile dropdown
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                const status = this.value;
                
                // Update desktop tabs to match
                document.querySelectorAll('.status-tab').forEach(tab => {
                    tab.classList.remove('is-active');
                    if (tab.getAttribute('data-status') === status) {
                        tab.classList.add('is-active');
                    }
                });
                
                // Fetch orders
                fetchOrders(status);
            });
        }
    }

    async function fetchOrders(status) {
        if (isLoadingOrders) return;
        
        isLoadingOrders = true;
        const url = new URL(window.location.origin + '/user/orders');
        if (status && status !== 'all') url.searchParams.set('status', status);

        try {
            loadingState.style.display = 'flex';
            ordersContainer.style.opacity = '0.4';

            const response = await fetch(url, {
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();

            if (data.success && data.html) {
                ordersContainer.innerHTML = data.html;
                
                // Re-apply event listeners after DOM update
                setupOrderButtons();
                
                // Update URL without page reload
                const newUrl = status === 'all' ? '/user/orders' : `/user/orders?status=${status}`;
                history.pushState({ status }, '', newUrl);
            }

        } catch (err) {
            console.error('Failed to load orders:', err);
            showToast('Failed to load orders. Please try again.', 'error');
        } finally {
            loadingState.style.display = 'none';
            ordersContainer.style.opacity = '1';
            isLoadingOrders = false;
        }
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.status) {
            const status = e.state.status;
            
            // Update UI to match URL
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.classList.remove('is-active');
                if (tab.getAttribute('data-status') === status) {
                    tab.classList.add('is-active');
                }
            });
            
            const dropdown = document.getElementById('statusFilter');
            if (dropdown) dropdown.value = status;
            
            fetchOrders(status);
        }
    });

    // ============ ORDER BUTTONS ============
    function setupOrderButtons() {
        // Copy tracking number
        window.copyTrackingNumber = async function(trackingNumber) {
            try {
                await navigator.clipboard.writeText(trackingNumber);
                showToast('Tracking number copied!', 'success');
            } catch (e) {
                showToast('Failed to copy', 'error');
            }
        };

        // Request cancellation buttons
        document.querySelectorAll('[data-action="request-cancel"]').forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });
        
        document.querySelectorAll('[data-action="request-cancel"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const orderId = this.getAttribute('data-id');
                const orderCard = this.closest('.order-card');
                const orderNumberEl = orderCard?.querySelector('.order-number');
                const orderNumber = orderNumberEl?.textContent.replace('Order #', '').trim() || '';
                
                openCancellationModal(orderId, orderNumber);
            });
        });

        // Confirm delivery buttons
        document.querySelectorAll('[data-action="confirm-delivery"]').forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });
        
        document.querySelectorAll('[data-action="confirm-delivery"]').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const orderId = this.getAttribute('data-id');
                const originalText = this.textContent;
                
                this.disabled = true;
                this.textContent = 'Processing...';
                
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]');
                    const response = await fetch(`/user/orders/${orderId}/confirm-delivery`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf.getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast(data.message || 'Order confirmed!', 'success');
                        setTimeout(() => {
                            const currentTab = document.querySelector('.status-tab.is-active');
                            const currentStatus = currentTab ? currentTab.getAttribute('data-status') : 'all';
                            fetchOrders(currentStatus);
                        }, 1500);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    showToast(error.message, 'error');
                    this.disabled = false;
                    this.textContent = originalText;
                }
            });
        });
    }

    // ============ CANCELLATION MODAL ============
    function setupCancellationListeners() {
        document.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => closeCancellationModal());
        });

        document.querySelectorAll('[data-dismiss-confirm="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                closeConfirmationModal();
                openCancellationModal(currentOrderId, currentOrderNumber);
            });
        });

        document.querySelectorAll('input[name="reason"]').forEach(radio => {
            radio.addEventListener('change', handleReasonChange);
        });

        const submitBtn = document.getElementById('submitCancellation');
        if (submitBtn) {
            submitBtn.addEventListener('click', handleSubmit);
        }

        const confirmBtn = document.getElementById('confirmCancellation');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', handleConfirm);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (confirmationModal?.style.display === 'block') {
                    closeConfirmationModal();
                    openCancellationModal(currentOrderId, currentOrderNumber);
                } else if (cancellationModal?.style.display === 'block') {
                    closeCancellationModal();
                }
            }
        });
    }

    function openCancellationModal(orderId, orderNumber) {
        currentOrderId = orderId;
        currentOrderNumber = orderNumber;

        const orderIdInput = document.getElementById('cancelOrderId');
        const orderNumberSpan = document.getElementById('modalOrderNumber');
        
        if (orderIdInput) orderIdInput.value = orderId;
        if (orderNumberSpan) orderNumberSpan.textContent = orderNumber;

        if (!savedFormData) {
            if (cancellationForm) cancellationForm.reset();
            document.getElementById('otherReasonGroup').style.display = 'none';
        } else {
            restoreFormData();
        }
        
        hideErrors();

        if (cancellationModal) {
            cancellationModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCancellationModal() {
        if (cancellationModal) {
            cancellationModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openConfirmationModal() {
        if (confirmationModal) {
            confirmationModal.style.display = 'block';
        }
    }

    function closeConfirmationModal() {
        if (confirmationModal) {
            confirmationModal.style.display = 'none';
        }
    }

    function handleReasonChange(e) {
        const otherGroup = document.getElementById('otherReasonGroup');
        const otherInput = document.getElementById('otherReason');

        if (e.target.value === 'other') {
            otherGroup.style.display = 'block';
            otherInput.required = true;
            setTimeout(() => otherInput.focus(), 100);
        } else {
            otherGroup.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
        hideErrors();
    }

    function validateForm() {
        const reasonRadio = document.querySelector('input[name="reason"]:checked');
        const otherInput = document.getElementById('otherReason');
        const reasonError = document.getElementById('reasonError');
        const otherError = document.getElementById('otherReasonError');
        
        let isValid = true;

        if (!reasonRadio) {
            if (reasonError) {
                reasonError.textContent = 'Please select a reason';
                reasonError.style.display = 'block';
            }
            isValid = false;
        }

        if (reasonRadio?.value === 'other') {
            const otherValue = otherInput.value.trim();
            
            if (!otherValue) {
                if (otherError) {
                    otherError.textContent = 'Please specify your reason';
                    otherError.style.display = 'block';
                }
                isValid = false;
            } else if (otherValue.length < 10) {
                if (otherError) {
                    otherError.textContent = 'Please provide at least 10 characters';
                    otherError.style.display = 'block';
                }
                isValid = false;
            }
        }

        return isValid;
    }

    function hideErrors() {
        const reasonError = document.getElementById('reasonError');
        const otherError = document.getElementById('otherReasonError');
        
        if (reasonError) reasonError.style.display = 'none';
        if (otherError) otherError.style.display = 'none';
    }

    function handleSubmit(e) {
        e.preventDefault();
        
        if (validateForm()) {
            savedFormData = getFormData();
            closeCancellationModal();
            openConfirmationModal();
        } else {
            showToast('Please fill in all required fields', 'error');
        }
    }

    async function handleConfirm(e) {
        e.preventDefault();
        
        const confirmBtn = document.getElementById('confirmCancellation');
        const originalText = confirmBtn.textContent;
        
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Submitting...';

        try {
            if (!savedFormData || !savedFormData.reason) {
                throw new Error('Form data was lost. Please try again.');
            }

            const result = await sendCancellationRequest(currentOrderId, savedFormData);

            if (result.success) {
                closeConfirmationModal();
                closeCancellationModal();
                showToast(result.message || 'Cancellation request submitted!', 'success');
                
                savedFormData = null;
                
                setTimeout(() => {
                    const currentTab = document.querySelector('.status-tab.is-active');
                    const currentStatus = currentTab ? currentTab.getAttribute('data-status') : 'all';
                    fetchOrders(currentStatus);
                }, 1500);
            } else {
                throw new Error(result.message || 'Request failed');
            }

        } catch (error) {
            closeConfirmationModal();
            showToast(error.message, 'error');
            
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    }

    function getFormData() {
        const reasonRadio = document.querySelector('input[name="reason"]:checked');
        const otherInput = document.getElementById('otherReason');
        const commentsInput = document.getElementById('additionalComments');

        const data = {};

        if (reasonRadio) {
            data.reason = reasonRadio.value;
        }

        if (reasonRadio?.value === 'other' && otherInput) {
            const otherValue = otherInput.value.trim();
            if (otherValue) {
                data.other_reason = otherValue;
            }
        }

        if (commentsInput) {
            const commentsValue = commentsInput.value.trim();
            if (commentsValue) {
                data.comments = commentsValue;
            }
        }

        return data;
    }

    function restoreFormData() {
        if (!savedFormData) return;

        if (savedFormData.reason) {
            const radio = document.querySelector(`input[name="reason"][value="${savedFormData.reason}"]`);
            if (radio) {
                radio.checked = true;
                
                if (savedFormData.reason === 'other') {
                    const otherGroup = document.getElementById('otherReasonGroup');
                    const otherInput = document.getElementById('otherReason');
                    if (otherGroup) otherGroup.style.display = 'block';
                    if (otherInput && savedFormData.other_reason) {
                        otherInput.value = savedFormData.other_reason;
                    }
                }
            }
        }

        if (savedFormData.comments) {
            const commentsInput = document.getElementById('additionalComments');
            if (commentsInput) {
                commentsInput.value = savedFormData.comments;
            }
        }
    }

    async function sendCancellationRequest(orderId, formData) {
        const url = `/user/orders/${orderId}/request-cancellation`;
        const csrf = document.querySelector('meta[name="csrf-token"]');

        if (!csrf) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                const firstError = Object.values(data.errors)[0][0];
                throw new Error(firstError);
            }
            throw new Error(data.message || 'Request failed');
        }

        return data;
    }

    function showToast(message, type = 'success') {
        let container = document.getElementById('toastContainer');
        
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = type === 'success' 
            ? '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            : '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                <p class="toast-message">${message}</p>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

})();