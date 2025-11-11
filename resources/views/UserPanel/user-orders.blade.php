@extends('layouts.user-dash-layout')
@section('dashboard-content')

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">My Orders</h1>
    <p class="page-subtitle">Track and manage your orders</p>
</div>

<!-- Order Status Navigation -->
<nav class="status-navigation" aria-label="Order status filter">
    <!-- Mobile: Dropdown -->
    <div class="status-mobile">
        <select id="statusFilter" 
                class="status-select" 
                data-filter="status"
                aria-label="Filter orders by status">
            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>
                All Orders
            </option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                To Pay
            </option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                To Ship
            </option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>
                To Receive
            </option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                Completed
            </option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                Cancelled
            </option>
            <option value="cancellation_requested" {{ request('status') == 'cancellation_requested' ? 'selected' : '' }}>
                Cancellation Requested
            </option>
            <option value="return_refund" {{ request('status') == 'return_refund' ? 'selected' : '' }}>
                Return/Refund
            </option>
        </select>
    </div>

    <!-- Desktop: Tabs -->
    <div class="status-desktop">
        <div class="status-tabs">
            <a href="#" 
               class="status-tab {{ request('status') == 'all' || !request('status') ? 'is-active' : '' }}"
               data-status="all"
               aria-current="{{ request('status') == 'all' || !request('status') ? 'page' : 'false' }}">
                All
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'pending' ? 'is-active' : '' }}"
               data-status="pending"
               aria-current="{{ request('status') == 'pending' ? 'page' : 'false' }}">
                To Pay
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'processing' ? 'is-active' : '' }}"
               data-status="processing"
               aria-current="{{ request('status') == 'processing' ? 'page' : 'false' }}">
                To Ship
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'shipped' ? 'is-active' : '' }}"
               data-status="shipped"
               aria-current="{{ request('status') == 'shipped' ? 'page' : 'false' }}">
                To Receive
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'completed' ? 'is-active' : '' }}"
               data-status="completed"
               aria-current="{{ request('status') == 'completed' ? 'page' : 'false' }}">
                Completed
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'cancelled' ? 'is-active' : '' }}"
               data-status="cancelled"
               aria-current="{{ request('status') == 'cancelled' ? 'page' : 'false' }}">
                Cancelled
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'cancellation_requested' ? 'is-active' : '' }}"
               data-status="cancellation_requested"
               aria-current="{{ request('status') == 'cancellation_requested' ? 'page' : 'false' }}">
                Cancellation Requested
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'return_refund' ? 'is-active' : '' }}"
               data-status="return_refund"
               aria-current="{{ request('status') == 'return_refund' ? 'page' : 'false' }}">
                Return/Refund
            </a>
        </div>
    </div>
</nav>

<!-- Orders List -->
<div class="orders-list" id="ordersList">
    <!-- Loading State -->
    <div class="loading-state" id="loadingState" style="display: none;">
        <div class="loading-spinner"></div>
        <p class="loading-text">Loading orders...</p>
    </div>

    <!-- Orders Container -->
    <div id="ordersContainer">
    @forelse($orders as $order)
        <article class="order-card" data-status="{{ $order->status }}">
           <!-- Order Header -->
            <header class="order-header">
                <div class="order-info">
                    @if($order->tracking_number && in_array($order->status, ['shipped', 'completed']))
                        <div class="tracking-info mb-1 flex items-center gap-2">
                            <svg class="tracking-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="tracking-label font-semibold" style="color:#020202;">Tracking #:</span>
                            
                            <span class="tracking-number text-lg font-medium" style="color:#020202;">{{ $order->tracking_number }}</span>
                            
                            <button onclick="copyTrackingNumber('{{ $order->tracking_number }}')"
                                class="tracking-copy text-xs border px-2 py-0.5 rounded"
                                type="button">
                                Copy
                            </button>
                        </div>
                    @endif

                    <span class="order-number block leading-tight text-sm font-semibold" style="color:#020202;">
                        Order #{{ $order->order_number }}
                    </span>

                    <time class="order-date block text-sm opacity-70" datetime="{{ $order->created_at->toIso8601String() }}">
                        {{ $order->created_at->format('M d, Y - h:i A') }}
                    </time>
                </div>

                <span class="status-badge status-{{ $order->status }}" role="status">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </header>


            <!-- Order Items -->
            <div class="order-items">
                @foreach($order->items as $item)
                    <div class="order-item">
                        <img src="{{ asset('product/' . $item->product->image) }}" 
                             alt="{{ $item->product_name }}" 
                             class="item-image"
                             loading="lazy">
                        
                        <div class="item-details">
                            <h3 class="item-name">{{ $item->product_name }}</h3>
                            <div class="item-meta">
                                <span class="item-quantity">x{{ $item->quantity }}</span>
                                <span class="item-price">₱{{ number_format($item->price, 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="item-total">₱{{ number_format($item->total, 2) }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Order Footer -->
            <footer class="order-footer">
                <div class="payment-info">
                    <span class="payment-label">Payment:</span>
                    @if($order->payment)
                        <span class="payment-method">{{ ucfirst($order->payment->method) }}</span>
                        <span class="payment-status">({{ ucfirst($order->payment->status) }})</span>
                    @else
                        <span class="payment-error">Not available</span>
                    @endif
                </div>

                <div class="order-actions">
                    <div class="order-total-section">
                        <span class="total-label">Order Total</span>
                        <span class="total-amount">₱{{ number_format($order->total, 2) }}</span>
                    </div>

                    <div class="action-buttons">
                        @if($order->status == 'pending' || $order->status == 'processing')
                            <button class="btn btn-danger order-action-btn" 
                                    data-id="{{ $order->id }}" 
                                    data-action="request-cancel"
                                    type="button">
                                Request Cancellation
                            </button>
                        @endif

                        @if($order->status == 'cancellation_requested')
                            <div class="btn btn-warning-static">
                                Cancellation Pending Approval
                            </div>
                        @endif

                        @if($order->status == 'processing')
                            <a href="{{ route('view.contact') }}" class="btn btn-secondary">
                                Contact Us
                            </a>
                        @endif

                        @if($order->status == 'shipped')
                            <a href="{{ route('view.contact') }}" class="btn btn-secondary">
                                Contact Us
                            </a>
                            <button class="btn btn-primary order-action-btn" 
                                    data-id="{{ $order->id }}" 
                                    data-action="confirm-delivery"
                                    type="button">
                                Order Received
                            </button>
                        @endif

                        @if($order->status == 'completed')
                            <button class="btn btn-secondary" type="button">
                                Buy Again
                            </button>
                        @endif

                        <button class="btn btn-primary" type="button">
                            View Details
                        </button>
                    </div>
                </div>
            </footer>
        </article>
    @empty
        <div class="empty-state" id="emptyState">
            <img src="{{ asset('img/logo/Byond.Co_Logomark_Red Mud.webp') }}" alt="No orders" class="empty-icon">
            <h3 class="empty-title">No orders yet</h3>
            <p class="empty-text">When you place orders, they'll appear here</p>
                <a href="{{ route('shop-page') }}" class="btn-primary-color btn-md">
                    Start Shopping
                </a>
        </div>
    @endforelse
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" class="toast-container" aria-live="polite" aria-atomic="true"></div>
@include('components.cancellation-modal')

@endsection