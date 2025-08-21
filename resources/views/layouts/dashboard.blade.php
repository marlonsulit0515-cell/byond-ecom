<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" />
    <title>Admin Panel</title>
</head>

<body>




    <div class="main">
        <header>
            @include('layouts.dashheader')

        </header>
        <div class="hero">
            <div class="sidebar">
                @include('layouts.sidenav')

            </div>
            <div class="heroframe">
                @yield('maincontent')
            </div>
        </div>

    </div>
</body>

</html>