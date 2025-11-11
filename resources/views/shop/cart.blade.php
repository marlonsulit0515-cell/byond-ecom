@extends('layouts.default')

@section('maincontent')
<div class="py-4 px-4 sm:px-10 max-w-7xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(!empty($cart))
        <div class="flex gap-2 border-b border-gray-300 pb-4 mb-6 lg:mt-12 lg:mb-16">
            <h2 class="text-xl font-semibold text-slate-900 flex-1">Shopping Cart</h2>
            <h4 class="text-base text-slate-900 font-medium" id="cart-items-count">{{ count($cart) }} {{ count($cart) === 1 ? 'Item' : 'Items' }}</h4>
        </div>

        <div class="grid lg:grid-cols-3 gap-10">
            <!-- Cart Items Section -->
            <div class="lg:col-span-2 bg-white divide-y divide-gray-300">
                @php $total = 0; @endphp

                @foreach($cart as $id => $details)
                    @php
                        $product = \App\Models\Product::find($details['product_id'] ?? $id);
                        
                        // Get current price from database
                        $currentPrice = $product 
                            ? (($product->discount_price && $product->discount_price > 0) 
                                ? $product->discount_price 
                                : $product->price)
                            : $details['price'];
                        
                        // Update cart price if it differs
                        if ($currentPrice != $details['price']) {
                            $cart[$id]['price'] = $currentPrice;
                        }
                        
                        $subtotal = $currentPrice * $details['quantity'];
                        $total += $subtotal;

                        $sizeField = 'stock_' . strtolower($details['size'] ?? 'm');
                        $currentStock = $product ? ($product->$sizeField ?? 0) : 0;
                    @endphp

                    <div class="flex sm:items-center max-sm:flex-col gap-6 py-6 cart-row" 
                         data-id="{{ $id }}" 
                         data-price="{{ $currentPrice }}"
                         data-max-stock="{{ $currentStock }}">
                        <div class="w-32 h-32 shrink-0">
                            <img src="/product/{{ $details['image'] }}" 
                                 alt="{{ $details['name'] }}" 
                                 class="w-full h-full object-contain" />
                        </div>

                        <div class="flex items-start gap-4 w-full">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-slate-900 mb-2">{{ $details['name'] }}</h3>
                                <div class="space-y-2">
                                    <h6 class="text-sm text-slate-900">
                                        Size: <span class="ml-2 font-medium">{{ $details['size'] ?? 'M' }}</span>
                                    </h6>
                                    <h6 class="text-sm text-slate-900">
                                        Price: <span class="ml-2 font-medium">₱{{ number_format($currentPrice, 2) }}</span>
                                    </h6>
                                    @if($currentStock < 5 && $currentStock > 0)
                                        <p class="text-xs text-orange-600">Only {{ $currentStock }} left in stock</p>
                                    @elseif($currentStock === 0)
                                        <p class="text-xs text-red-600">Out of stock</p>
                                    @endif
                                </div>

                                <div class="mt-4 flex flex-wrap gap-4">
                                    <button type="button" class="remove-from-cart font-medium text-black-500 text-sm flex items-center gap-2 cursor-pointer">
                                        <img src="{{ asset('img/icons/delete.svg') }}" alt="delete icon">
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <div class="ml-auto text-right">
                                <div class="flex gap-2 items-center border border-gray-300 px-3 py-2 w-max rounded-full mb-4">
                                    <button type="button" 
                                            class="quantity-decrease cursor-pointer w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded"
                                            {{ $details['quantity'] <= 1 ? 'disabled' : '' }}>
                                        -
                                    </button>
                                    <input type="number" 
                                           value="{{ $details['quantity'] }}" 
                                           min="1" 
                                           max="{{ $currentStock }}"
                                           class="quantity-input w-12 text-center border-0 outline-none bg-transparent" 
                                           readonly />
                                    <button type="button" 
                                            class="quantity-increase cursor-pointer w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded"
                                            {{ $details['quantity'] >= $currentStock ? 'disabled' : '' }}>
                                        +
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <h4 class="text-base font-semibold text-slate-900 subtotal">₱{{ number_format($subtotal, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary Section -->
            <div class="border border-gray-200 rounded-xl shadow-sm p-6 lg:sticky lg:top-6 bg-white h-max w-full max-w-sm mx-auto mb-8 lg:mb-16">
                <h3 class="text-lg font-semibold text-slate-900 border-b border-gray-300 pb-4 text-center">
                    Order Summary
                </h3>

                <ul class="text-slate-600 font-medium divide-y divide-gray-200 mt-4">
                    <li class="flex justify-between items-center text-sm py-3">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-900" id="cart-total">₱{{ number_format($total, 2) }}</span>
                    </li>
                    <li class="flex justify-between items-center text-sm py-3">
                        <span>Shipping</span>
                        <span class="font-semibold text-slate-900">Calculated at checkout</span>
                    </li>
                    <li class="flex justify-between items-center text-sm py-3 font-semibold text-slate-900 text-base">
                        <span>Total</span>
                        <span id="cart-total-bottom">₱{{ number_format($total, 2) }}</span>
                    </li>
                </ul>

                <div class="flex flex-col gap-3 mt-6">
                    @if(auth()->check())
                        <a href="{{ route('checkout_page') }}" 
                        class="btn-primary-color btn-lg text-center">
                            Proceed to Checkout
                        </a>
                    @else
                        <button type="button" 
                                onclick="showAuthModal()" 
                                class="btn-primary-color btn-lg">
                            Proceed to Checkout
                        </button>
                    @endif
                    <a href="{{ route('shop-page') }}" 
                    class="btn-secondary-color btn-md w-full py-3 text-center">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        @guest
            <x-auth-modal />
        @endguest
    @else
        {{-- Empty Cart State with Fixed Dimensions --}}
        <div class="flex flex-col items-center justify-center min-h-[60vh] transition-opacity duration-300 opacity-100" id="empty-cart-container">
            <h2 class="text-2xl font-semibold text-slate-900 mb-2">Your cart is empty</h2>
            
            <div class="flex flex-col items-center justify-center p-6 text-center text-gray-500 bg-white">
                <!-- Reserve space to prevent layout shift -->
                <div class="relative w-[220px] h-[250px] md:w-[450px] md:h-[400px] overflow-hidden rounded-lg">
                    <img 
                        class="absolute inset-0 w-full h-full object-contain" 
                        src="{{ asset('img/logo/statement-Byond-Black.webp') }}" 
                        alt="Empty Content Logo"
                        width="450"
                        height="505"
                        loading="lazy"
                    >
                </div>
            </div>
            
           
        </div>
            <div style="display: flex; justify-content: center;">
                <a href="{{ route('shop-page') }}" class="btn-primary-color btn-lg mt">
                    Start Shopping
                </a>
            </div>
    @endif
</div>
@endsection