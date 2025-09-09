@extends('layouts.default')
@section('maincontent')
<link href="{{ asset('css/shop.css') }}" rel="stylesheet" />
<h1>Shop Page</h1>
@if(isset($category))
    <h2>Category: {{ $category }}</h2>
@endif
<div class="product-main">
        <div class="product-item">
            {{-- Loop through products passed from ShopController --}}
            @foreach ($product as $item)
                <div class="itemCont">
                    
                    {{-- Product image clickable to details --}}
                    <div class="image-container">
                        <a href="{{ url('product-details', $item->id) }}">
                            {{-- Default image --}}
                            <img class="default-img" src="{{ asset('product/' . $item->image) }}" alt="{{ $item->name }}">
                            
                            {{-- Hover image (only show if exists) --}}
                            @if($item->hover_image)
                                <img class="hover-img" src="{{ asset('product/' . $item->hover_image) }}" alt="{{ $item->name }}">
                            @endif
                        </a>
                    </div>
                    
                    {{-- Product information and pricing --}}
                    <div class="product-info">
                        <h2 class="product-name">{{ $item->name }}</h2>
                        
                        {{-- Show discount price if available --}}
                        @if (!is_null($item->discount_price) && $item->discount_price > 0)
                            <p class="discount-price">₱ {{ number_format($item->discount_price, 2) }}</p>
                            <p class="original-price"><s>₱ {{ number_format($item->price, 2) }}</s></p>
                        @else
                            <p class="price">₱ {{ number_format($item->price, 2) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach     
        </div>
    </div>
@endsection
