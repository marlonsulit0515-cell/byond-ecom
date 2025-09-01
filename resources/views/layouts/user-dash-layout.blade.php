@extends('layouts.default')
<link href="{{ asset('css/user-dashboard.css') }}" rel="stylesheet" />
@section('maincontent')
<div class="dashboard-container flex min-h-screen">
    <!-- Sidebar -->
    <aside class="sidebar w-64">
        @include('layouts.user-sidenav')
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content flex-1">
        <div class="orders-dashboard">
            @yield('dashboard-content')
        </div>
    </main>
</div>
@endsection
