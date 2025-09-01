        <ul>
            <li class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('user.dashboard') }}">Overview</a>
            </li>
            <li class="{{ request()->routeIs('dashboard.orders*') ? 'active' : '' }}">
                <a href="{{ route('user.orders') }}">My Orders</a>
            </li>
            <li class="{{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">
                <a href="{{ route('profile.edit') }}">Profile</a>
            </li>
            <li>
                <a href="{{ url('home') }}">Back to Store</a>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </li>
        </ul>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>