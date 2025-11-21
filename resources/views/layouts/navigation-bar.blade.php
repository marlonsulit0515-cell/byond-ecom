<nav class="menuMain">
    <!-- Hamburger Menu Toggle (Mobile) - Now on the left -->
    <button class="menu-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Logo - Centered on mobile -->
    <a href="{{ route('home') }}" aria-label="Go to homepage">
        <img src="{{ asset('img/logo/Byond-logo-black.png') }}" alt="Byond Logo" loading="lazy" width="160">
    </a>

    <!-- Desktop Navigation Menu -->
    <ul class="desktop-menu">
        <li>
            <a href="{{ route('home') }}">Home</a>
        </li>
        <li class="dropdown">
            <a href="#" class="shop-link" onclick="toggleDropdown(event)" aria-expanded="false">
                Shop
            </a>
            <ul>
                <li><a href="{{ route('shop-page') }}">All Products</a></li>
                @if(isset($categories) && count($categories) > 0)
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('shop-category', $category->category_name) }}">
                                {{ $category->category_name }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </li>
        <li>
            <a href="{{ route('view.contact') }}">Contact Us</a>
        </li>
        <li>
            <a href="{{ route('aboutus') }}">About Us</a>
        </li>
    </ul>

    <!-- Utility Icons -->
    <div class="header__utils">
        <!-- Profile/Login Icon -->
        @guest
            <a href="{{ route('login') }}" class="util__icon" aria-label="Login">
                <img src="{{ asset('img/icons/Profile_Icon.png') }}" alt="Profile" loading="lazy">
            </a>
        @else
            <div class="util__profile">
                <a href="{{ optional(Auth::user())->usertype === 'admin' ? route('admin.dashboard') : route('user.orders') }}" 
                   class="util__icon" 
                   aria-label="Profile">
                    <img src="{{ asset('img/icons/Profile_Icon.png') }}" alt="Profile" loading="lazy">
                </a>
                <div class="util__tooltip">
                    <p>{{ optional(Auth::user())->name ?? optional(Auth::user())->email ?? 'Account' }}</p>
                </div>
            </div>
        @endguest

        <!-- Cart Icon -->
        <a href="{{ url('view-cart') }}" class="util__icon util__cart" aria-label="Shopping cart">
            <img src="{{ asset('img/icons/Cart_icon.png') }}" alt="Cart" loading="lazy">
            @php
                $cart = session()->get('cart', []);
                $cartCount = array_sum(array_column($cart, 'quantity'));
            @endphp
            <span class="util__badge cart-count" 
                id="cart-count-badge" 
                data-cart-count 
                style="display: {{ $cartCount > 0 ? 'flex' : 'none' }};" 
                aria-label="{{ $cartCount }} items in cart">
                {{ $cartCount }}
            </span>
        </a>
    </div>
</nav>

<!-- Mobile Menu (Accordion Style with Dropdown) -->
<ul class="mobile-menu-parent">
    <li>
        <a href="{{ route('home') }}">Home</a>
    </li>
    <li class="dropdown">
        <a href="#" class="shop-link-mobile" onclick="openMobileSubmenu(event)" aria-expanded="false">
            Shop
        </a>
        <ul>
            <li><a href="{{ route('shop-page') }}">All Products</a></li>
            @if(isset($categories) && count($categories) > 0)
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('shop-category', $category->category_name) }}">
                            {{ $category->category_name }}
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </li>
    <li>
        <a href="{{ route('view.contact') }}">Contact Us</a>
    </li>
    <li>
        <a href="{{ route('aboutus') }}">About Us</a>
    </li>
</ul>

<!-- Mobile Menu Overlay -->
<div class="sidebar-overlay" aria-hidden="true"></div>
