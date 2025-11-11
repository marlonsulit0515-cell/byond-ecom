<header class="w-full bg-black shadow-sm px-6 py-4">
    <div class="flex items-center justify-between max-w-7xl mx-auto">
        <!-- Logo -->
        <div class="flex-shrink-0">
            <a href="{{ route('home') }}" class="block w-36 sm:w-44">
                <img src="{{ asset('img/logo/Byond.Co_Primary_Logo_White.webp') }}" alt="Byond Logo" class="w-full h-auto">
            </a>
        </div>

        <!-- Cart Icon -->
       <ul class="flex items-center space-x-4">
            <li class="relative">
                <a href="{{ url('view-cart') }}" class="relative flex items-center">
                    <img src="{{ asset('img/icons/Cart_icon.png') }}" alt="Cart" class="w-6 h-6 sm:w-7 sm:h-7">
                    @php
                        $cart = session()->get('cart', []);
                        $cartCount = array_sum(array_column($cart, 'quantity'));
                    @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-white text-black text-xs font-semibold rounded-full px-1.5 py-0.5 border border-gray-300">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</header>
