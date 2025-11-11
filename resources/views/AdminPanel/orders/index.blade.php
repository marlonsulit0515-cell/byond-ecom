@extends('layouts.dashboard')
@section('maincontent')
<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || performance.getEntriesByType("navigation")[0].type === 'back_forward') {
            window.location.reload();
        }
    });
</script>
<head>
    <title>Order Management - Admin</title>
</head>
<body>
    <div class="p-6 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="space-y-2">
                <h1 class="text-3xl font-semibold text-gray-900 m-0">Order Management</h1>
                <p class="text-lg text-gray-600 m-0">Manage and track customer orders</p>
            </div>
        </div>

       <!-- Fixed Filters Section -->
        <div class="mb-6">
            <div class="bg-white rounded-xl p-5 shadow-sm">
                
                <form method="GET" 
                    class="flex flex-col gap-4 lg:flex-row lg:items-end" 
                    id="filtersForm">

                    <!-- Search input -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search by order number, customer name, or email..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100">
                    </div>

                    <input type="hidden" name="status" id="statusFilter" value="{{ request('status') }}">

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary-color btn-sm flex items-center gap-2">
                            <span class="icon icon-search"></span>
                            <span>Search</span>
                        </button>

                        <a href="{{ route('orders.index') }}" class="btn-secondary-color btn-sm flex items-center gap-2 no-underline">
                            <span class="icon icon-times"></span>
                            <span>Clear All</span>
                        </a>
                    </div>

                    <!-- Sort -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"
                                onchange="document.getElementById('filtersForm').submit()">
                            <option value="">Default (Latest)</option>
                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date: Newest First</option>
                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date: Oldest First</option>
                            <option value="total_asc" {{ request('sort') == 'total_asc' ? 'selected' : '' }}>Total: Low to High</option>
                            <option value="total_desc" {{ request('sort') == 'total_desc' ? 'selected' : '' }}>Total: High to Low</option>
                        </select>
                    </div>

                </form>

            </div>
        </div>
        <!-- Statistics Cards Section -->
        <div class="mb-8">
            <div class="grid grid-cols-6 gap-3">
                <!-- All Orders Card -->
                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ !request('status') ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">All Orders</h3>
                        <p class="text-xl font-bold text-gray-900 m-0">{{ $allOrdersCount }}</p>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'pending' ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('pending')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">Pending</h3>
                        <p class="text-xl font-bold text-gray-900 m-0">{{ $statusCounts['pending'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Processing Orders -->
                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'processing' ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('processing')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">Processing</h3>
                        <p class="text-xl font-bold text-gray-900 m-0">{{ $statusCounts['processing'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Shipped Orders -->
                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'shipped' ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('shipped')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">Shipped</h3>
                        <p class="text-xl font-bold text-gray-900 m-0">{{ $statusCounts['shipped'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'completed' ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('completed')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">Completed</h3>
                        <p class="text-xl font-bold text-gray-900 m-0">{{ $statusCounts['completed'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'cancellation_requested' ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('cancellation_requested')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">Cancellation Requested</h3>
                        <p class="text-xl font-bold text-orange-600 m-0">{{ $statusCounts['cancellation_requested'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Cancelled Orders -->
                <div class="stat-card bg-white rounded-xl p-4 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'cancelled' ? 'stat-card--active border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('cancelled')">
                    <div>
                        <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-1.5 m-0">Cancelled</h3>
                        <p class="text-xl font-bold text-gray-900 m-0">{{ $statusCounts['cancelled'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table Section -->
        <div class="mb-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Payment</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Order Place Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="border-b border-gray-100 transition-colors duration-200 hover:bg-gray-50">
                            <td class="px-4 py-3 align-middle">
                                <div>
                                    <div class="font-semibold text-gray-900 mb-1">{{ $order->order_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->items->count() }} item/s</div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div>
                                    <div class="font-medium text-gray-900 mb-1">{{ $order->full_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $order->user->email ?? $order->guest_email }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium uppercase tracking-wide status-{{ str_replace('_', '-', $order->status) }}">
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <span class="font-semibold text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div>
                                    <div class="font-medium text-gray-900 mb-1 capitalize">
                                        <span class="font-semibold text-gray-800">{{ ucfirst($order->payment->method ?? 'N/A') }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 capitalize">
                                        <span class="font-semibold text-gray-800"></span>{{ ucfirst($order->payment->status ?? 'N/A') }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div>
                                    <div class="font-medium text-gray-900 mb-1">{{ $order->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-600 cursor-pointer transition-all duration-200 flex items-center justify-center text-xs no-underline hover:bg-blue-50 hover:border-primary hover:text-primary" 
                                       title="View Order">
                                        <span><img src="{{ asset('img/icons/view.png') }}" alt="view-order"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <div>
                                        <span class="icon icon-cart"></span>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 m-0">No orders found</h3>
                                    <p class="text-gray-500 m-0">
                                        Orders will appear here when customers place them
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="mt-8 mb-12 flex justify-center">
                <div class="pagination-custom">
                    {{ $orders->appends(request()->input())->links() }}
                </div>
            </div>
        @endif
    <script>
        function filterByStatus(status) {
            document.getElementById('statusFilter').value = status;
            document.getElementById('filtersForm').submit();
        }
    </script>
</body>
@endsection