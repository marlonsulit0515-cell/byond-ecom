@extends('layouts.dashboard')

@section('maincontent')
<head>
    <title>Order Management - Admin</title>
    <link href="{{ asset('css/order-management-tbl.css') }}" rel="stylesheet" />
    <script src="{{ asset('script/admin-order-management.js') }}" defer></script>
</head>

<body>
    <div class="order-management-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Order Management</h1>
                <p class="page-description">Manage and track customer orders</p>
            </div>
        </div>

        <!-- Fixed Filters Section -->
        <div class="filters-section">
            <div class="filters-card">
                <form method="GET" class="filters-form" id="filtersForm">
                    <!-- Search input -->
                    <div class="form-group form-group--search">
                        <label class="form-label">Search Orders</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search by order number, customer name, or email..."
                            class="form-control form-control--search">
                    </div>
                    <!-- Hidden input to maintain status filter -->
                    <input type="hidden" name="status" id="statusFilter" value="{{ request('status') }}">

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary">
                            <span class="icon icon-search"></span>
                            <span class="btn__text">Search</span>
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn--secondary">
                            <span class="icon icon-times"></span>
                            <span class="btn__text">Clear All</span>
                        </a>
                    </div>
                     <!-- Sort dropdown - NOW INSIDE THE FORM -->
                    <div class="form-group form-group--sort">
                        <label class="form-label">Sort By</label>
                        <select name="sort" class="form-control" onchange="document.getElementById('filtersForm').submit()">
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
        <div class="bulk-actions-section">
            <div class="bulk-actions-card" id="bulkActions" style="display: none;">
                <form method="POST" action="{{ route('orders.bulk-update') }}" class="bulk-actions-form" id="bulkForm">
                    @csrf
                    <!-- Remove the single hidden input, we'll create multiple ones dynamically -->
                    
                    <div class="bulk-actions__info">
                        <span class="bulk-actions__text">
                            Selected: <span id="selectedCount" class="bulk-actions__count">0</span> orders
                        </span>
                    </div>

                    <div class="form-group form-group--bulk-status">
                        <select name="status" required class="form-control form-control--bulk">
                            <option value="">Change Status To...</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="bulk-actions__buttons">
                        <button type="submit" class="btn btn--success">
                            <span class="btn__text">Update Selected</span>
                        </button>
                        <button type="button" onclick="clearSelection()" class="btn btn--secondary">
                            <span class="btn__text">Clear Selection</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
                <!-- Statistics Cards Section -->
        <div class="statistics-section">
            <div class="stats-grid">
                <!-- All Orders Card (shows all) -->
                <div class="stat-card stat-card--all {{ !request('status') ? 'stat-card--active' : '' }}" 
                    onclick="filterByStatus('')">
                    <div class="stat-card__icon">
                        <span class="icon icon-list"></span>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">All Orders</h3>
                        <p class="stat-card__value">{{ $allOrdersCount }}</p>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="stat-card stat-card--pending {{ request('status') == 'pending' ? 'stat-card--active' : '' }}" 
                    onclick="filterByStatus('pending')">
                    <div class="stat-card__icon">
                        <span class="icon icon-clock"></span>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Pending</h3>
                        <p class="stat-card__value">{{ $statusCounts['pending'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Processing Orders -->
                <div class="stat-card stat-card--processing {{ request('status') == 'processing' ? 'stat-card--active' : '' }}" 
                    onclick="filterByStatus('processing')">
                    <div class="stat-card__icon">
                        <span class="icon icon-cog"></span>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Processing</h3>
                        <p class="stat-card__value">{{ $statusCounts['processing'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Shipped Orders -->
                <div class="stat-card stat-card--shipped {{ request('status') == 'shipped' ? 'stat-card--active' : '' }}" 
                    onclick="filterByStatus('shipped')">
                    <div class="stat-card__icon">
                        <span class="icon icon-truck"></span>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Shipped</h3>
                        <p class="stat-card__value">{{ $statusCounts['shipped'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="stat-card stat-card--completed {{ request('status') == 'completed' ? 'stat-card--active' : '' }}" 
                    onclick="filterByStatus('completed')">
                    <div class="stat-card__icon">
                        <span class="icon icon-check"></span>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Completed</h3>
                        <p class="stat-card__value">{{ $statusCounts['completed'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Cancelled Orders -->
                <div class="stat-card stat-card--cancelled {{ request('status') == 'cancelled' ? 'stat-card--active' : '' }}" 
                    onclick="filterByStatus('cancelled')">
                    <div class="stat-card__icon">
                        <span class="icon icon-times"></span>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Cancelled</h3>
                        <p class="stat-card__value">{{ $statusCounts['cancelled'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table Section -->
        <div class="table-section">
            <div class="table-container">
                <table class="orders-table">
                    <thead class="table-header">
                        <tr class="table-header__row">
                            <th class="table-header__cell table-header__cell--checkbox">
                                <input 
                                    type="checkbox" 
                                    id="selectAll" 
                                    class="table-checkbox table-checkbox--all"
                                    onchange="toggleSelectAll()"
                                >
                            </th>
                            <th class="table-header__cell table-header__cell--order">Order</th>
                            <th class="table-header__cell table-header__cell--customer">Customer</th>
                            <th class="table-header__cell table-header__cell--status">Status</th>
                            <th class="table-header__cell table-header__cell--total">Total</th>
                            <th class="table-header__cell table-header__cell--payment">Payment</th>
                            <th class="table-header__cell table-header__cell--date">Placed Order Date</th>
                            <th class="table-header__cell table-header__cell--actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @forelse($orders as $order)
                        <tr class="table-row">
                            <td class="table-cell table-cell--checkbox">
                                <input 
                                    type="checkbox" 
                                    class="table-checkbox order-checkbox" 
                                    value="{{ $order->id }}" 
                                    onchange="updateSelection()"
                                >
                            </td>

                            <td class="table-cell table-cell--order">
                                <div class="order-info">
                                    <div class="order-info__number">{{ $order->order_number }}</div>
                                    <div class="order-info__items">{{ $order->items->count() }} items</div>
                                </div>
                            </td>

                            <td class="table-cell table-cell--customer">
                                <div class="customer-info">
                                    <div class="customer-info__name">{{ $order->full_name }}</div>
                                    <div class="customer-info__email">
                                        {{ $order->user->email ?? $order->guest_email }}
                                    </div>
                                </div>
                            </td>

                            <td class="table-cell table-cell--status">
                                <span class="status-badge status-badge--{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="table-cell table-cell--total">
                                <span class="order-total">₱{{ number_format($order->total, 2) }}</span>
                            </td>

                            <td class="table-cell table-cell--payment">
                                <div class="payment-info">
                                    <div class="payment-info__method">
                                        {{ ucfirst($order->payment->method ?? 'N/A') }}
                                    </div>
                                    <div class="payment-info__status">
                                        {{ ucfirst($order->payment->status ?? 'N/A') }}
                                    </div>
                                </div>
                            </td>

                            <td class="table-cell table-cell--date">
                                <div class="order-date">
                                    <div class="order-date__day">{{ $order->created_at->format('M d, Y') }}</div>
                                    <div class="order-date__time">{{ $order->created_at->format('h:i A') }}</div>
                                </div>
                            </td>

                            <td class="table-cell table-cell--actions">
                                <div class="table-actions">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="action-btn action-btn--view" 
                                       title="View Order">
                                        <span class="icon icon-eye"></span>
                                    </a>

                                    <div class="action-dropdown">
                                        <button 
                                            type="button" 
                                            onclick="toggleStatusMenu({{ $order->id }})" 
                                            class="action-btn action-btn--edit"
                                            title="Edit Status">
                                            <span class="icon icon-edit"></span>
                                        </button>

                                        <div id="statusMenu-{{ $order->id }}" class="dropdown-menu">
                                            <form 
                                                method="POST" 
                                                action="{{ route('orders.update-status', $order) }}"
                                                class="dropdown-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" name="status" value="pending" 
                                                        class="dropdown-item dropdown-item--pending">
                                                    Pending
                                                </button>
                                                <button type="submit" name="status" value="processing" 
                                                        class="dropdown-item dropdown-item--processing">
                                                    Processing
                                                </button>
                                                <button type="submit" name="status" value="shipped" 
                                                        class="dropdown-item dropdown-item--shipped">
                                                    Shipped
                                                </button>
                                                <button type="submit" name="status" value="completed" 
                                                        class="dropdown-item dropdown-item--completed">
                                                    Completed
                                                </button>
                                                <button type="submit" name="status" value="cancelled" 
                                                        class="dropdown-item dropdown-item--cancelled dropdown-item--danger">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="table-row table-row--empty">
                            <td colspan="8" class="table-cell table-cell--empty">
                                <div class="empty-state">
                                    <div class="empty-state__icon">
                                        <span class="icon icon-cart"></span>
                                    </div>
                                    <h3 class="empty-state__title">No orders found</h3>
                                    <p class="empty-state__description">
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
        <div class="pagination-section">
            <div class="pagination-wrapper">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</body>
@endsection