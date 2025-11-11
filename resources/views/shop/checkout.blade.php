<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="{{ asset('css/checkout.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/universal-style.css') }}" rel="stylesheet" />

    <title>Checkout Page</title>
</head>
<body>
    @include('layouts.checkout-header')
    <div class="bg-white min-h-screen">
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(empty($cart))
        <div class="max-w-2xl mx-auto px-4 py-16 text-center">
            <h2 class="text-2xl font-semibold text-slate-900 mb-4">Your cart is empty</h2>
            <p class="text-slate-600 mb-8">Add some products before proceeding to checkout.</p>
            <a href="{{ url('/home') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-md transition">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="flex max-md:flex-col gap-12 max-lg:gap-4 h-full max-w-[1400px] mx-auto px-8">
            <!-- Order Summary Sidebar - Sticky on Desktop -->
            <div class="md:h-screen md:sticky md:top-0 md:min-w-[370px] md:max-w-[420px]">
                <div class="relative h-full">
                    <div class="px-6 py-8 md:overflow-auto md:h-screen">
                        <!-- Order Summary Content with Background -->
                        <div class="bg-[#f4eedf] rounded-lg p-6 mt-4" style="color: #020202;">
                            <h2 class="text-xl font-semibold mb-6" style="color: #020202;">Order Summary</h2>
                            
                            <div class="space-y-4">
                                @php $subtotal = 0; @endphp
                                @foreach($cart as $id => $item)
                                    @php 
                                        $itemPrice = $item['discount_price'] ?? $item['price'];
                                        $itemTotal = $itemPrice * $item['quantity'];
                                        $subtotal += $itemTotal;
                                    @endphp
                                    <div class="flex items-start gap-4">
                                        <div class="w-24 h-24 flex p-3 shrink-0 bg-white rounded-md relative">
                                            <img src="/product/{{ $item['image'] }}" class="w-full object-contain" alt="{{ e($item['name']) }}" />
                                            <span class="absolute -top-2 -right-2 bg-slate-700 text-white text-xs font-semibold rounded-full w-6 h-6 flex items-center justify-center">
                                                {{ $item['quantity'] }}
                                            </span>
                                        </div>
                                        <div class="w-full">
                                            <h3 class="text-sm font-semibold" style="color: #020202;">{{ e($item['name']) }}</h3>
                                            @if(isset($item['size']) && $item['size'])
                                                <p class="text-xs mt-1" style="color: #020202; opacity: 0.7;">Size: {{ e($item['size']) }}</p>
                                            @endif
                                            <ul class="text-xs space-y-2 mt-3" style="color: #020202;">
                                                <li class="flex flex-wrap gap-4">
                                                    Quantity 
                                                    <span class="ml-auto">{{ $item['quantity'] }}</span>
                                                </li>
                                                <li class="flex flex-wrap gap-4">
                                                    Total Price 
                                                    <span class="ml-auto font-semibold">₱{{ number_format($itemTotal, 2) }}</span>
                                                </li>
                                                @if(isset($item['discount_price']) && $item['discount_price'] < $item['price'])
                                                    <li class="flex flex-wrap gap-4" style="opacity: 0.6;">
                                                        <span class="line-through">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-gray-400 my-8" style="opacity: 0.3;" />
                            
                            <div>
                                <ul class="font-medium space-y-4" style="color: #020202; opacity: 0.8;">
                                    <li class="flex flex-wrap gap-4 text-sm">
                                        Subtotal 
                                        <span class="ml-auto font-semibold" id="subtotal-amount" style="color: #020202; opacity: 1;">₱{{ number_format($subtotal, 2) }}</span>
                                    </li>
                                    <li class="flex flex-wrap gap-4 text-sm">
                                        Shipping 
                                        <span class="ml-auto font-semibold" id="shipping-amount" style="color: #020202; opacity: 1;">
                                            <span id="shipping-fee-display">Select delivery method</span>
                                        </span>
                                    </li>
                                    <hr class="border-gray-400" style="opacity: 0.3;" />
                                    <li class="flex flex-wrap gap-4 text-[15px] font-semibold" style="color: #020202;">
                                        Total 
                                        <span class="ml-auto" id="total-amount">₱{{ number_format($subtotal, 2) }}</span>
                                    </li>
                                </ul>

                                <div class="mt-8 flex flex-col gap-4">
                                    <button type="submit" form="checkout-form" class="btn-primary-color btn-md w-full" id="submit-btn">
                                        Complete Purchase
                                    </button>

                                    <a href="{{ route('view-cart') }}" class="btn-secondary-color btn-md w-full text-center">
                                        ← Back to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="max-w-4xl w-full h-max rounded-md px-4 py-8 max-md:-order-1">
                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-xl text-slate-900 font-semibold mb-6">Contact Information</h2>
                        <div class="grid lg:grid-cols-2 gap-y-6 gap-x-4">
                            <!-- Full Name -->
                            <div>
                                <label class="text-sm text-slate-900 font-medium block mb-2">Full Name</label>
                                <input type="text" 
                                    name="full_name" 
                                    value="{{ old('full_name', Auth::user()->name ?? '') }}" 
                                    required
                                    placeholder="Enter your full name"
                                    maxlength="40"
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                                @error('full_name')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="text-sm text-slate-900 font-medium block mb-2">Phone Number</label>

                                <input type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                                @error('phone')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="mt-12">
                        <h2 class="text-xl text-slate-900 font-semibold mb-6">Billing Address</h2>
                        <div class="grid lg:grid-cols-2 gap-y-6 gap-x-4">
                            <div class="lg:col-span-2">
                                <label class="text-sm text-slate-900 font-medium block mb-2">Country</label>
                                <select name="country" required
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600">
                                    <option value="Philippines" selected>Philippines</option>
                                </select>
                            </div>
                            
                            <!-- Province Dropdown -->
                            <div>
                                <label class="text-sm text-slate-900 font-medium block mb-2">Province</label>
                                <select name="province" id="province" required
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600">
                                    <option value="">Select Province</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->province }}" 
                                            data-price="{{ $province->price }}"
                                            {{ old('province') == $province->province ? 'selected' : '' }}>
                                            {{ $province->province }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-slate-500 mt-1" id="province-shipping-info">Select a province to see shipping rates</p>
                                @error('province')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="text-sm text-slate-900 font-medium block mb-2">City / Municipality</label>
                                <input type="text" name="city" value="{{ old('city') }}" required
                                    placeholder="e.g. Quezon City, Taguig"
                                    maxlength="255"
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                                @error('city')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm text-slate-900 font-medium block mb-2">Barangay</label>
                                <input type="text" name="barangay" value="{{ old('barangay') }}" required
                                    placeholder="e.g. Barangay 123"
                                    maxlength="255"
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                                @error('barangay')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm text-slate-900 font-medium block mb-2">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                                    placeholder="e.g. 1100"
                                    maxlength="10"
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                                @error('postal_code')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="lg:col-span-2">
                                <label class="text-sm text-slate-900 font-medium block mb-2">Street Address</label>
                                <input type="text" name="billing_address" value="{{ old('billing_address') }}" required
                                    placeholder="House number, street name, building"
                                    maxlength="255"
                                    class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                                @error('billing_address')
                                    <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Options -->
                    <div class="mt-12">
                        <h2 class="text-xl text-slate-900 font-semibold mb-6">Shipping Options</h2>
                        <div class="mb-6">
                            <label class="text-sm text-slate-900 font-medium block mb-2">Delivery Method</label>
                            <select name="delivery_option" id="delivery_option" required
                                class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600">
                                <option value="">Select delivery option</option>
                                <option value="ship" {{ old('delivery_option') == 'ship' ? 'selected' : '' }}>Ship to Address</option>
                                <option value="pickup" {{ old('delivery_option') == 'pickup' ? 'selected' : '' }}>Store Pickup (Free)</option>
                            </select>
                            @error('delivery_option')
                                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md border border-gray-300">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="same_as_billing" name="same_as_billing" 
                                    {{ old('same_as_billing', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" />
                                <span class="ml-3 text-sm font-medium text-slate-900">Shipping address same as billing address</span>
                            </label>
                        </div>

                        <div id="shipping_address_group" class="mt-6" style="display: {{ old('same_as_billing', true) ? 'none' : 'block' }};">
                            <label class="text-sm text-slate-900 font-medium block mb-2">Complete Shipping Address</label>
                            <input type="text" id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}"
                                placeholder="Enter complete shipping address if different"
                                maxlength="255"
                                class="px-4 py-2.5 bg-white border border-gray-400 text-slate-900 w-full text-sm rounded-md focus:outline-blue-600 focus:border-blue-600" />
                            @error('shipping_address')
                                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-12">
                        <h2 class="text-xl text-slate-900 font-semibold mb-6">Payment Method</h2>
                        @error('payment_method')
                            <div class="text-red-600 text-sm mb-4 p-3 bg-red-50 rounded-md">{{ $message }}</div>
                        @enderror
                        
                        <div class="grid gap-4 lg:grid-cols-2">
                            <div class="payment-option bg-gray-100 p-4 rounded-md border-2 border-gray-300 hover:border-blue-600 transition cursor-pointer {{ old('payment_method') == 'paypal' ? 'border-blue-600 bg-blue-50' : '' }}">
                                <div class="flex items-center">
                                    <input type="radio" name="payment_method" value="paypal" id="paypal" 
                                        {{ old('payment_method') == 'paypal' ? 'checked' : '' }} 
                                        required
                                        class="w-5 h-5 cursor-pointer text-blue-600 focus:ring-blue-500" />
                                    <label for="paypal" class="ml-4 flex gap-2 cursor-pointer">
                                        <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" class="h-6" alt="PayPal" />
                                        <span class="font-semibold text-slate-900">PayPal</span>
                                    </label>
                                </div>
                                <p class="mt-4 text-sm text-slate-600 font-medium ml-9">Pay securely with your PayPal account</p>
                            </div>

                            <div class="payment-option bg-gray-100 p-4 rounded-md border-2 border-gray-300 hover:border-blue-600 transition cursor-pointer {{ old('payment_method') == 'paymongo' ? 'border-blue-600 bg-blue-50' : '' }}">
                                <div class="flex items-center">
                                    <input type="radio" name="payment_method" value="paymongo" id="paymongo" 
                                        {{ old('payment_method') == 'paymongo' ? 'checked' : '' }}
                                        class="w-5 h-5 cursor-pointer text-blue-600 focus:ring-blue-500" />
                                    <label for="paymongo" class="ml-4 flex gap-2 cursor-pointer">
                                        <span class="inline-flex gap-2">
                                            <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold">GCash</span>
                                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">Maya</span>
                                        </span>
                                    </label>
                                </div>
                                <p class="mt-4 text-sm text-slate-600 font-medium ml-9">Pay with GCash or Maya via Paymongo</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
<script>
    window.checkoutData = {
        subtotal: @json($subtotal),
        calculateShippingUrl: @json(route('calculate.shipping')),
        csrfToken: @json(csrf_token()),
    };
</script>

<script src="{{ asset('script/checkout.js') }}"></script>
</body>
</html>