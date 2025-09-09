@extends('layouts.dashboard')

@section('maincontent')
<title>Invoice - {{ $order->order_number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        
        <!-- Invoice Card -->
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            
            <!-- Header -->
        <div class="flex justify-between items-center border-b p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
                <p class="text-gray-400 text-sm">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 text-sm font-semibold rounded-full 
                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                    @elseif($order->status == 'cancellation_requested') bg-orange-100 text-orange-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <!-- Status History -->
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Status History</h2>
            <div class="space-y-4">
                @foreach($order->statusLogs->sortByDesc('changed_at') as $log)
                    <div class="flex items-start space-x-3">
                        <div class="w-3 h-3 rounded-full 
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
                            <p class="font-medium text-gray-900">{{ ucfirst($log->status) }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $log->changed_at->format('F j, Y g:i A') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 border-b">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Customer Information</h2>
                    <p class="text-gray-700">{{ $order->full_name }}</p>
                    <p class="text-gray-600 text-sm">{{ $order->email }}</p>
                    <p class="text-gray-600 text-sm">{{ $order->phone }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Shipping Address</h2>
                    <p class="text-gray-700">{{ $order->billing_address }}</p>
                    <p class="text-gray-600 text-sm">{{ $order->city }}, {{ $order->province }}, {{ $order->country ?? 'Philippines' }}</p>
                </div>
                <div>
                    <h3>Payment</h3>
                    @if($order->payment)
                        <p>Payer Name: {{ ucfirst($order->name ?? $order->user->name) }}</p>
                        <p>Method: {{ ucfirst($order->payment->method) }}</p>
                        <p>Status: {{ ucfirst($order->payment->status) }}</p>
                        <p>Payment Date and Time: {{ $order->payment->created_at->format('F j, Y g:i A') }}</p>
                    @else
                        <p>No payment record yet.</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
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
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('product/' . $item->product->image) }}" 
                                                 class="w-12 h-12 object-cover rounded-md border" 
                                                 alt="{{ $item->product_name }}">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
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
                <div class="mt-6 flex justify-end">
                    <div class="w-full md:w-1/3 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">₱{{ number_format($order->subtotal ?? $order->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="text-gray-900">₱{{ number_format($order->shipping_fee ?? 0, 2) }}</span>
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
</body>
@endsection
