<!-- Order Details Modal Component -->
<div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-4 sm:px-6 py-4" style="background-color: #762c21;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl font-bold text-white truncate">Order Details</h1>
                        <p class="text-xs sm:text-sm text-white text-opacity-90" id="modal-order-subtitle">Order information</p>
                    </div>
                </div>
                <button onclick="closeOrderDetailsModal()" class="absolute top-4 right-4 sm:relative sm:top-0 sm:right-0 text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Status Badge in Header -->
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs text-white text-opacity-80">Current Status:</span>
                <span id="header-order-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    -
                </span>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6" style="background-color: #f4eedf;">
            <!-- Order Number, Date, and Tracking -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-1">Order Number</p>
                        <p class="text-base sm:text-lg font-bold text-slate-900 break-all" id="details-order-number">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-1">Tracking Number</p>
                        <p class="text-base sm:text-lg font-bold text-slate-900 break-all" id="details-tracking-number">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-1">Order Date</p>
                        <p class="text-sm sm:text-base font-semibold text-slate-900" id="details-order-date">-</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-1">Status Last Updated</p>
                        <p class="text-sm sm:text-base font-semibold text-slate-900" id="details-last-updated">-</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                <div class="bg-gray-50 px-3 sm:px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Shipping Information
                    </h3>
                </div>
                <div class="p-3 sm:p-4">
                    <div class="grid grid-cols-1 gap-2 sm:gap-3 text-xs sm:text-sm">
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Name:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-full-name">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Phone:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-phone">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Province:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-province">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">City:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-city">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Barangay:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-barangay">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Postal Code:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-postal">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Street Address:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-address">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-32 flex-shrink-0 font-medium sm:font-normal">Delivery Method:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-delivery">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                <div class="bg-gray-50 px-3 sm:px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Order Summary
                    </h3>
                </div>
                <div class="p-3 sm:p-4">
                    <div id="details-order-items" class="space-y-3 mb-4">
                        <!-- Items will be dynamically inserted here -->
                    </div>
                    <div class="border-t border-gray-200 pt-3 space-y-2 text-xs sm:text-sm">
                        <div class="flex">
                            <span class="text-slate-600 flex-1">Subtotal:</span>
                            <span class="font-medium text-slate-900 text-right" id="details-subtotal">-</span>
                        </div>
                        <div class="flex">
                            <span class="text-slate-600 flex-1">Shipping Fee:</span>
                            <span class="font-medium text-slate-900 text-right" id="details-shipping">-</span>
                        </div>
                        <div class="flex text-base sm:text-lg font-bold text-slate-900 pt-2 border-t border-gray-200">
                            <span class="flex-1">Total:</span>
                            <span class="text-right" id="details-total">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                <div class="bg-gray-50 px-3 sm:px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Payment Information
                    </h3>
                </div>
                <div class="p-3 sm:p-4">
                    <div class="grid grid-cols-1 gap-2 sm:gap-3 text-xs sm:text-sm">
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-36 flex-shrink-0 font-medium sm:font-normal">Payer Name:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-payer-name">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-36 flex-shrink-0 font-medium sm:font-normal">Payment Method:</span>
                            <span class="font-medium text-slate-900 flex-1 break-words" id="details-payment-method">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row">
                            <span class="text-slate-600 sm:w-36 flex-shrink-0 font-medium sm:font-normal">Transaction ID:</span>
                            <span class="font-medium text-slate-900 flex-1 text-xs break-all" id="details-transaction-id">-</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <span class="text-slate-600 sm:w-36 flex-shrink-0 font-medium sm:font-normal mb-1 sm:mb-0">Payment Status:</span>
                            <span id="details-payment-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit">
                                -
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
            <button onclick="closeOrderDetailsModal()" class="btn-secondary-color btn-md w-full sm:w-auto order-2 sm:order-1">
                Close
            </button>
            <a href="{{ route('shop-page') }}" class="btn-primary-color btn-md w-full sm:w-auto order-1 sm:order-2 text-center">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
// Function to open order details modal
function openOrderDetailsModal(orderId) {
    console.log('Opening order details for ID:', orderId);
    
    // Fetch order details via AJAX
    fetch(`/user/orders/${orderId}`)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                populateOrderDetailsModal(data.order);
            } else {
                alert(data.message || 'Failed to load order details');
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            alert('Failed to load order details. Please check console for details.\nError: ' + error.message);
        });
}

