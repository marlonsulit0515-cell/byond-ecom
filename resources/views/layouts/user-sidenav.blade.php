<!-- User Dashboard Navigation -->
<nav class="space-y-2 mb-8">
    <ul class="space-y-2">
        <!-- My Orders -->
        <li>
            <a href="{{ route('user.orders') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 
               {{ request()->routeIs('dashboard.orders*') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6 2a2 2 0 00-2 2v2h12V4a2 2 0 00-2-2H6zM4 8h12v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8z"></path>
                </svg>
                <span class="font-medium">My Orders</span>
            </a>
        </li>

        <!-- Profile -->
        <li>
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200 
               {{ request()->routeIs('dashboard.profile') ? 'bg-black text-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Profile</span>
            </a>
        </li>

        <!-- Back to Store -->
        <li>
            <a href="{{ url('home') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0L2.586 9H5v9a1 1 0 001 1h3v-6h2v6h3a1 1 0 001-1V9h2.414l-6.707-6.707z"></path>
                </svg>
                <span class="font-medium">Back to Store</span>
            </a>
        </li>

        <!-- Logout -->
        <li>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-200 hover:text-black transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="font-medium">Logout</span>
            </a>
        </li>
    </ul>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</nav>
