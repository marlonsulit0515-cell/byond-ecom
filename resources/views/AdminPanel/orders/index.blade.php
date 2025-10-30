@extends('layouts.dashboard')
@section('maincontent')
<script>
    // Prevent caching - always reload fresh data when returning to this page
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || performance.getEntriesByType("navigation")[0].type === 'back_forward') {
            window.location.reload();
        }
    });
</script>
<head>
    <title>Order Management - Admin</title>
    <link href="{{ asset('css/order-management-tbl.css') }}" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('script/admin-order-management.js') }}" defer></script>
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
                <form method="GET" class="flex items-end gap-4" id="filtersForm">
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
                    <!-- Hidden input to maintain status filter -->
                    <input type="hidden" name="status" id="statusFilter" value="{{ request('status') }}">

                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-hover transition-all duration-200 border-none cursor-pointer">
                            <span class="icon icon-search"></span>
                            <span>Search</span>
                        </button>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 transition-all duration-200 no-underline">
                            <span class="icon icon-times"></span>
                            <span>Clear All</span>
                        </a>
                    </div>
                    <!-- Sort dropdown -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100" onchange="document.getElementById('filtersForm').submit()">
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
                <table class="w-full border-collapse compact-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Payment</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="border-b border-gray-100 transition-colors duration-200 hover:bg-gray-50">
                            <td class="px-4 py-3 align-middle">
                                <div>
                                    <div class="font-semibold text-gray-900 mb-1">{{ $order->order_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->items->count() }} items</div>
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
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium uppercase tracking-wide status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <span class="font-semibold text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div>
                                    <div class="font-medium text-gray-900 mb-1 capitalize">
                                        {{ ucfirst($order->payment->method ?? 'N/A') }}
                                    </div>
                                    <div class="text-xs text-gray-500 capitalize">
                                        {{ ucfirst($order->payment->status ?? 'N/A') }}
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
                                        <span class="icon icon-eye"></span>
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

        <!-- Enhanced Pagination Section -->
        @if($orders->hasPages())
        <div class="pagination-container">
            <!-- Previous Button -->
            @if ($orders->onFirstPage())
                <span class="pagination-btn disabled">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </span>
            @else
                <a href="{{ $orders->previousPageUrl() }}" class="pagination-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </a>
            @endif

            <!-- Page Numbers -->
            <div class="flex items-center gap-2">
                @php
                    $currentPage = $orders->currentPage();
                    $lastPage = $orders->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);
                @endphp

                @if($start > 1)
                    <a href="{{ $orders->url(1) }}" class="pagination-btn">1</a>
                    @if($start > 2)
                        <span class="text-gray-500">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $currentPage)
                        <span class="pagination-btn active">{{ $i }}</span>
                    @else
                        <a href="{{ $orders->url($i) }}" class="pagination-btn">{{ $i }}</a>
                    @endif
                @endfor

                @if($end < $lastPage)
                    @if($end < $lastPage - 1)
                        <span class="text-gray-500">...</span>
                    @endif
                    <a href="{{ $orders->url($lastPage) }}" class="pagination-btn">{{ $lastPage }}</a>
                @endif
            </div>

            <!-- Next Button -->
            @if ($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="pagination-btn">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="pagination-btn disabled">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif

            <!-- Go to Page Input -->
            <div class="page-input-group">
                <span class="text-sm text-gray-600">Go to page:</span>
                <input 
                    type="number" 
                    class="page-input" 
                    min="1" 
                    max="{{ $orders->lastPage() }}" 
                    value="{{ $orders->currentPage() }}"
                    onkeypress="if(event.key === 'Enter') { 
                        const page = parseInt(this.value);
                        if(page >= 1 && page <= {{ $orders->lastPage() }}) {
                            window.location.href = '{{ $orders->url(1) }}'.replace(/page=\d+/, 'page=' + page);
                        }
                    }"
                >
                <span class="text-sm text-gray-600">of {{ $orders->lastPage() }}</span>
            </div>
        </div>
        @endif
    </div>

    <script>
        function filterByStatus(status) {
            document.getElementById('statusFilter').value = status;
            document.getElementById('filtersForm').submit();
        }
    </script>
</body>
@endsection