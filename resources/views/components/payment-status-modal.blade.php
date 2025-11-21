<!-- Payment Status Modal -->
<div id="paymentStatusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Order Placed Successfully!</h2>
                    <p class="text-sm text-slate-600">Thank you for your purchase</p>
                </div>
            </div>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 space-y-6">
            <p class="text-slate-600">
                Your order has been received and is being processed. You will receive a confirmation email shortly.
            </p>

            <!-- Order Number Highlight -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Order Number</p>
                        <p class="text-xl font-bold text-slate-900" id="modal-order-number">-</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-600 mb-1">Order Date</p>
                        <p class="font-semibold text-slate-900" id="modal-order-date">-</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Shipping Information
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Name:</span>
                        <span class="font-medium text-slate-900" id="modal-full-name">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Phone:</span>
                        <span class="font-medium text-slate-900" id="modal-phone">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Province:</span>
                        <span class="font-medium text-slate-900" id="modal-province">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">City:</span>
                        <span class="font-medium text-slate-900" id="modal-city">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Barangay:</span>
                        <span class="font-medium text-slate-900" id="modal-barangay">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Postal Code:</span>
                        <span class="font-medium text-slate-900" id="modal-postal">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Street Address:</span>
                        <span class="font-medium text-slate-900" id="modal-address">-</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Order Summary
                    </h3>
                </div>
                <div class="p-4">
                    <div id="modal-order-items" class="space-y-3 mb-4">
                        <!-- Items will be dynamically inserted here -->
                    </div>
                    <div class="border-t border-gray-200 pt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Subtotal:</span>
                            <span class="font-medium" id="modal-subtotal">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Shipping Fee:</span>
                            <span class="font-medium" id="modal-shipping">-</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-slate-900 pt-2 border-t border-gray-200">
                            <span>Total:</span>
                            <span id="modal-total">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Payment Information
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Payment Method:</span>
                        <span class="font-medium text-slate-900" id="modal-payment-method">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Payment Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" id="modal-payment-status">
                            Paid
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex gap-3">
            <a href="{{ route('user.orders') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-md transition text-center">
                View My Orders
            </a>
            <a href="{{ route('shop-page') }}" class="flex-1 bg-white hover:bg-gray-50 text-slate-900 font-medium px-6 py-3 rounded-md border border-gray-300 transition text-center">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
function openPaymentModal(orderData) {
    // Populate modal with order data
    document.getElementById('modal-order-number').textContent = orderData.order_number || '-';
    document.getElementById('modal-order-date').textContent = orderData.created_at || '-';
    document.getElementById('modal-full-name').textContent = orderData.full_name || '-';
    document.getElementById('modal-phone').textContent = orderData.phone || '-';
    document.getElementById('modal-province').textContent = orderData.province || '-';
    document.getElementById('modal-city').textContent = orderData.city || '-';
    document.getElementById('modal-barangay').textContent = orderData.barangay || '-';
    document.getElementById('modal-postal').textContent = orderData.postal_code || '-';
    document.getElementById('modal-address').textContent = orderData.billing_address || '-';
    
    // Populate order items
    const itemsContainer = document.getElementById('modal-order-items');
    itemsContainer.innerHTML = '';
    
    if (orderData.items && orderData.items.length > 0) {
        orderData.items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex justify-between items-start text-sm';
            itemDiv.innerHTML = `
                <div class="flex-1">
                    <p class="font-medium text-slate-900">${item.product_name}</p>
                    <p class="text-slate-500 text-xs">Size: ${item.size} • Qty: ${item.quantity}</p>
                </div>
                <span class="font-medium text-slate-900">₱${parseFloat(item.total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            `;
            itemsContainer.appendChild(itemDiv);
        });
    }
    
    // Calculate subtotal
    const subtotal = orderData.total - (orderData.shipping_fee || 0);
    document.getElementById('modal-subtotal').textContent = '₱' + parseFloat(subtotal).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('modal-shipping').textContent = '₱' + parseFloat(orderData.shipping_fee || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('modal-total').textContent = '₱' + parseFloat(orderData.total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Payment info
    document.getElementById('modal-payment-method').textContent = orderData.payment_method ? orderData.payment_method.toUpperCase() : '-';
    
    // Show modal
    const modal = document.getElementById('paymentStatusModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closePaymentModal() {
    const modal = document.getElementById('paymentStatusModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('paymentStatusModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});
</script>