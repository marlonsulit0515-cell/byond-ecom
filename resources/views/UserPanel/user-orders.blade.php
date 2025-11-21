@extends('layouts.user-dash-layout')
@section('dashboard-content')

<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div> <h1 class="page-title">My Orders</h1>
        <p class="page-subtitle">Track and manage your orders</p>
    </div>
    
    <ul style="list-style: none; margin: 0; padding: 0;"> <li class="sidenav-item">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="sidenav-link btn-primary-color btn-sm" >
                <svg class="sidenav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="">Logout</span>
            </a>
        </li>
    </ul>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

</div>

<!-- Order Status Navigation -->

    <!-- Mobile: Dropdown -->
  <div class="status-mobile-wrapper">
    <div class="status-mobile-container">
        <select id="statusFilter" 
                class="status-mobile-select"
                data-filter="status"
                aria-label="Filter orders by status">
            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Orders</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>To Pay</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>To Ship</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>To Receive</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="cancellation_requested" {{ request('status') == 'cancellation_requested' ? 'selected' : '' }}>Cancellation Requested</option>
            <option value="return_refund" {{ request('status') == 'return_refund' ? 'selected' : '' }}>Return/Refund</option>
        </select>

        <svg xmlns="http://www.w3.org/2000/svg" class="status-mobile-arrow" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </div>
</div>


    <!-- Desktop: Tabs -->
    <div class="status-desktop">
        <div class="status-tabs">
            <a href="#" 
               class="status-tab {{ request('status') == 'all' || !request('status') ? 'is-active' : '' }}"
               data-status="all"
               aria-current="{{ request('status') == 'all' || !request('status') ? 'page' : 'false' }}">
                All
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'pending' ? 'is-active' : '' }}"
               data-status="pending"
               aria-current="{{ request('status') == 'pending' ? 'page' : 'false' }}">
                To Pay
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'processing' ? 'is-active' : '' }}"
               data-status="processing"
               aria-current="{{ request('status') == 'processing' ? 'page' : 'false' }}">
                To Ship
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'shipped' ? 'is-active' : '' }}"
               data-status="shipped"
               aria-current="{{ request('status') == 'shipped' ? 'page' : 'false' }}">
                To Receive
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'completed' ? 'is-active' : '' }}"
               data-status="completed"
               aria-current="{{ request('status') == 'completed' ? 'page' : 'false' }}">
                Completed
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'cancelled' ? 'is-active' : '' }}"
               data-status="cancelled"
               aria-current="{{ request('status') == 'cancelled' ? 'page' : 'false' }}">
                Cancelled
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'cancellation_requested' ? 'is-active' : '' }}"
               data-status="cancellation_requested"
               aria-current="{{ request('status') == 'cancellation_requested' ? 'page' : 'false' }}">
                Cancellation Requested
            </a>
            <a href="#" 
               class="status-tab {{ request('status') == 'return_refund' ? 'is-active' : '' }}"
               data-status="return_refund"
               aria-current="{{ request('status') == 'return_refund' ? 'page' : 'false' }}">
                Return/Refund
            </a>
        </div>
    </div>
    @include('UserPanel.order-table')
</div>
<!-- Toast Notification Container -->
<div id="toastContainer" class="toast-container" aria-live="polite" aria-atomic="true"></div>
@include('components.cancellation-modal')
@include('components.order-details')

@endsection