<!-- Admin Dashboard Navigation -->
<ul class="sidenav-list">

    <!-- Dashboard -->
    <li class="sidenav-item">
        <a href="{{ route('admin.dashboard') }}"
           class="sidenav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h3v-5a1 1 0 011-1h4a1 1 0 011 1v5h3a1 1 0 001-1V7l-7-5z" clip-rule="evenodd"></path>
            </svg>
            <span class="sidenav-text">Dashboard</span>
        </a>
    </li>

    <!-- All Products -->
    <li class="sidenav-item">
        <a href="{{ route('admin.show-product') }}"
           class="sidenav-link {{ request()->routeIs('admin.show-product*') ? 'active' : '' }}">
           <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
            </svg>
            <span class="sidenav-text">All Products</span>
        </a>
    </li>

    <!-- All Orders -->
    <li class="sidenav-item">
        <a href="{{ route('orders.index') }}"
           class="sidenav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
            <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
            </svg>
            <span class="sidenav-text">All Orders</span>
        </a>
    </li>

    <!-- Product Categories -->
    <li class="sidenav-item">
        <a href="{{ route('admin.categories') }}"
           class="sidenav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
            </svg>
            <span class="sidenav-text">Product Categories</span>
        </a>
    </li>

    <!-- Shipping Settings -->
    <li class="sidenav-item">
        <a href="{{ route('admin.shipping-settings') }}"
           class="sidenav-link {{ request()->routeIs('admin.shipping-settings') ? 'active' : '' }}">
            <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
            </svg>
            <span class="sidenav-text">Shipping Settings</span>
        </a>
    </li>

    <!-- User Management -->
    <li class="sidenav-item">
        <a href="{{ route('admin.user-management') }}"
           class="sidenav-link {{ request()->routeIs('admin.user-management') ? 'active' : '' }}">
            <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
            </svg>
            <span class="sidenav-text">User Management</span>
        </a>
    </li>

    <!-- Inbox -->
    <li class="sidenav-item">
        <a href="{{ route('admin.inbox') }}"
           class="sidenav-link {{ request()->routeIs('admin.inbox') ? 'active' : '' }}">
            <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
            </svg>
            <span class="sidenav-text">Inbox</span>
        </a>
    </li>

    <!-- Logout -->
    <li class="sidenav-item">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="sidenav-link">
            <svg class="sidenav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span class="sidenav-text">Logout</span>
        </a>
    </li>

</ul>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>
