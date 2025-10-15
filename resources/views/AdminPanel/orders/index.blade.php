@extends('layouts.dashboard')
@section('maincontent')

<head>
    
    <title>Order Management - Admin</title>
    <link href="{{ asset('css/order-management-tbl.css') }}" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('script/admin-order-management.js') }}" defer></script>

</head>

<body>
    <div class="p-6 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-12">
            <div class="space-y-2">
                <h1 class="text-3xl font-semibold text-gray-900 m-0">Order Management</h1>
                <p class="text-lg text-gray-600 m-0">Manage and track customer orders</p>
            </div>
        </div>

        <!-- Fixed Filters Section -->
        <div class="mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <form method="GET" class="flex items-end gap-4" id="filtersForm">
                    <!-- Search input -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search by order number, customer name, or email..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100">
                    </div>
                    <!-- Hidden input to maintain status filter -->
                    <input type="hidden" name="status" id="statusFilter" value="{{ request('status') }}">

                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-hover transition-all duration-200 border-none cursor-pointer">
                            <span class="icon icon-search"></span>
                            <span>Search</span>
                        </button>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 transition-all duration-200 no-underline">
                            <span class="icon icon-times"></span>
                            <span>Clear All</span>
                        </a>
                    </div>
                    <!-- Sort dropdown - NOW INSIDE THE FORM -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100" onchange="document.getElementById('filtersForm').submit()">
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

        <!-- Bulk Actions Section -->
        <div class="mb-8">
            <div class="bg-primary rounded-xl p-5 shadow-sm" id="bulkActions" style="display: none;">
                <form method="POST" action="{{ route('orders.bulk-update') }}" class="flex items-center gap-6" id="bulkForm">
                    @csrf
                    
                    <div class="text-white font-medium">
                        <span>Selected: <span id="selectedCount" class="bg-white/20 px-3 py-2 rounded-lg ml-2">0</span> orders</span>
                    </div>

                    <div>
                        <select name="status" required class="min-w-[200px] px-4 py-3 border-none rounded-lg text-base bg-white/95 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Change Status To...</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="flex gap-3 ml-auto">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-all duration-200 border-none cursor-pointer">
                            <span>Update Selected</span>
                        </button>
                        <button type="button" onclick="clearSelection()" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 transition-all duration-200 border-none cursor-pointer">
                            <span>Clear Selection</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards Section -->
        <div class="mb-10">
            <div class="grid grid-cols-6 gap-4">
                <!-- All Orders Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ !request('status') ? 'border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('')">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2 m-0">All Orders</h3>
                        <p class="text-2xl font-bold text-gray-900 m-0">{{ $allOrdersCount }}</p>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'pending' ? 'border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('pending')">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2 m-0">Pending</h3>
                        <p class="text-2xl font-bold text-gray-900 m-0">{{ $statusCounts['pending'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Processing Orders -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'processing' ? 'border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('processing')">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2 m-0">Processing</h3>
                        <p class="text-2xl font-bold text-gray-900 m-0">{{ $statusCounts['processing'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Shipped Orders -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'shipped' ? 'border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('shipped')">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2 m-0">Shipped</h3>
                        <p class="text-2xl font-bold text-gray-900 m-0">{{ $statusCounts['shipped'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'completed' ? 'border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('completed')">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2 m-0">Completed</h3>
                        <p class="text-2xl font-bold text-gray-900 m-0">{{ $statusCounts['completed'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Cancelled Orders -->
                <div class="bg-white rounded-xl p-6 shadow-sm border-2 border-transparent cursor-pointer transition-all duration-200 hover:-translate-y-px hover:shadow-md {{ request('status') == 'cancelled' ? 'border-primary shadow-md' : '' }}" 
                    onclick="filterByStatus('cancelled')">
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2 m-0">Cancelled</h3>
                        <p class="text-2xl font-bold text-gray-900 m-0">{{ $statusCounts['cancelled'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table Section -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-12 p-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">
                                <input 
                                    type="checkbox" 
                                    id="selectAll" 
                                    class="w-4 h-4 rounded"
                                    onchange="toggleSelectAll()"
                                >
                            </th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Order</th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Customer</th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Status</th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Total</th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Payment</th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Placed Order Date</th>
                            <th class="px-5 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wide border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="border-b border-gray-100 transition-colors duration-200 hover:bg-gray-50">
                            <td class="w-12 p-3 align-middle">
                                <input 
                                    type="checkbox" 
                                    class="w-4 h-4 rounded order-checkbox" 
                                    value="{{ $order->id }}" 
                                    onchange="updateSelection()"
                                >
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <div>
                                    <div class="font-semibold text-gray-900 mb-2">{{ $order->order_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->items->count() }} items</div>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <div>
                                    <div class="font-medium text-gray-900 mb-2">{{ $order->full_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $order->user->email ?? $order->guest_email }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium uppercase tracking-wide status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <span class="font-semibold text-gray-900 text-lg">₱{{ number_format($order->total, 2) }}</span>
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <div>
                                    <div class="font-medium text-gray-900 mb-2 capitalize">
                                        {{ ucfirst($order->payment->method ?? 'N/A') }}
                                    </div>
                                    <div class="text-sm text-gray-500 capitalize">
                                        {{ ucfirst($order->payment->status ?? 'N/A') }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <div>
                                    <div class="font-medium text-gray-900 mb-2">{{ $order->created_at->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-middle text-base">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-600 cursor-pointer transition-all duration-200 flex items-center justify-center text-sm no-underline hover:bg-blue-50 hover:border-primary hover:text-primary" 
                                       title="View Order">
                                        <span class="icon icon-eye"></span>
                                    </a>

                                    <div class="action-dropdown relative">
                                        <button 
                                            type="button" 
                                            onclick="toggleStatusMenu({{ $order->id }})" 
                                            class="w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-600 cursor-pointer transition-all duration-200 flex items-center justify-center text-sm hover:bg-yellow-50 hover:border-yellow-600 hover:text-yellow-600"
                                            title="Edit Status">
                                            <span class="icon icon-edit"></span>
                                        </button>

                                        <div id="statusMenu-{{ $order->id }}" class="dropdown-menu bg-white border border-gray-200 p-2 rounded-lg shadow-lg">
                                            <form 
                                                method="POST" 
                                                action="{{ route('orders.update-status', $order) }}"
                                                class="flex flex-col">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" name="status" value="pending" 
                                                        class="w-full px-4 py-3 bg-none border-none text-left text-sm text-gray-700 cursor-pointer transition-colors duration-200 hover:bg-gray-50">
                                                    Pending
                                                </button>
                                                <button type="submit" name="status" value="processing" 
                                                        class="w-full px-4 py-3 bg-none border-none text-left text-sm text-gray-700 cursor-pointer transition-colors duration-200 hover:bg-gray-50">
                                                    Processing
                                                </button>
                                                <button type="submit" name="status" value="shipped" 
                                                        class="w-full px-4 py-3 bg-none border-none text-left text-sm text-gray-700 cursor-pointer transition-colors duration-200 hover:bg-gray-50">
                                                    Shipped
                                                </button>
                                                <button type="submit" name="status" value="completed" 
                                                        class="w-full px-4 py-3 bg-none border-none text-left text-sm text-gray-700 cursor-pointer transition-colors duration-200 hover:bg-gray-50">
                                                    Completed
                                                </button>
                                                <button type="submit" name="status" value="cancelled" 
                                                        class="w-full px-4 py-3 bg-none border-none text-left text-sm text-red-600 cursor-pointer transition-colors duration-200 hover:bg-red-50">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
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

        <!-- Pagination Section -->
        @if($orders->hasPages())
        <div class="flex justify-center mt-8">
            <div class="flex gap-2">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</body>
@endsection