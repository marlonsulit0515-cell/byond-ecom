@extends('layouts.default')
@section('maincontent')

{{-- Page Title --}}
<div class="page-title">
    <h1>{{ isset($category) ? $category : 'Shop Page' }}</h1>
</div>

{{-- Top Bar with Sort + Filter + Active Filters --}}
<div class="top-bar">
    <div class="top-bar-content">
        {{-- Left Side: Sort and Filter Button --}}
        <div class="top-bar-controls">
            {{-- Sort Dropdown --}}
            <form method="GET" action="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" id="sortForm" class="sort-form">
                <input type="hidden" name="price_from" value="{{ request('price_from') }}">
                <input type="hidden" name="price_to" value="{{ request('price_to') }}">
                <input type="hidden" name="availability" value="{{ request('availability') }}">
                <input type="hidden" name="size" value="{{ request('size') }}">

                <label for="sortSelect" class="sort-label">Sort:</label>
                <select name="sort" id="sortSelect" class="sort-select">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </form>

            {{-- Filter Button --}}
            <button id="filterToggle" class="filter-toggle-btn" aria-label="Toggle filters" aria-expanded="false">
                <svg class="filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filters
            </button>
        </div>

        {{-- Right Side: Active Filters --}}
        @if(request('availability') || request('price_from') || request('price_to') || request('size'))
            <div class="active-filters">
                @if(request('availability'))
                    <a href="{{ request()->fullUrlWithQuery(['availability' => null]) }}" 
                       class="filter-tag"
                       aria-label="Remove availability filter">
                        {{ ucfirst(str_replace('_', ' ', request('availability'))) }}
                        <span class="filter-tag-close" aria-hidden="true">&times;</span>
                    </a>
                @endif
                
                @if(request('price_from') || request('price_to'))
                    <a href="{{ request()->fullUrlWithQuery(['price_from' => null, 'price_to' => null]) }}" 
                       class="filter-tag"
                       aria-label="Remove price filter">
                        ₱{{ number_format(request('price_from', 0), 2) }} - ₱{{ number_format(request('price_to', 10000), 2) }}
                        <span class="filter-tag-close" aria-hidden="true">&times;</span>
                    </a>
                @endif
                
                @if(request('size'))
                    <a href="{{ request()->fullUrlWithQuery(['size' => null]) }}" 
                       class="filter-tag"
                       aria-label="Remove size filter">
                        Size: {{ strtoupper(request('size')) }}
                        <span class="filter-tag-close" aria-hidden="true">&times;</span>
                    </a>
                @endif
                
                <a href="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" 
                   class="clear-all-link"
                   aria-label="Clear all filters">
                    Clear All
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Filter Sidebar Overlay --}}
<div id="filterOverlay" class="filter-overlay" style="top: var(--header-height, 0px);" aria-hidden="true"></div>

