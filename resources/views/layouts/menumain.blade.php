<div class="menuMain">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/logo/logo-name.png') }}" alt="Byond Logo">
        </a>

        <!-- HAMBURGER BUTTON (visible only on mobile) -->
        <button class="menu-toggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Desktop menu structure -->
        <ul class="desktop-menu">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="dropdown">
                <a href="#" class="shop-link" onclick="toggleDropdown(event)">Shop</a>
                <ul>
                    <li><a href="{{ route('shop-page') }}">All Products</a></li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('shop-category', $category->category_name) }}">
                                {{ $category->category_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li><a href="{{ route('view.contact') }}">Contact Us</a></li>
            <li><a href="{{ route('aboutus') }}">About Us</a></li>
        </ul>

        <!-- Mobile menu structure (parent menu) -->
        <ul class="mobile-menu-parent">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="dropdown">
                <a href="#" class="shop-link-mobile" onclick="openMobileSubmenu(event)">Shop</a>
            </li>
            <li><a href="{{ route('view.contact') }}">Contact Us</a></li>
            <li><a href="{{ route('aboutus') }}">About Us</a></li>
        </ul>

        <!-- Mobile menu structure (child submenu) -->
        <ul class="mobile-menu-child">
            <li><a href="#" class="back-link" onclick="goBackMobile(event)">← Back</a></li>
            <li><a href="{{ route('shop-page') }}">All Products</a></li>
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('shop-category', $category->category_name) }}">
                        {{ $category->category_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay"></div>

 <script src="{{ asset('script/mobile-menu.js') }}"></script>