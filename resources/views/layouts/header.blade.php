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
                <a href="{{ Auth::user()->usertype === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}">
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
                <img src="{{ asset('img/icons/Cart_icon.png') }}" alt="">
            </a>
        </li>
    </ul>
</div>

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