@extends('layouts.dashboard')

@section('maincontent')
<title>Invoice - {{ $order->order_number }}</title>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 sm:px-6 py-8 sm:py-10">

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Invoice Card -->
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b p-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 break-words">
                        Order {{ $order->order_number }}
                    </h1>
                    <p class="text-gray-400 text-sm">
                        Order Placed Date {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                    @if($order->tracking_number)
                        <p class="text-indigo-600 text-sm font-medium mt-1">
                            <i class="fas fa-truck"></i> Order Tracking Number: {{ $order->tracking_number }}
                        </p>
                    @endif
                </div>
                <div class="text-left sm:text-right">
                    <span class="px-3 sm:px-4 py-1 sm:py-2 text-xs sm:text-sm font-semibold rounded-full
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                        @elseif($order->status == 'completed') bg-green-100 text-green-800
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                        @elseif($order->status == 'cancellation_requested') bg-orange-100 text-orange-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </div>

            <!-- Update Order Status -->
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Order Status</h2>

                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="space-y-4" id="statusUpdateForm">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                        <div class="flex-1">
                            <select name="status" id="statusSelect" class="w-full border-gray-300 rounded-lg p-2 text-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                                @php
                                    // Define status flow logic
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

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Update Status
                        </button>
                    </div>

                    <!-- Tracking Number Input (shows when 'shipped' is selected) -->
                    <div id="trackingNumberField" class="hidden">
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Tracking Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="tracking_number" 
                            id="tracking_number"
                            value="{{ old('tracking_number', $order->tracking_number) }}"
                            placeholder="Enter tracking number (e.g., 1234567890)"
                            class="w-full border-gray-300 rounded-lg p-2 text-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400"
                        >
                        <p class="text-xs text-gray-500 mt-1">Required when marking order as shipped</p>
                    </div>

                    @error('status')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                    @error('tracking_number')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </form>
            </div>

            <!-- Status History -->
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Status History</h2>
                <div class="space-y-4">
                    @forelse($order->statusLogs->sortByDesc('changed_at') as $log)
                        <div class="flex items-start space-x-3">
                            <div class="w-3 h-3 rounded-full mt-1
                                @if($log->status == 'pending') bg-yellow-500
                                @elseif($log->status == 'processing') bg-blue-500
                                @elseif($log->status == 'shipped') bg-purple-500
                                @elseif($log->status == 'completed') bg-green-500
                                @elseif($log->status == 'cancelled') bg-red-500
                                @elseif($log->status == 'cancellation_requested') bg-orange-500
                                @else bg-gray-400
                                @endif">
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->status)) }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $log->changed_at->format('F j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 text-sm">No status history available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 border-b">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Customer Information</h2>
                    <p class="text-gray-700 text-sm sm:text-base">{{ $order->full_name }}</p>
                    <p class="text-gray-600 text-xs sm:text-sm">{{ $order->email }}</p>
                    <p class="text-gray-600 text-xs sm:text-sm">{{ $order->phone }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Shipping Address</h2>
                    <p class="text-gray-700 text-sm sm:text-base">{{ $order->billing_address }}</p>
                    <p class="text-gray-600 text-xs sm:text-sm">
                        {{ $order->city }}, {{ $order->province }}, {{ $order->country ?? 'Philippines' }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Payment</h3>
                    @if($order->payment)
                        <div class="text-gray-700 text-sm sm:text-base space-y-1">
                            <p><span class="font-medium">Payer Name:</span> {{ ucfirst($order->name ?? $order->user->name ?? 'N/A') }}</p>
                            <p><span class="font-medium">Method:</span> {{ ucfirst($order->payment->method) }}</p>
                            <p><span class="font-medium">Status:</span> {{ ucfirst($order->payment->status) }}</p>
                            <p><span class="font-medium">Date & Time:</span> {{ $order->payment->created_at->format('F j, Y g:i A') }}</p>
                        </div>
                    @else
                        <p class="text-gray-600 text-sm">No payment record yet.</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm sm:text-base border border-gray-200 rounded-lg min-w-[600px] sm:min-w-0">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-600 font-medium">Product</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-medium">Quantity</th>
                                <th class="px-4 py-3 text-right text-gray-600 font-medium">Price</th>
                                <th class="px-4 py-3 text-right text-gray-600 font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('product/' . $item->product->image) }}" 
                                                     class="w-10 h-10 sm:w-12 sm:h-12 object-cover rounded-md border" 
                                                     alt="{{ $item->product_name }}">
                                            @else
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                                @if($item->size)
                                                    <p class="text-xs text-gray-500">Size: {{ strtoupper($item->size) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">₱{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                @php
                    $subtotal = $order->items->sum('total');
                    $shippingFee = $order->shipping_fee ?? 0;
                @endphp
                <div class="mt-6 flex flex-col sm:flex-row sm:justify-end">
                    <div class="w-full sm:w-2/3 md:w-1/3 space-y-2 text-sm sm:text-base">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="text-gray-900">
                                @if($shippingFee > 0)
                                    ₱{{ number_format($shippingFee, 2) }}
                                @else
                                    <span class="text-green-600">FREE</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg border-t pt-2">
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

            // Check on page load (for validation errors)
            toggleTrackingField();

            // Listen for changes
            statusSelect.addEventListener('change', toggleTrackingField);
        });
    </script>
</body>
@endsection