{{-- Filter Sidebar on Left --}}
<aside id="filterSidebar" 
       class="filter-sidebar" 
       style="top: var(--header-height, 0px); height: calc(100vh - var(--header-height, 0px));"
       aria-label="Filters"
       role="dialog"
       aria-modal="true">
    
    {{-- Sidebar Header --}}
    <div class="sidebar-header">
        <h3 class="sidebar-title">Filters</h3>
        <button id="closeSidebar" class="close-sidebar-btn" aria-label="Close filters">
            <svg class="close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Filter Form --}}
    <div class="sidebar-content">
        <form method="GET" action="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" id="filterForm">
            <input type="hidden" name="sort" value="{{ request('sort') }}">

            {{-- Price Range --}}
            <div class="filter-section">
                <h4 class="filter-section-title">Price Range</h4>
                <div class="price-inputs">
                    <div class="input-group">
                        <label for="price_from" class="input-label">From</label>
                        <input type="number" 
                               id="price_from"
                               name="price_from" 
                               value="{{ request('price_from') }}" 
                               placeholder="0" 
                               min="0" 
                               max="10000" 
                               class="price-input"
                               aria-label="Minimum price">
                    </div>
                    <div class="input-group">
                        <label for="price_to" class="input-label">To</label>
                        <input type="number" 
                               id="price_to"
                               name="price_to" 
                               value="{{ request('price_to') }}" 
                               placeholder="10000" 
                               min="0" 
                               max="10000" 
                               class="price-input"
                               aria-label="Maximum price">
                    </div>
                </div>
            </div>

            {{-- Availability --}}
            <div class="filter-section">
                <h4 class="filter-section-title">Availability</h4>
                <div class="radio-group" role="radiogroup" aria-label="Product availability">
                    <label class="radio-label">
                        <input type="radio" 
                               name="availability" 
                               value="" 
                               {{ request('availability') == '' ? 'checked' : '' }} 
                               class="radio-input">
                        <span class="radio-text">All Products</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" 
                               name="availability" 
                               value="in_stock" 
                               {{ request('availability') == 'in_stock' ? 'checked' : '' }} 
                               class="radio-input">
                        <span class="radio-text">In Stock</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" 
                               name="availability" 
                               value="out_of_stock" 
                               {{ request('availability') == 'out_of_stock' ? 'checked' : '' }} 
                               class="radio-input">
                        <span class="radio-text">Out of Stock</span>
                    </label>
                </div>
            </div>

            {{-- Size --}}
            <div class="filter-section">
                <h4 class="filter-section-title">Size</h4>
                <div class="size-grid" role="radiogroup" aria-label="Product size">
                    @foreach (['' => 'All', 's' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', '2xl' => '2XL'] as $val => $label)
                        <label class="size-label">
                            <input type="radio" 
                                   name="size" 
                                   value="{{ $val }}" 
                                   {{ request('size') == $val ? 'checked' : '' }} 
                                   class="size-radio"
                                   aria-label="Size {{ $label }}">
                            <span class="size-option {{ request('size') == $val ? 'active' : '' }}">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Apply and Clear Buttons --}}
            <div class="filter-actions">
                <button type="submit" class="apply-btn">Apply Filters</button>
                <a href="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" 
                   class="clear-btn">
                    Clear All Filters
                </a>
            </div>
        </form>
    </div>
</aside>

{{-- Product Grid Display --}}
<div class="flex">
    <div class="flex-1">
        <div class="product-grid-container">
            <div class="product-grid">
                @forelse ($product as $item)
                    <article class="product-card">
                        <div class="product-image-wrapper">
                            <a href="{{ url('product-details', $item->id) }}" 
                               aria-label="View details for {{ $item->name }}">
                                <img class="product-image" 
                                     src="{{ asset('product/' . $item->image) }}" 
                                     alt="{{ $item->name }}"
                                     loading="lazy"
                                     width="280"
                                     height="280">
                                @if($item->hover_image)
                                    <img class="product-hover-image" 
                                         src="{{ asset('product/' . $item->hover_image) }}" 
                                         alt="{{ $item->name }} alternate view"
                                         loading="lazy"
                                         width="280"
                                         height="280">
                                @endif
                            </a>
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="{{ url('product-details', $item->id) }}">
                                    {{ $item->name }}
                                </a>
                            </h3>
                            
                            @if (!is_null($item->discount_price) && $item->discount_price > 0)
                                <div class="product-price-wrapper">
                                    <span class="discount-price" aria-label="Sale price">
                                        ₱{{ number_format($item->discount_price, 2) }}
                                    </span>
                                    <span class="original-price" aria-label="Original price">
                                        ₱{{ number_format($item->price, 2) }}
                                    </span>
                                </div>
                            @else
                                <p class="product-price">
                                    <span class="regular-price" aria-label="Price">
                                        ₱{{ number_format($item->price, 2) }}
                                    </span>
                                </p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <p class="empty-state-text">No products found matching your criteria.</p>
                        @if(request('availability') || request('price_from') || request('price_to') || request('size'))
                            <a href="{{ isset($category) ? route('shop-category', $category) : route('shop-page') }}" 
                               class="clear-all-link"
                               style="display: inline-block; margin-top: 1rem;">
                                Clear all filters
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if($product->hasPages())
            <div class="pagination-wrapper">
                {{ $product->appends(request()->input())->links('vendor.pagination.pagination-custom') }}
            </div>
        @endif
    </div>
</div>
@endsection