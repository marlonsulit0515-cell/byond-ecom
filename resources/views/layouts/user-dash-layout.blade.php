<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">


        <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
        <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">
        
        <link rel="stylesheet"href="{{ asset('css/universal-style.css') }}" rel="stylesheet" /> {{-- Font Style,Size,Colors. --}}
        <link href="{{ asset('css/screen-behavior.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/pagination.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/navigation-bar.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/user-order.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/sidenav.css') }}" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('css/cancellation-modal.css') }}">
        <script src="https://cdn.tailwindcss.com"></script> 
    <title>My Account</title>
</head>

<body class="bg-white text-gray-900">
    <div class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm h-[72px]">
        @include('layouts.navigation-bar')
    </div>

    <div class="pt-[72px] md:pt-[120px] flex min-h-screen bg-gray-50">
        
        <aside id="sidebar"
               class="fixed top-[72px] left-0 w-64 h-[calc(100vh-72px)] bg-[#f4eedf] border-r border-gray-200 overflow-y-auto transform -translate-x-full 
                      md:top-[120px] md:h-[calc(100vh-104px)] md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
            <div class="p-6">
                <button id="closeSidebar" 
                        class="md:hidden absolute top-4 right-4 text-gray-600 hover:text-gray-800 focus:outline-none"
                        aria-label="Close sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div class="mb-6 pb-4 border-b border-gray-300">
                    <h2 class="text-xl font-bold text-gray-900">My Account</h2>
                </div>
                @include('layouts.user-sidenav')
            </div>
        </aside>

        <main class="flex-1 w-full md:ml-64">
            <div class="p-6 sm:p-8 lg:p-10 max-w-[1400px] mx-auto">
                @yield('dashboard-content')
            </div>
        </main>
    </div>

    <button id="sidebarToggle"
            class="md:hidden fixed top-24 right-4 z-50 bg-black text-white w-12 h-12 rounded-lg shadow-lg flex items-center justify-center focus:outline-none hover:bg-gray-800 transition-colors"
            aria-label="Open sidebar">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <div id="overlay" class="fixed inset-0 top-[72px] h-[calc(100vh-72px)] bg-black bg-opacity-50 hidden z-30 md:hidden"></div>


    <script src="{{ asset('script/user-orders.js') }}"></script>
    <script src="{{ asset('script/user-mobile-sidenav.js') }}"></script>
    <script src="{{ asset('script/navigation-bar.js') }}"></script>
</body>

</html>