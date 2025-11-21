@extends('layouts.default')

@section('maincontent')
<div class="product-container">

    <div class="product-image">
        <div class="main-image-container">
            <img id="mainProductImage" src="/product/{{ $product->image }}" alt="{{ $product->name }}">
        </div>
        
        <div class="image-thumbnails">
            @if($product->model_image)
                <img src="/product/{{ $product->model_image }}" 
                    alt="Model view" 
                    class="thumbnail"
                    onclick="changeMainImage('/product/{{ $product->model_image }}', this)">
            @endif
            @if($product->image)
                <img src="/product/{{ $product->image }}" 
                    alt="Main view" 
                    class="thumbnail active"
                    onclick="changeMainImage('/product/{{ $product->image }}', this)">
            @endif
            @if($product->hover_image)
                <img src="/product/{{ $product->hover_image }}" 
                    alt="Hover view" 
                    class="thumbnail"
                    onclick="changeMainImage('/product/{{ $product->hover_image }}', this)">
            @endif
            @if($product->closeup_image)
                <img src="/product/{{ $product->closeup_image }}" 
                    alt="Close-up view" 
                    class="thumbnail"
                    onclick="changeMainImage('/product/{{ $product->closeup_image }}', this)">
            @endif
        </div>
    </div>

    <div class="product-info">
        <h1 class="product-title">{{ $product->name }}</h1>

        @if (!is_null($product->discount_price) && $product->discount_price > 0)
            <p class="discount-price">₱ {{ number_format($product->discount_price, 2) }}</p>
            <p class="original-price"><s>₱ {{ number_format($product->price, 2) }}</s></p>
        @else
            <p class="price">₱ {{ number_format($product->price, 2) }}</p>
        @endif

        <div class="product-section">
            <h3>Size</h3>
            <div class="size-options">
                @php
                    $sizes = [
                        'S' => $product->stock_s ?? 0,
                        'M' => $product->stock_m ?? 0,
                        'L' => $product->stock_l ?? 0,
                        'XL' => $product->stock_xl ?? 0,
                        '2XL' => $product->stock_2xl ?? 0
                    ];
                    $firstAvailableSize = null;
                    foreach($sizes as $size => $stock) {
                        if($stock > 0 && !$firstAvailableSize) {
                            $firstAvailableSize = $size;
                        }
                    }
                    $initialStock = $firstAvailableSize ? $sizes[$firstAvailableSize] : 0;
                @endphp

                @foreach($sizes as $size => $stock)
                    <button type="button" 
                            class="size-btn {{ $stock > 0 ? ($size == $firstAvailableSize ? 'active' : '') : 'disabled' }}" 
                            data-size="{{ $size }}"
                            data-stock="{{ $stock }}"
                            {{ $stock <= 0 ? 'disabled' : '' }}
                            onclick="selectSize('{{ $size }}', {{ $stock }})">
                        {{ $size }}
                        <span class="stock-info">
                            @if($stock <= 0)
                                (Out of Stock)
                            @elseif($stock < 5)
                                (Only {{ $stock }} left!)
                            @else
                                ({{ $stock }} available)
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="product-section">
            <h3>Quantity</h3>
            <div class="flex gap-2 items-center border border-gray-300 px-3 py-2 w-max rounded-full">
                <button type="button" 
                        class="qty-btn cursor-pointer w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded"
                        id="decrease"
                        {{ $initialStock <= 0 ? 'disabled' : '' }}>
                    −
                </button>
                <input type="number" 
                    id="quantity" 
                    value="{{ $initialStock > 0 ? 1 : 0 }}" 
                    min="1" 
                    max="{{ $initialStock }}"
                    class="w-12 text-center border-0 outline-none bg-transparent" 
                    readonly />
                <button type="button" 
                        class="qty-btn cursor-pointer w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded"
                        id="increase"
                        {{ $initialStock <= 1 ? 'disabled' : '' }}>
                    +
                </button>
            </div>
        </div>

        <div class="product-page-actions flex gap-4 justify-between">
            <form action="{{ route('add-to-cart', $product->id) }}" method="POST" id="add-to-cart-form" class="w-full flex">
                @csrf
                <input type="hidden" name="quantity" id="cart-quantity" value="{{ $initialStock > 0 ? 1 : 0 }}">
                <input type="hidden" name="size" id="selected-size" value="{{ $firstAvailableSize }}">
                <button type="submit" class="btn-primary-color btn-md w-3/5" id="add-to-cart-btn" {{ $initialStock <= 0 ? 'disabled' : '' }}>
                    Add to Cart
                </button>
            </form>

            @if(auth()->check())
                <form action="{{ route('buy-now', $product->id) }}" method="POST" id="buy-now-form" class="w-full flex">
                    @csrf
                    <input type="hidden" name="quantity" id="buy-quantity" value="{{ $initialStock > 0 ? 1 : 0 }}">
                    <input type="hidden" name="size" id="buy-size" value="{{ $firstAvailableSize }}">
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" class="btn-primary-color btn-md w-3/5" id="buy-now-btn" {{ $initialStock <= 0 ? 'disabled' : '' }}>
                        Buy it now
                    </button>
                </form>
            @else
                <x-auth-modal />
                <div class="w-full flex">
                    <button type="button" class="btn-primary-color btn-md w-3/5" onclick="showAuthModal()" id="guest-buy-btn" {{ $initialStock <= 0 ? 'disabled' : '' }}>
                        Buy it now
                    </button>
                </div>
            @endif
        </div>


        <p class="product-note">
            {{ $product->description }}
        </p>

        <details>
            <summary>Sizing and Information</summary>
            <img src="{{ asset('img/model/BYOND SIZE CHART.jpg') }}" width="300" height="300" alt="Size Chart">
        </details>
        <details>
            <summary>Shipping and Returns</summary>
            <p>Details about shipping...</p>
        </details>
    </div>
</div>

<x-toast-notif />
@endsection