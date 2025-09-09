<link rel="stylesheet" href="{{ asset('css/menumain.css') }}">
<body>
    <div class="menuMain">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/logo/Byond-Logo.png') }}" alt="">
        </a>
        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="dropdown">
                <a href="#" class="shop-link" onclick="toggleDropdown()">Shop</a>
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
    </div>

    <script>
        function toggleDropdown() {
            document.querySelector('.dropdown').classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelector('.dropdown').classList.remove('active');
            }
        });
    </script>
</body>
