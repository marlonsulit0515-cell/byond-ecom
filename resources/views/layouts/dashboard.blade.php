<!-- Main Layout File for Admin Panel -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">


    <link href="{{ asset('css/universal-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin-table.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin-create-product.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/order-invoice.css') }}" rel="stylesheet" />
    <title>Admin Panel</title>
</head>

<body class="bg-white text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 bg-[#762c21] text-white h-16 flex items-center justify-between px-6 md:px-8 shadow-md z-50">
            <div class="flex items-center space-x-3">
                <!-- Mobile Sidebar Toggle -->
                <button id="sidebarToggle" class="md:hidden text-white focus:outline-none">
                    <!-- Hamburger Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- clickable logo -->
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo/Byond-logo-black.png') }}"
                        alt="Byond Logo"
                        loading="lazy"
                        class="h-10 w-auto" />
                </a>
            </div>

            @include('layouts.dashheader')
        </header>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed top-16 left-0 w-64 md:w-72 h-screen bg-[#f4eedf] border-r border-gray-200 overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
        <div class="p-6">
                @include('layouts.sidenav')
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="overlay"
            class="fixed inset-0 bg-black bg-opacity-40 hidden z-30 md:hidden"></div>

        <!-- Main Content -->
        <main class="pt-16 md:ml-72 flex-1 bg-white transition-all duration-300">
            <div class="p-4 sm:p-6 md:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('maincontent')
                </div>
            </div>
        </main>
    </div>
<script src="https://cdn.tailwindcss.com"></script>
<script src="{{ asset('script/toast-notif.js') }}"></script>
<script src="{{ asset('script/admin-order-management.js') }}" defer></script>
<script src="{{ asset('script/admin-product-form.js') }}"></script>
<script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const toggleButton = document.getElementById('sidebarToggle');

        toggleButton.addEventListener('click', () => {
            const isHidden = sidebar.classList.contains('-translate-x-full');
            sidebar.classList.toggle('-translate-x-full', !isHidden);
            overlay.classList.toggle('hidden', !isHidden);
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
</script>
</body>
</html>
