@extends('layouts.dashboard')

@section('maincontent')
<title>Invoice - {{ $order->order_number }}</title>
<body class="invoice-page">
    <div class="invoice-container">
        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Invoice Card -->
        <div class="invoice-card">
            <!-- Header -->
            <div class="invoice-header">
                <div class="invoice-header-info">
                    <h1>Order {{ $order->order_number }}</h1>
                    <p class="order-date">
                        Order Placed Date {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                    @if($order->tracking_number)
                        <p class="tracking-info">
                            <i class="fas fa-truck"></i> Order Tracking Number: {{ $order->tracking_number }}
                        </p>
                    @endif
                </div>
                <div>
                    <span class="status-badge 
                        @if($order->status == 'pending') status-pending
                        @elseif($order->status == 'processing') status-processing
                        @elseif($order->status == 'shipped') status-shipped
                        @elseif($order->status == 'completed') status-completed
                        @elseif($order->status == 'cancelled') status-cancelled
                        @elseif($order->status == 'cancellation_requested') status-cancellation-requested
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </div>

            <!-- Cancellation Information -->
            @if($order->status == 'cancelled' || $order->status == 'cancellation_requested')
                <div class="section cancellation-section">
                    <h2 class="section-title cancellation-title">
                        <i class="fas fa-times-circle"></i>
                        Cancellation Information
                    </h2>
                    <div class="cancellation-details">
                        @if($order->cancellation_reason)
                            <div class="cancellation-item">
                                <span>Reason:</span>
                                <p>{{ $order->cancellation_reason }}</p>
                            </div>
                        @endif

                        @if($order->cancellation_comments)
                            <div class="cancellation-item">
                                <span>Comments:</span>
                                <p>{{ $order->cancellation_comments }}</p>
                            </div>
                        @endif

                        @if($order->cancellation_data)
                            @php
                                $cancellationData = json_decode($order->cancellation_data, true);
                            @endphp
                            
                            @if(isset($cancellationData['requested_by']))
                                <div class="cancellation-item">
                                    <span>Requested By:</span>
                                    <p>{{ $cancellationData['requested_by'] }}</p>
                                </div>
                            @endif
                            
                            @if(isset($cancellationData['requested_at']))
                                <div class="cancellation-item">
                                    <span>Requested At:</span>
                                    <p>{{ \Carbon\Carbon::parse($cancellationData['requested_at'])->format('F j, Y g:i A') }}</p>
                                </div>
                            @endif
                            
                            @if(isset($cancellationData['cancelled_at']))
                                <div class="cancellation-item">
                                    <span>Cancelled At:</span>
                                    <p>{{ \Carbon\Carbon::parse($cancellationData['cancelled_at'])->format('F j, Y g:i A') }}</p>
                                </div>
                            @endif
                        @endif

                        @if(!$order->cancellation_reason && !$order->cancellation_comments && !$order->cancellation_data)
                            <p class="no-data-message">No cancellation details available.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Update Order Status -->
            <div class="section">
                <h2 class="section-title">Update Order Status</h2>
                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" id="statusUpdateForm">
                    @csrf
                    @method('PATCH')

                    <div class="status-form">
                        <div class="status-select-wrapper">
                            <select name="status" id="statusSelect" class="status-select">
                                @php
                                    $currentStatus = $order->status;
                                    $availableStatuses = [];
                                    
                                    switch($currentStatus) {
                                        case 'pending':
                                            $availableStatuses = ['pending', 'processing', 'cancelled'];
                                            break;
                                        case 'processing':
                                            $availableStatuses = ['processing', 'shipped', 'cancelled'];
                                            break;
                                        case 'shipped':
                                            $availableStatuses = ['shipped', 'completed'];
                                            break;
                                        case 'completed':
                                            $availableStatuses = ['completed'];
                                            break;
                                        case 'cancelled':
                                            $availableStatuses = ['cancelled', 'pending'];
                                            break;
                                        case 'cancellation_requested':
                                            $availableStatuses = ['cancellation_requested', 'cancelled', 'processing'];
                                            break;
                                        default:
                                            $availableStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
                                    }
                                @endphp

                                @foreach($availableStatuses as $status)
                                    <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn-primary-color btn-md">
                            Update Status
                        </button>
                    </div>

                    <!-- Tracking Number Field -->
                    <div id="trackingNumberField" class="tracking-field hidden">
                        <label for="tracking_number" class="tracking-label">
                            Tracking Number <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="tracking_number" 
                            id="tracking_number"
                            value="{{ old('tracking_number', $order->tracking_number) }}"
                            placeholder="Enter tracking number (e.g., 1234567890)"
                            class="tracking-input"
                        >
                        <p class="tracking-hint">Required when marking order as shipped</p>
                    </div>

                    @error('status')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    @error('tracking_number')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </form>
            </div>

            <!-- Status History -->
            <div class="section">
                <h2 class="section-title">Order Status History</h2>
                <div class="status-history">
                    @forelse($order->statusLogs->sortByDesc('changed_at') as $log)
                        <div class="status-history-item">
                            <div class="status-indicator {{ $log->status }}"></div>
                            <div class="status-history-content">
                                <p>{{ ucfirst(str_replace('_', ' ', $log->status)) }}</p>
                                <p>{{ $log->changed_at->format('F j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="no-data-message">No status history available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="section">
                <div class="info-grid">
                    <div class="info-block">
                        <h2>Customer Information</h2>
                        <p class="info-name">Full Name: <span class="info-detail">{{ $order->full_name }}</span></p>
                        <p class="info-name">Email: <span class="info-detail">{{ $order->email }}</span></p>
                        <p class="info-name">Contact Number: <span class="info-detail">{{ $order->phone }}</span></p>
                    </div>
                    
                    <div class="info-block">
                        <h2>Shipping Address</h2>
                        <p class="info-name">{{ $order->postal_code }},{{ $order->billing_address }}, {{ $order->barangay}},  {{ $order->city }}, {{ $order->province }}, {{ $order->country ?? 'Philippines' }}</p>
                    </div>
                    
                    <div class="info-block payment-info">
                        <h3>Payment</h3>
                        @if($order->payment)
                            <div class="payment-details">
                                <p><span>Payer Name:</span> {{ ucfirst($order->name ?? $order->user->name ?? 'N/A') }}</p>
                                <p><span>Method:</span> {{ ucfirst($order->payment->method) }}</p>
                                <p><span>Status:</span> {{ ucfirst($order->payment->status) }}</p>
                                <p><span>Date & Time:</span> {{ $order->payment->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                        @else
                            <p class="no-data-message">No payment record yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="section">
                <h2 class="section-title">Order Summary</h2>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr class="admin-table">
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('product/' . $item->product->image) }}" 
                                                     class="product-image" 
                                                     alt="{{ $item->product_name }}">
                                            @else
                                                <div class="product-placeholder">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <div class="product-info">
                                                <p class="product-name">{{ $item->product_name }}</p>
                                                @if($item->size)
                                                    <p class="product-size">Size: {{ strtoupper($item->size) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="text-right font-medium">₱{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Order Totals -->
                @php
                    $subtotal = $order->items->sum('total');
                    $shippingFee = $order->shipping_fee ?? 0;
                @endphp
                <div class="order-totals">
                    <div class="totals-box">
                        <div class="totals-row">
                            <span class="label">Subtotal:</span>
                            <span class="value">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="totals-row">
                            <span class="label">Shipping:</span>
                            <span class="value">
                                @if($shippingFee > 0)
                                    ₱{{ number_format($shippingFee, 2) }}
                                @else
                                    <span class="free-shipping">FREE</span>
                                @endif
                            </span>
                        </div>
                        <div class="totals-row totals-total">
                            <span>Total:</span>
                            <span>₱{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('statusSelect');
            const trackingNumberField = document.getElementById('trackingNumberField');
            const trackingNumberInput = document.getElementById('tracking_number');

            function toggleTrackingField() {
                if (statusSelect.value === 'shipped') {
                    trackingNumberField.classList.remove('hidden');
                    trackingNumberInput.setAttribute('required', 'required');
                } else {
                    trackingNumberField.classList.add('hidden');
                    trackingNumberInput.removeAttribute('required');
                }
            }

            toggleTrackingField();
            statusSelect.addEventListener('change', toggleTrackingField);
        });
    </script>
</body>
@endsection