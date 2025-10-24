<div class="headMain">
    <ul class="socmedContainer">
        <li>
            <a href="https://www.facebook.com/profile.php?id=61571159256828" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('img/icons/FB_Icon.png') }}" alt="Facebook">
            </a>
        </li>
        <li>
            <a href="https://www.instagram.com/byondco.official/" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('img/icons/IG_Icon.png') }}" alt="Instagram">
            </a>
        </li>
        <li>
            <a href="https://www.tiktok.com/@byondcoph" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('img/icons/Tiktok_Icon.png') }}" alt="TikTok">
            </a>
        </li>
    </ul>

    <ul class="utilityContainer">
        <li class="search-box">
            <img src="{{ asset('img/icons/Search_icon.png') }}" alt="Search" id="search-icon">
        </li>
        @guest
            <li>
                <a href="{{ route('login') }}">
                    <img src="{{ asset('img/icons/Profile_Icon.png') }}" alt="">
                </a>
            </li>
        @else
            <li class="profile-link">
                <a href="{{ Auth::user()->usertype === 'admin' ? route('admin.dashboard') : route('user.orders') }}">
                    <img src="{{ asset('img/icons/Profile_Icon.png') }}" alt="">
                </a>
                <div class="profile-tooltip">
                    <strong>{{ Auth::user()->name }}</strong><br>
                    <span>{{ Auth::user()->usertype === 'admin' ? 'Admin' : 'User' }}</span>
                </div>
            </li>
        @endguest
        
        <li>
            <a href="{{ url('view-cart') }}" class="cart-icon">
                <img src="{{ asset('img/icons/Cart_icon.png') }}" alt="Cart">
                @php
                    $cart = session()->get('cart', []);
                    $cartCount = array_sum(array_column($cart, 'quantity'));
                @endphp
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</div>

<script src="{{ asset('script/header.js') }}"></script>