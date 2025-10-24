@extends('layouts.default')
@section('maincontent')

<div class="flex flex-col lg:flex-row min-h-[calc(100vh-100px)] bg-gray-50">
    <!-- Mobile Menu Toggle -->
    <button id="mobileSidebarToggle" class="lg:hidden fixed bottom-4 right-4 z-50 w-14 h-14 bg-black text-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-800 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Overlay for mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:h-[calc(100vh-100px)] overflow-y-auto lg:sticky lg:top-[100px]">
        <div class="p-6">
            <!-- Close button for mobile -->
            <button id="closeSidebar" class="lg:hidden absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Logo/Brand -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">My Account</h2>
            </div>
            
            @include('layouts.user-sidenav')
        </div>
    </aside>

    <!-- Main Dashboard Content -->
    <main class="flex-1 w-full lg:overflow-y-auto">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-6">
            @yield('dashboard-content')
        </div>
    </main>
</div>

<script>
// Mobile sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('mobileSidebarToggle');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    toggleBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar when clicking on navigation links (mobile)
    if (window.innerWidth < 1024) {
        sidebar?.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeSidebar);
        });
    }
});
</script>
@endsection