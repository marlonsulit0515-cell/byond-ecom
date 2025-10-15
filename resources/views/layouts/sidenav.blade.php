<!-- layouts.sidenav for admin panel-->
<!-- Main Navigation -->
<nav class="space-y-2 mb-8">
    <ul class="space-y-2">
        <li>
            <a href="{{ route('home') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 {{ request()->routeIs('home') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                <span class="font-medium">Home Page</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.show-product') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 {{ request()->routeIs('admin.show-product') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h3v-5a1 1 0 011-1h4a1 1 0 011 1v5h3a1 1 0 001-1V7l-7-5z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">All Products</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.categories') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 {{ request()->routeIs('admin.categories') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
                <span class="font-medium">Product Categories</span>
            </a>
        </li>

        <li>
            <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 {{ request()->routeIs('orders.index') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">All Orders</span>
            </a>
        </li>
    </ul>
</nav>

<!-- User Section -->
<div class="pt-6 border-t border-gray-200">
    <ul>
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="font-medium">Logout</span>
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>