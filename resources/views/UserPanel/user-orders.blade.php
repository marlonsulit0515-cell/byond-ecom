@extends('layouts.user-dash-layout')
<link href="{{ asset('css/user-order.css') }}" rel="stylesheet" />

@section('dashboard-content')
<div class="dashboard-container">
    <div class="dashboard-content">
        <h1>My Orders</h1>
            <div class="order-status-nav">
                <a href="{{ route('user.orders', ['status' => 'all']) }}" 
                class="status-link {{ request('status') == 'all' || !request('status') ? 'active' : '' }}">
                All
                </a>
                <a href="{{ route('user.orders', ['status' => 'pending']) }}" 
                class="status-link {{ request('status') == 'pending' ? 'active' : '' }}">
                To Pay
                </a>
                <a href="{{ route('user.orders', ['status' => 'processing']) }}" 
                class="status-link {{ request('status') == 'processing' ? 'active' : '' }}">
                To Ship
                </a>
                <a href="{{ route('user.orders', ['status' => 'shipped']) }}" 
                class="status-link {{ request('status') == 'shipped' ? 'active' : '' }}">
                To Receive
                </a>
                <a href="{{ route('user.orders', ['status' => 'completed']) }}" 
                class="status-link {{ request('status') == 'completed' ? 'active' : '' }}">
                Completed
                </a>
                <a href="{{ route('user.orders', ['status' => 'cancelled']) }}" 
                class="status-link {{ request('status') == 'cancelled' ? 'active' : '' }}">
                Cancelled
                </a>
                <a href="{{ route('user.orders', ['status' => 'return_refund']) }}" 
                class="status-link {{ request('status') == 'return_refund' ? 'active' : '' }}">
                Return Refund
                </a>
            </div>
        @forelse($orders as $order)
            <div class="order-card">
                <!-- Order Header -->
                <div class="order-header">
                    <div>
                        <div class="order-number">Order #{{ $order->order_number }}</div>
                        <div class="order-date">{{ $order->created_at->format('M d, Y - h:i A') }}</div>
                    </div>
                    <div class="status-badge 
                        {{ $order->status == 'pending' ? 'status-pending' : '' }}
                        {{ $order->status == 'completed' ? 'status-completed' : '' }}
                        {{ $order->status == 'cancelled' ? 'status-cancelled' : '' }}
                        {{ $order->status == 'processing' ? 'status-processing' : '' }}
                    ">
                        {{ ucfirst($order->status) }}
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-items">
                    @foreach($order->items as $item)
                        <div class="order-item">
                            <img src="{{ asset('product/' . $item->product->image) }}" 
                                 alt="{{ $item->product_name }}" 
                                 class="product-image">
                            
                            <div class="product-details">
                                <div class="product-name">{{ $item->product_name }}</div>
                                <div class="product-specs">
                                    <div class="spec-item">
                                        <span class="spec-label">x</span> {{ $item->quantity }}
                                    </div>
                                    <div class="spec-item">
                                        <span class="spec-label">Price:</span> ₱{{ number_format($item->price, 2) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-pricing">
                                <div class="item-total">₱{{ number_format($item->total, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="payment-info">
                        <div><strong>Payment Method:</strong></div>
                        <div class="payment-method">
                            @if($order->payment)
                                {{ ucfirst($order->payment->method) }} 
                                <span class="payment-status">({{ ucfirst($order->payment->status) }})</span>
                            @else
                                <span style="color: #dc3545;">Not available</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="order-total">
                        <div class="total-label">Order Total</div>
                        <div class="total-amount">₱{{ number_format($order->total, 2) }}</div>
                    </div>
                    
                    <div class="order-actions">
                        @if($order->status == 'pending')
                        <button class="btn btn-outline order-action-btn" 
                                data-id="{{ $order->id }}" 
                                data-action="cancel">
                            Cancel Order
                        </button>
                    @endif

                    @if($order->status == 'processing')
                        <button class="btn btn-outline">Contact Us</button>
                    @endif

                    <button class="btn btn-primary">View Details</button>

                    @if($order->status == 'shipped')
                        <button class="btn btn-outline">Contact Us</button>
                        <button class="btn btn-outline order-action-btn" 
                                data-id="{{ $order->id }}" 
                                data-action="received">
                            Order Received
                        </button>
                    @endif

                    @if($order->status == 'completed')
                        <button class="btn btn-outline">Buy Again</button>
                    @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-orders">
                <span class="material-symbols-outlined">
                    box
                </span>
                <div class="empty-orders-text">No orders yet</div>
                <div class="empty-orders-subtext">When you place orders, they'll appear here</div>
                <a href="{{ route('shop-page') }}" class="btn btn-primary" style="margin-top: 16px;">Start Shopping</a>
            </div>
        @endforelse
    </div>
</div>
<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('order-action-btn')) {
        const orderId = e.target.getAttribute('data-id');
        const action = e.target.getAttribute('data-action');
        handleUserOrderAction(orderId, action, e);
    }
});

function handleUserOrderAction(orderId, action, event) {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Processing...';
    button.disabled = true;

    let endpoint = '';
    switch(action) {
        case 'received':
            endpoint = `/user/orders/${orderId}/received`;
            break;
        case 'cancel':
            endpoint = `/user/orders/${orderId}/cancel`;
            break;
        default:
            button.textContent = originalText;
            button.disabled = false;
            return;
    }

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const orderCard = button.closest('.order-card');
            const statusBadge = orderCard.querySelector('.status-badge');
            statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            statusBadge.className = 'status-badge';
            statusBadge.classList.add(`status-${data.status}`);
            updateOrderActions(orderCard, data.status, orderId);
            showUserNotification(`Order #${data.orderNumber} ${action === 'received' ? 'marked as received' : 'cancelled'} successfully`, 'success');
        } else {
            throw new Error(data.message || 'Action failed');
        }
    })
    .catch(error => {
        showUserNotification(`Failed to ${action} order`, 'error');
        console.error('Error:', error);
    })
    .finally(() => {
        button.textContent = originalText;
        button.disabled = false;
    });
}

// Optional: Auto-refresh order status (polls server every 30 seconds)
// Uncomment if you want real-time updates without user action
/*
setInterval(function() {
    // Only refresh if page is visible
    if (!document.hidden) {
        checkForOrderUpdates();
    }
}, 30000);

function checkForOrderUpdates() {
    const orderCards = document.querySelectorAll('.order-card');
    const orderIds = Array.from(orderCards).map(card => {
        return card.querySelector('.order-number').textContent.replace('Order #', '');
    });
    
    if (orderIds.length === 0) return;
    
    fetch('/user/orders/check-updates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ order_ids: orderIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.updates && data.updates.length > 0) {
            data.updates.forEach(update => {
                const orderCard = document.querySelector(`.order-card:has(.order-number:contains("${update.order_number}"))`);
                if (orderCard) {
                    const statusBadge = orderCard.querySelector('.status-badge');
                    if (statusBadge.textContent.toLowerCase() !== update.status) {
                        // Status has changed
                        statusBadge.textContent = update.status.charAt(0).toUpperCase() + update.status.slice(1);
                        statusBadge.className = `status-badge status-${update.status}`;
                        updateOrderActions(orderCard, update.status, update.id);
                        showUserNotification(`Order #${update.order_number} status updated to ${update.status}`, 'info');
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Error checking order updates:', error);
    });
}
*/
</script>
@endsection