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
        <div class="flex gap-2 border-b border-gray-300 pb-4 mb-6">
            <h2 class="text-xl font-semibold text-slate-900 flex-1">Shopping Cart</h2>
            <h4 class="text-base text-slate-900 font-medium">{{ count($cart) }} {{ count($cart) === 1 ? 'Item' : 'Items' }}</h4>
        </div>

        <div class="grid lg:grid-cols-3 gap-10">
            <!-- Cart Items Section -->
            <div class="lg:col-span-2 bg-white divide-y divide-gray-300">
                @php $total = 0; @endphp

                @foreach($cart as $id => $details)
                    @php
                        $subtotal = $details['price'] * $details['quantity'];
                        $total += $subtotal;

                        $product = \App\Models\Product::find($details['product_id'] ?? $id);
                        $sizeField = 'stock_' . strtolower($details['size'] ?? 'm');
                        $currentStock = $product ? ($product->$sizeField ?? 0) : 0;
                    @endphp

                    <div class="flex sm:items-center max-sm:flex-col gap-6 py-6 cart-row" data-id="{{ $id }}">
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
                                </div>

                                <div class="mt-4 flex flex-wrap gap-4">
                                    <button type="button" class="remove-from-cart font-medium text-black-500 text-sm flex items-center gap-2 cursor-pointer">
                                        <img src="{{ asset('img/icons/delete.svg') }}" alt="delete icon">
                                    </button>
                                </div>
                            </div>

                            <div class="ml-auto text-right">
                                <div class="flex gap-2 items-center border border-gray-300 px-3 py-2 w-max rounded-full mb-4">
                                    <button type="button" class="quantity-decrease cursor-pointer" data-id="{{ $id }}">-</button>
                                    <input type="number" 
                                           value="{{ $details['quantity'] }}" 
                                           min="1" 
                                           max="{{ $currentStock }}"
                                           class="quantity-input w-12 text-center border-0 outline-none bg-transparent" 
                                           readonly />
                                    <button type="button" class="quantity-increase cursor-pointer" data-id="{{ $id }}" data-max="{{ $currentStock }}">+</button>
                                </div>
                                <div class="mt-2">
                                    <h4 class="text-base font-semibold text-slate-900 subtotal">₱{{ number_format($details['price'] * $details['quantity'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary Section -->
            <div class="border border-gray-200 rounded-xl shadow-sm p-6 lg:sticky lg:top-6 bg-white h-max w-full max-w-sm mx-auto">
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

                @if(auth()->check())
                    <a href="{{ route('checkout_page') }}" 
                    class="mt-6 text-sm font-semibold px-5 py-3 w-full rounded-md bg-[#020202] text-white text-center transition-all duration-300 hover:bg-[#762c21] hover:text-[#f4eedf] block">
                        Proceed to Checkout
                    </a>
                @else
                    <button type="button" 
                            onclick="showAuthModal()" 
                            class="mt-6 text-sm font-semibold px-5 py-3 w-full rounded-md bg-[#020202] text-white transition-all duration-300 hover:bg-[#762c21] hover:text-[#f4eedf]">
                        Proceed to Checkout
                    </button>
                @endif

                <a href="{{ route('shop-page') }}" 
                class="mt-3 text-sm font-medium px-5 py-3 w-full rounded-md bg-gray-100 hover:bg-gray-200 text-slate-900 text-center transition block">
                    Continue Shopping
                </a>
            </div>

        @guest
            <x-auth-modal />
        @endguest
    @else
        <div class="flex flex-col items-center justify-center py-16 px-4">
            <h2 class="text-2xl font-semibold text-slate-900 mb-2">Your cart is empty</h2>
            <img src="{{ asset('img/icons/image for empty content.png') }}" class="mb-6" alt="">
            <a href="{{ route('shop-page') }}" class="px-6 py-3 bg-[#020202] text-white text-center transition-all duration-300 hover:bg-[#762c21] hover:text-[#f4eedf] text-white font-medium rounded-md transition">Start Shopping</a>
        </div>
    @endif
</div>

<script>
const updateCartTotals = (cartData) => {
    let total = 0;
    cartData.forEach(item => total += item.price * item.quantity);
    document.getElementById('cart-total').textContent = '₱' + total.toFixed(2);
    document.getElementById('cart-total-bottom').textContent = '₱' + total.toFixed(2);
};

document.querySelectorAll('.quantity-increase, .quantity-decrease').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('.cart-row');
        const input = row.querySelector('.quantity-input');
        const id = this.dataset.id;
        const max = parseInt(this.dataset.max || input.max);
        let newQty = parseInt(input.value);

        if (this.classList.contains('quantity-increase') && newQty < max) newQty++;
        if (this.classList.contains('quantity-decrease') && newQty > 1) newQty--;

        if (newQty !== parseInt(input.value)) {
            input.value = newQty;

            fetch('{{ route("update-cart") }}', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ id, quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optionally update subtotal for this row
                    const price = parseFloat(row.querySelector('.subtotal').textContent.replace('₱','')) / parseInt(input.value);
                    row.querySelector('.subtotal').textContent = '₱' + (price * newQty).toFixed(2);
                    // Reload page for flash messages
                    window.location.reload();
                }
            });
        }
    });
});

document.querySelectorAll('.remove-from-cart').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('.cart-row');
        const id = row.dataset.id;

        if (!confirm('Are you sure you want to remove this item?')) return;

        fetch('{{ route("remove-from-cart") }}', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.reload();
        });
    });
});

// Auth modal functions
function showAuthModal() { document.getElementById('authModal').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
function closeAuthModal() { document.getElementById('authModal').style.display = 'none'; document.body.style.overflow = 'auto'; }
window.showAuthModal = showAuthModal;
window.closeAuthModal = closeAuthModal;
</script>
@endsection
