@extends('layouts.user-dash-layout')
<link href="{{ asset('css/user-order.css') }}" rel="stylesheet" />

@section('dashboard-content')
<!-- Page Header -->
<div class="mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">My Orders</h1>
    <p class="mt-1 text-sm text-gray-600">Track and manage your orders</p>
</div>

<!-- Order Status Navigation -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 md:mb-6">
    <!-- Mobile: Dropdown -->
    <div class="block md:hidden">
        <select id="statusFilter" class="w-full px-4 py-3 text-sm font-medium border-0 focus:ring-2 focus:ring-black rounded-lg bg-gray-50" 
                onchange="window.location.href = this.value">
            <option value="{{ route('user.orders', ['status' => 'all']) }}" 
                    {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>
                All Orders
            </option>
            <option value="{{ route('user.orders', ['status' => 'pending']) }}" 
                    {{ request('status') == 'pending' ? 'selected' : '' }}>
                To Pay
            </option>
            <option value="{{ route('user.orders', ['status' => 'processing']) }}" 
                    {{ request('status') == 'processing' ? 'selected' : '' }}>
                To Ship
            </option>
            <option value="{{ route('user.orders', ['status' => 'shipped']) }}" 
                    {{ request('status') == 'shipped' ? 'selected' : '' }}>
                To Receive
            </option>
            <option value="{{ route('user.orders', ['status' => 'completed']) }}" 
                    {{ request('status') == 'completed' ? 'selected' : '' }}>
                Completed
            </option>
            <option value="{{ route('user.orders', ['status' => 'cancelled']) }}" 
                    {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                Cancelled
            </option>
            <option value="{{ route('user.orders', ['status' => 'return_refund']) }}" 
                    {{ request('status') == 'return_refund' ? 'selected' : '' }}>
                Return/Refund
            </option>
        </select>
    </div>

    <!-- Desktop: Tabs (Horizontal Scroll) -->
    <div class="hidden md:block overflow-x-auto">
        <div class="flex min-w-max">
            <a href="{{ route('user.orders', ['status' => 'all']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'all' || !request('status') ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                All
            </a>
            <a href="{{ route('user.orders', ['status' => 'pending']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'pending' ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                To Pay
            </a>
            <a href="{{ route('user.orders', ['status' => 'processing']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'processing' ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                To Ship
            </a>
            <a href="{{ route('user.orders', ['status' => 'shipped']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'shipped' ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                To Receive
            </a>
            <a href="{{ route('user.orders', ['status' => 'completed']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'completed' ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                Completed
            </a>
            <a href="{{ route('user.orders', ['status' => 'cancelled']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'cancelled' ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                Cancelled
            </a>
            <a href="{{ route('user.orders', ['status' => 'return_refund']) }}" 
               class="flex-1 px-4 lg:px-6 py-4 text-center font-medium text-sm transition-colors duration-200 border-b-2 whitespace-nowrap
               {{ request('status') == 'return_refund' ? 'border-black text-black bg-gray-50' : 'border-transparent text-gray-600 hover:text-black hover:bg-gray-50' }}">
                Return/Refund
            </a>
        </div>
    </div>
</div>

<!-- Orders List -->
<div class="space-y-3 md:space-y-4">
    @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <!-- Order Header -->
            <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                            <span class="text-sm font-semibold text-gray-900">Order #{{ $order->order_number }}</span>
                            <span class="text-xs sm:text-sm text-gray-500">{{ $order->created_at->format('M d, Y - h:i A') }}</span>
                        </div>
                    </div>
                    <div class="self-start sm:self-auto">
                        <span class="inline-flex items-center px-2.5 md:px-3 py-1 rounded-full text-xs font-medium
                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $order->status == 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status == 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                        ">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="px-4 md:px-6 py-3 md:py-4 flex gap-3 md:gap-4">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('product/' . $item->product->image) }}" 
                                 alt="{{ $item->product_name }}" 
                                 class="w-16 h-16 md:w-20 md:h-20 object-cover rounded-lg border border-gray-200">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 mb-1 line-clamp-2">{{ $item->product_name }}</h3>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 text-xs sm:text-sm text-gray-600">
                                <span>Qty: {{ $item->quantity }}</span>
                                <span class="hidden sm:inline">₱{{ number_format($item->price, 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex-shrink-0 text-right">
                            <div class="text-sm md:text-base font-semibold text-gray-900">₱{{ number_format($item->total, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Footer -->
            <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-200">
                <!-- Mobile Layout -->
                <div class="block lg:hidden space-y-3">
                    <!-- Payment Info -->
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600">Payment:</span>
                        @if($order->payment)
                            <div class="flex items-center gap-1 flex-wrap justify-end">
                                <span class="font-medium text-gray-900">{{ ucfirst($order->payment->method) }}</span>
                                <span class="text-gray-500">({{ ucfirst($order->payment->status) }})</span>
                            </div>
                        @else
                            <span class="text-red-600">Not available</span>
                        @endif
                    </div>

                    <!-- Order Total -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Order Total:</span>
                        <span class="text-lg font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2">
                        @if($order->status == 'pending')
                            <button class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors order-action-btn" 
                                    data-id="{{ $order->id }}" 
                                    data-action="cancel">
                                Cancel Order
                            </button>
                        @endif

                        @if($order->status == 'processing')
                            <button class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <a href="{{ route('view.contact') }}">Contact Us</a>    
                            </button>
                        @endif

                        @if($order->status == 'shipped')
                            <button class="w-full px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors order-action-btn" 
                                    data-id="{{ $order->id }}" 
                                    data-action="received">
                                Order Received
                            </button>
                            <button class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <a href="{{ route('view.contact') }}">Contact Us</a>
                            </button>
                        @endif

                        @if($order->status == 'completed')
                            <button class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Buy Again
                            </button>
                        @endif

                        <button class="w-full px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">
                            View Details
                        </button>
                    </div>
                </div>

                <!-- Desktop Layout -->
                <div class="hidden lg:flex flex-wrap items-center justify-between gap-4">
                    <!-- Payment Info -->
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-600">Payment:</span>
                        @if($order->payment)
                            <span class="font-medium text-gray-900">{{ ucfirst($order->payment->method) }}</span>
                            <span class="text-gray-500">({{ ucfirst($order->payment->status) }})</span>
                        @else
                            <span class="text-red-600">Not available</span>
                        @endif
                    </div>

                    <!-- Order Total & Action Buttons -->
                    <div class="flex items-center gap-4 xl:gap-6">
                        <div class="text-right">
                            <div class="text-xs text-gray-600 mb-1">Order Total</div>
                            <div class="text-xl font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 flex-wrap">
                            @if($order->status == 'pending')
                                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors order-action-btn" 
                                        data-id="{{ $order->id }}" 
                                        data-action="cancel">
                                    Cancel Order
                                </button>
                            @endif

                            @if($order->status == 'processing')
                                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Contact Us
                                </button>
                            @endif

                            @if($order->status == 'shipped')
                                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Contact Us
                                </button>
                                <button class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors order-action-btn" 
                                        data-id="{{ $order->id }}" 
                                        data-action="received">
                                    Order Received
                                </button>
                            @endif

                            @if($order->status == 'completed')
                                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Buy Again
                                </button>
                            @endif

                            <button class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 md:p-12 text-center">
            <div class="max-w-md mx-auto">
                <svg class="w-20 h-20 md:w-24 md:h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-2">No orders yet</h3>
                <p class="text-sm md:text-base text-gray-600 mb-6">When you place orders, they'll appear here</p>
                <a href="{{ route('shop-page') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-black text-white text-sm md:text-base font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Start Shopping
                </a>
            </div>
        </div>
    @endforelse
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
            showUserNotification(`Order #${data.orderNumber} ${action === 'received' ? 'marked as received' : 'cancelled'} successfully`, 'success');
            setTimeout(() => window.location.reload(), 1000);
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

function showUserNotification(message, type) {
    alert(message);
}
</script>
@endsection