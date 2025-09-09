@extends('layouts.default')
<link href="{{ asset('css/reciept.css') }}" rel="stylesheet" />

@section('maincontent')
<div class="receipt-container">
    <div class="order-success">

        <!-- Success Icon -->
        <div class="success-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22,4 12,14.01 9,11.01"></polyline>
            </svg>
        </div>

        <!-- Headings -->
        <h1>Order Placed Successfully!</h1>
        <p class="subtitle">
            Thank you for your purchase. Your order has been received and is being processed.
        </p>

        @if(isset($order))
            <!-- Shipping Information -->
            <div class="order-box">
                <h3>Shipping Information</h3>
                <div class="order-row">
                    <span>Name:</span>
                    <span>{{ $order->full_name ?? $order->user->full_name }}</span>
                </div>
                <div class="order-row">
                    <span>Country:</span>
                    <span>{{ $order->country }}</span>
                </div>
                <div class="order-row">
                    <span>Province:</span>
                    <span>{{ $order->province }}</span>
                </div>
                <div class="order-row">
                    <span>City / Municipality:</span>
                    <span>{{ $order->city }}</span>
                </div>
                <div class="order-row">
                    <span>Barangay:</span>
                    <span>{{ $order->barangay }}</span>
                </div>
                <div class="order-row">
                    <span>Postal Code:</span>
                    <span>{{ $order->postal_code }}</span>
                </div>
                <div class="order-row">
                    <span>Street Address:</span>
                    <span>{{ $order->billing_address }}</span>
                </div>
            </div>

            <!-- Order Details -->
            <div class="order-box">
                <h3>Order Details</h3>
                <div class="order-row">
                    <span>Order Number:</span>
                    <span>{{ $order->order_number }}</span>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-box">
                <h3>Order Summary</h3>
                <ul>
                    @foreach($order->items as $item)
                        <li>
                            {{ $item->product_name }} (x{{ $item->quantity }})
                            <span>₱{{ number_format($item->total, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="order-row total">
                    <span>Total:</span>
                    <span>₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="order-box">
                <h3>Payment</h3>
                @if($order->payment)
                    <p><strong>Payer Name:</strong> {{ ucfirst($order->name ?? $order->user->name) }}</p>
                    <p><strong>Method:</strong> {{ ucfirst($order->payment->method) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($order->payment->status) }}</p>
                    
                @else
                    <p>No payment record yet.</p>
                @endif
            </div>
        @endif

        <!-- Actions -->
        <div class="actions">
            <a href="{{ route('shop.more') }}" class="btn primary">Continue Shopping</a>
            <a href="{{ route('orders.dashboard') }}" class="btn secondary">View Orders</a>
        </div>
    </div>
</div>
@endsection
