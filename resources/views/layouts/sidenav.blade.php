<!-- Dashboard -->
<ul>
    <li><a href="{{ route('home') }}">Home</a></li>   
    <li><a href="{{ route('admin.dashboard') }}">Overview</a></li>
</ul>

<!-- Product Management -->
<ul>
    <li><a href="{{ route('admin.show-product') }}">All Products</a></li>
    <li><a href="{{ route('admin.categories') }}">Product Categories</a></li>
</ul>

<!-- Order Management -->
<ul>
    <li><a href="{{ route('orders.index') }}">All Orders</a></li>
</ul>
<ul>
    <li>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
    </li>
</ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>