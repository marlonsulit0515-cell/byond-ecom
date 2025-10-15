<!-- Main Layout File for Admin Panel -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Admin Panel</title>
</head>

<body class="bg-white text-gray-900 ">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 bg-black text-white h-16 flex items-center justify-between px-8 shadow-md z-50">
            @include('layouts.dashheader')
        </header>

        <!-- Sidebar -->
        <aside class="fixed top-16 left-0 w-72 h-screen bg-gray-50 border-r border-gray-200 overflow-y-auto">
            <div class="p-6">
                @include('layouts.sidenav')
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-72 pt-16 min-h-screen bg-white">
            <div class="p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('maincontent')
                </div>
            </div>
        </main>
    </div>
</body>
</html>