@extends('layouts.default')
@section('maincontent')
<link href="{{ asset('css/product-card.css') }}" rel="stylesheet" />
<script src="https://cdn.tailwindcss.com"></script>
<h1 class="text-2xl font-bold text-gray-800 mb-4">
    {{ isset($category) ? $category : 'Shop Page' }}
</h1>

{{-- Top Bar with Sort + Filter --}}
<div class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4 mb-6">
    <div class="flex items-center justify-between gap-4">
        <!-- Left side: Sort + Filter Button -->
        <div class="flex items-center gap-3">
            <!-- Sort Dropdown -->
            <form method="GET" action="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" id="sortForm" class="flex items-center gap-2">
                <!-- Preserve all current filters when sorting -->
                <input type="hidden" name="price_from" value="{{ request('price_from') }}">
                <input type="hidden" name="price_to" value="{{ request('price_to') }}">
                <input type="hidden" name="availability" value="{{ request('availability') }}">
                <input type="hidden" name="size" value="{{ request('size') }}">

                <label class="text-sm font-medium text-gray-700">Sort:</label>
                <select name="sort" id="sortSelect"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </form>

            <!-- Filter Button -->
            <button id="filterToggle" 
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filters
            </button>
        </div>
    </div>
</div>

{{-- Filter Sidebar Overlay --}}
<div id="filterOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" style="top: var(--header-height, 0px);"></div>

{{-- Filter Sidebar on Left --}}
<div id="filterSidebar" 
     class="fixed left-0 w-80 bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto" 
     style="top: var(--header-height, 0px); height: calc(100vh - var(--header-height, 0px));">
    
    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
        <button id="closeSidebar" class="p-2 hover:bg-gray-100 rounded-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Filter Form --}}
    <div class="p-4 lg:p-6">
        <form method="GET" action="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" id="filterForm">
            <!-- Preserve sort when filtering -->
            <input type="hidden" name="sort" value="{{ request('sort') }}">

            {{-- Price Range --}}
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Price Range</h4>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">From</label>
                        <input type="number" name="price_from" value="{{ request('price_from') }}" 
                               placeholder="0" min="0" 
                               onchange="document.getElementById('filterForm').submit()"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">To</label>
                        <input type="number" name="price_to" value="{{ request('price_to') }}" 
                               placeholder="10000" min="0" 
                               onchange="document.getElementById('filterForm').submit()"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- Availability --}}
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Availability</h4>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="availability" value="" 
                               {{ request('availability') == '' ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()"
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">All Products</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="availability" value="in_stock" 
                               {{ request('availability') == 'in_stock' ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()"
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">In Stock</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="availability" value="out_of_stock" 
                               {{ request('availability') == 'out_of_stock' ? 'checked' : '' }}
                               onchange="document.getElementById('filterForm').submit()"
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Out of Stock</span>
                    </label>
                </div>
            </div>

            {{-- Size --}}
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Size</h4>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (['' => 'All', 's' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', '2xl' => '2XL'] as $val => $label)
                        <label class="flex items-center justify-center">
                            <input type="radio" name="size" value="{{ $val }}" 
                                   {{ request('size') == $val ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()"
                                   class="sr-only">
                            <span class="size-option {{ request('size') == $val ? 'active' : '' }}">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Clear --}}
            <div class="pt-4 border-t border-gray-200">
                <a href="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" 
                   class="w-full inline-block text-center px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Clear All Filters
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Active Filters --}}
@if(request('availability') || request('price_from') || request('price_to') || request('size'))
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @if(request('availability'))
            <a href="{{ request()->fullUrlWithQuery(['availability' => null]) }}"
               class="flex items-center gap-1 px-3 py-1 bg-gray-100 text-sm rounded-full">
                {{ ucfirst(str_replace('_', ' ', request('availability'))) }}
                <span class="text-gray-500 hover:text-red-600">&times;</span>
            </a>
        @endif
        @if(request('price_from') || request('price_to'))
            <a href="{{ request()->fullUrlWithQuery(['price_from' => null, 'price_to' => null]) }}"
               class="flex items-center gap-1 px-3 py-1 bg-gray-100 text-sm rounded-full">
                ₱{{ number_format(request('price_from', 0), 2) }} - ₱{{ number_format(request('price_to', 10000), 2) }}
                <span class="text-gray-500 hover:text-red-600">&times;</span>
            </a>
        @endif
        @if(request('size'))
            <a href="{{ request()->fullUrlWithQuery(['size' => null]) }}"
               class="flex items-center gap-1 px-3 py-1 bg-gray-100 text-sm rounded-full">
                Size: {{ strtoupper(request('size')) }}
                <span class="text-gray-500 hover:text-red-600">&times;</span>
            </a>
        @endif
        <a href="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" 
           class="text-sm text-gray-500 underline hover:text-gray-800">
            Clear All
        </a>
    </div>
@endif

<!--Product Grid Display Shop Page-->
<div class="flex">
    <div class="flex-1">
        <div class="mt-8 mb-12 mx-4 sm:mx-8 lg:mx-16 xl:mx-24">
            <div class="grid gap-6 sm:gap-8 lg:gap-10 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($product as $item)
                    <div class="group block">
                        <div class="relative overflow-hidden bg-gray-50">
                            <a href="{{ url('product-details', $item->id) }}">
                                <img class="default-img h-[200px] sm:h-[250px] lg:h-[280px] w-full object-contain transition duration-500 group-hover:scale-105" 
                                     src="{{ asset('product/' . $item->image) }}" 
                                     alt="{{ $item->name }}">
                                @if($item->hover_image)
                                    <img class="hover-img absolute top-0 left-0 h-[200px] sm:h-[250px] lg:h-[280px] w-full object-contain opacity-0 transition-opacity duration-500 group-hover:opacity-100" 
                                         src="{{ asset('product/' . $item->hover_image) }}" 
                                         alt="{{ $item->name }}">
                                @endif
                            </a>
                        </div>
                        
                        <div class="relative bg-white pt-3">
                            <h3 class="text-xs sm:text-sm text-gray-700 text-start uppercase tracking-wide group-hover:underline group-hover:underline-offset-4">
                                {{ $item->name }}
                            </h3>
                            
                            @if (!is_null($item->discount_price) && $item->discount_price > 0)
                                <div class="mt-2 flex items-center justify-start gap-2">
                                    <span class="text-sm tracking-wider text-black-600 font-semibold">₱{{ number_format($item->discount_price, 2) }}</span>
                                    <span class="text-xs text-gray-400 line-through">₱{{ number_format($item->price, 2) }}</span>
                                </div>
                            @else
                                <p class="mt-2 text-start">
                                    <span class="text-sm tracking-wider text-gray-900 font-bold">₱{{ number_format($item->price, 2) }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach     
            </div>
        </div>
        
        @if($product->hasPages())
            <div class="mt-8 mb-12 flex justify-center">
                <div class="pagination-custom">
                    {{ $product->appends(request()->input())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
<script src="{{ asset('script/shop-page.js') }}"></script>
@endsection