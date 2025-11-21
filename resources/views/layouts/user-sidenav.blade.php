<!-- User Dashboard Navigation -->
  <ul class="sidenav-list">
    <!-- My Orders -->
    <li class="sidenav-item">
      <a href="{{ route('user.orders') }}"
         class="sidenav-link {{ request()->routeIs('user.orders*') ? 'active' : '' }}">
        <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
          <path d="M6 2a2 2 0 00-2 2v2h12V4a2 2 0 00-2-2H6zM4 8h12v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8z"></path>
        </svg>
        <span class="sidenav-text">My Purchase</span>
      </a>
    </li>

    <!-- Profile 
    <li class="sidenav-item">
      <a href="{{ route('profile.edit') }}"
         class="sidenav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
        <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
          <path fill-rule="evenodd" d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
        </svg>
        <span class="sidenav-text">Profile</span>
      </a>
    </li>-->

    <!-- Back to Store -->
    <li class="sidenav-item">
      <a href="{{ url('home') }}" class="sidenav-link">
        <svg class="sidenav-icon" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
          <path d="M10.707 2.293a1 1 0 00-1.414 0L2.586 9H5v9a1 1 0 001 1h3v-6h2v6h3a1 1 0 001-1V9h2.414l-6.707-6.707z"></path>
        </svg>
        <span class="sidenav-text">Back to Store</span>
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

