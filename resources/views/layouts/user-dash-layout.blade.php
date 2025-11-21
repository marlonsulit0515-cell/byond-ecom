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
        <main class="flex-1 w-full">
            <div class="p-6 sm:p-8 lg:p-10 max-w-[1400px] mx-auto">
                @yield('dashboard-content')
            </div>
        </main>
    </div>
    <script src="{{ asset('script/user-orders.js') }}"></script>
    <script src="{{ asset('script/user-mobile-sidenav.js') }}"></script>
    <script src="{{ asset('script/navigation-bar.js') }}"></script>
</body>

</html>