@extends('layouts.default')
@section('maincontent')


<div class="flex h-[calc(100vh-100px)] bg-gray-50 overflow-hidden">
    <!-- Sidebar (fixed width, fixed position inside layout) -->
    <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 h-full overflow-y-auto sticky top-[100px]">
        <div class="p-6">
            <!-- Logo/Brand -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">My Account</h2>
            </div>
            
            @include('layouts.user-sidenav')
        </div>
    </aside>

    <!-- Main Dashboard Content -->
    <main class="flex-1 overflow-y-auto">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('dashboard-content')
        </div>
    </main>
</div>
@endsection
