@extends('layouts.dashboard')
<!-- Login Section -->
@section('maincontent')

<head>
    <title>Order Management - Admin</title>
    <link href="{{ asset('css/order-management-tbl.css') }}" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Order Management</h1>
            <p>Manage and track customer orders</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-icon icon icon-clock"></div>
                <div class="stat-info">
                    <h3>Pending</h3>
                    <p>{{ $statusCounts['pending'] }}</p>
                </div>
            </div>
            <div class="stat-card processing">
                <div class="stat-icon icon icon-cog"></div>
                <div class="stat-info">
                    <h3>Processing</h3>
                    <p>{{ $statusCounts['processing'] }}</p>
                </div>
            </div>
            <div class="stat-card shipped">
                <div class="stat-icon icon icon-truck"></div>
                <div class="stat-info">
                    <h3>Shipped</h3>
                    <p>{{ $statusCounts['shipped'] }}</p>
                </div>
            </div>
            <div class="stat-card completed">
                <div class="stat-icon icon icon-check"></div>
                <div class="stat-info">
                    <h3>Completed</h3>
                    <p>{{ $statusCounts['completed'] }}</p>
                </div>
            </div>
            <div class="stat-card cancelled">
                <div class="stat-icon icon icon-times"></div>
                <div class="stat-info">
                    <h3>Cancelled</h3>
                    <p>{{ $statusCounts['cancelled'] }}</p>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card">
            <form method="GET" class="form-row">
                <div class="form-group">
                    <label>Search Orders</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by order number, customer name, or email..."
                           class="form-control">
                </div>
                <div class="form-group" style="flex: 0 0 auto; min-width: auto;">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <span class="icon icon-search"></span>Filter
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <span class="icon icon-times"></span>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="card bulk-actions" id="bulkActions">
            <form method="POST" action="{{ route('orders.bulk-update') }}" class="form-row">
                @csrf
                <input type="hidden" name="order_ids" id="selectedOrders">
                <span>Selected: <span id="selectedCount">0</span> orders</span>
                <div class="form-group" style="flex: 0 0 auto; min-width: auto;">
                    <select name="status" required class="form-control">
                        <option value="">Change Status To...</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update Selected</button>
                <button type="button" onclick="clearSelection()" class="btn btn-secondary">Clear Selection</button>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <input type="checkbox" class="order-checkbox" 
                                   value="{{ $order->id }}" onchange="updateSelection()">
                        </td>
                        <td>
                            <div>
                                <div style="font-weight: 500; color: #111827;">{{ $order->order_number }}</div>
                                <div style="font-size: 0.875rem; color: #6b7280;">{{ $order->items->count() }} items</div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div style="font-weight: 500; color: #111827;">{{ $order->full_name }}</div>
                                <div style="font-size: 0.875rem; color: #6b7280;">{{ $order->user->email ?? $order->guest_email }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td style="font-weight: 500; color: #111827;">
                            ₱{{ number_format($order->total, 2) }}
                        </td>
                        <td>
                            <div style="color: #111827;">{{ ucfirst($order->payment->method ?? 'N/A') }}</div>
                            <div style="font-size: 0.875rem; color: #6b7280;">{{ ucfirst($order->payment->status ?? 'N/A') }}</div>
                        </td>
                        <td style="font-size: 0.875rem; color: #6b7280;">
                            {{ $order->created_at->format('M d, Y') }}
                            <br>
                            {{ $order->created_at->format('h:i A') }}
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('orders.show', $order) }}" class="action-btn view">
                                    <span class="icon icon-eye"></span>
                                </a>
                                <div class="dropdown">
                                    <button type="button" onclick="toggleStatusMenu({{ $order->id }})" class="action-btn">
                                        <span class="icon icon-edit"></span>
                                    </button>
                                    <div id="statusMenu-{{ $order->id }}" class="dropdown-menu">
                                        <form method="POST" action="{{ route('orders.update-status', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="pending">Pending</button>
                                            <button type="submit" name="status" value="processing">Processing</button>
                                            <button type="submit" name="status" value="shipped">Shipped</button>
                                            <button type="submit" name="status" value="completed">Completed</button>
                                            <button type="submit" name="status" value="cancelled" class="danger">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="icon icon-cart"></div>
                            <h3>No orders found</h3>
                            <p>Orders will appear here when customers place them</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $orders->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <script>
        let selectedOrders = [];

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.order-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelection();
        }

        function updateSelection() {
            const checkboxes = document.querySelectorAll('.order-checkbox:checked');
            selectedOrders = Array.from(checkboxes).map(cb => cb.value);
            
            document.getElementById('selectedCount').textContent = selectedOrders.length;
            document.getElementById('selectedOrders').value = selectedOrders.join(',');
            
            const bulkActions = document.getElementById('bulkActions');
            if (selectedOrders.length > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }

        function clearSelection() {
            document.getElementById('selectAll').checked = false;
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
            updateSelection();
        }

        function toggleStatusMenu(orderId) {
            const menu = document.getElementById(`statusMenu-${orderId}`);
            const isShown = menu.classList.contains('show');
            
            // Close all menus
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                m.classList.remove('show');
            });
            
            // Toggle current menu
            if (!isShown) {
                menu.classList.add('show');
            }
        }

        // Close status menus when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    m.classList.remove('show');
                });
            }
        });
    </script>
</body>
</html>
@endsection