// Function to populate modal with order data
function populateOrderDetailsModal(order) {
    // Basic info
    document.getElementById('details-order-number').textContent = order.order_number || '-';
    document.getElementById('details-tracking-number').textContent = order.tracking_number || 'Not yet assigned';
    document.getElementById('details-order-date').textContent = order.created_at || '-';
    
    // Use last_status_update or updated_at for the status update time
    const statusUpdateTime = order.last_status_update || order.updated_at || '-';
    document.getElementById('details-last-updated').textContent = statusUpdateTime;
    
    // Status badge (both in header and previous location)
    const statusText = order.status ? order.status.charAt(0).toUpperCase() + order.status.slice(1).replace(/_/g, ' ') : '-';
    const statusClass = getStatusClasses(order.status);
    
    // Update header status badge
    const headerStatusBadge = document.getElementById('header-order-status');
    headerStatusBadge.textContent = statusText;
    headerStatusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + statusClass;
    
    // Shipping info
    document.getElementById('details-full-name').textContent = order.full_name || '-';
    document.getElementById('details-phone').textContent = order.phone || '-';
    document.getElementById('details-province').textContent = order.province || '-';
    document.getElementById('details-city').textContent = order.city || '-';
    document.getElementById('details-barangay').textContent = order.barangay || '-';
    document.getElementById('details-postal').textContent = order.postal_code || '-';
    document.getElementById('details-address').textContent = order.billing_address || '-';
    document.getElementById('details-delivery').textContent = order.delivery_option === 'pickup' ? 'Store Pickup' : 'Ship to Address';
    
    // Order items
    const itemsContainer = document.getElementById('details-order-items');
    itemsContainer.innerHTML = '';
    
    if (order.items && order.items.length > 0) {
        order.items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex flex-col sm:flex-row sm:justify-between sm:items-start py-2 border-b border-gray-200 last:border-0 gap-2';
            itemDiv.innerHTML = `
                <div class="flex-1">
                    <p class="font-semibold text-slate-900 text-sm sm:text-base">${item.product_name}</p>
                    <p class="text-xs sm:text-sm text-slate-600">Size: ${item.size} • Qty: ${item.quantity}</p>
                    <p class="text-xs sm:text-sm text-slate-500">₱${parseFloat(item.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} each</p>
                </div>
                <span class="font-semibold text-slate-900 text-sm sm:text-base sm:text-right">₱${parseFloat(item.total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            `;
            itemsContainer.appendChild(itemDiv);
        });
    }
    
    // Totals
    const subtotal = order.total - (order.shipping_fee || 0);
    document.getElementById('details-subtotal').textContent = '₱' + parseFloat(subtotal).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('details-shipping').textContent = '₱' + parseFloat(order.shipping_fee || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('details-total').textContent = '₱' + parseFloat(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Payment info
    document.getElementById('details-payer-name').textContent = order.full_name || '-';
    document.getElementById('details-payment-method').textContent = order.payment_method ? order.payment_method.toUpperCase() : '-';
    document.getElementById('details-transaction-id').textContent = order.transaction_id || 'N/A';
    
    const paymentStatusBadge = document.getElementById('details-payment-status');
    paymentStatusBadge.textContent = order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : '-';
    paymentStatusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    
    if (order.payment_status === 'paid') {
        paymentStatusBadge.classList.add('bg-green-100', 'text-green-800');
    } else if (order.payment_status === 'pending') {
        paymentStatusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
    } else {
        paymentStatusBadge.classList.add('bg-red-100', 'text-red-800');
    }
    
    // Show modal
    const modal = document.getElementById('orderDetailsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

// Helper function to get status badge classes
function getStatusClasses(status) {
    if (status === 'completed') {
        return 'bg-green-100 text-green-800';
    } else if (status === 'processing') {
        return 'bg-blue-100 text-blue-800';
    } else if (status === 'shipped') {
        return 'bg-purple-100 text-purple-800';
    } else if (status === 'pending') {
        return 'bg-yellow-100 text-yellow-800';
    } else if (status === 'cancellation_requested') {
        return 'bg-orange-100 text-orange-800';
    } else if (status === 'cancelled') {
        return 'bg-red-100 text-red-800';
    } else {
        return 'bg-gray-100 text-gray-800';
    }
}

// Function to close modal
function closeOrderDetailsModal() {
    const modal = document.getElementById('orderDetailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('orderDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderDetailsModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOrderDetailsModal();
    }
});
</script>