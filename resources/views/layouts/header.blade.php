 <div class="headMain">
     <ul>
        <li><a href=""><img src="{{ asset('img/icons/FB_Icon.png') }}" alt=""></li>
        <li><a href=""><img src="{{ asset('img/icons/IG_Icon.png') }}" alt=""></li>
        <li><a href=""><img src="{{ asset('img/icons/Tiktok_Icon.png') }}" alt=""></li>
     </ul>

<ul>
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
        <li class="profile-dropdown" style="position:relative;">
            <a href="#" id="profile-icon">
                <img src="{{ asset('img/icons/Profile_Icon.png') }}" alt="">
            </a>
            <div id="dropdown-menu" style="display:none; position:absolute; right:0; background:#fff; border:1px solid #ccc; min-width:160px; z-index:100;">
                <div style="padding:8px; border-bottom:1px solid #eee;">
                    <strong>{{ Auth::user()->name }}</strong><br>
                    <span style="font-size:12px; color:#888;">
                        {{ Auth::user()->usertype === 'admin' ? 'Admin' : 'User' }}
                    </span>
                </div>
                @if(Auth::user()->usertype === 'admin')
                    <a href="{{ route('admin.dashboard') }}" style="display:block; padding:8px;">Admin Dashboard</a>
                @else
                    <a href="{{ route('userdash') }}" style="display:block; padding:8px;">User Dashboard</a>
                @endif
                <a href="{{ route('profile.edit') }}" style="display:block; padding:8px;">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width:100%; text-align:left; padding:8px; border:none; background:none;">Logout</button>
                </form>
            </div>
        </li>
    @endguest
    <li>
        <a href="{{ url('view-cart') }}" class="cart-icon">
            <img src="{{ asset('img/icons/Cart_icon.png') }}" alt="">
        </a>
    </li>
</ul>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileIcon = document.getElementById('profile-icon');
        const dropdownMenu = document.getElementById('dropdown-menu');
        if(profileIcon && dropdownMenu) {
            profileIcon.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', function(e) {
                if (!profileIcon.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        }
    });
</script>
</div>
 