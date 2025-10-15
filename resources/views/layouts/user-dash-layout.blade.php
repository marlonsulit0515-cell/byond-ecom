@extends('layouts.default')

@section('maincontent')
<link href="{{ asset('css/user-dashboard.css') }}" rel="stylesheet" />
<script src="https://cdn.tailwindcss.com"></script>

<div class="flex min-h-screen bg-gray-50">
    <!-- Fixed Sidebar (relative to main content area) -->
    <aside class="sticky top-0 left-0 h-screen w-64 bg-white border-r border-gray-200 overflow-y-auto z-10 flex-shrink-0">
        <div class="p-6">
            <!-- Logo/Brand -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">My Account</h2>
            </div>
            
            @include('layouts.user-sidenav')
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 min-h-screen">
        <!-- Content Wrapper with consistent padding -->
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('dashboard-content')
        </div>
    </main>
</div>
@endsection