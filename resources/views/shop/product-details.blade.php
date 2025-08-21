@extends('layouts.default')
<link href="{{ asset('css/product-details.css') }}" rel="stylesheet" />
<script src="{{ asset('script/product-details.js') }}"></script>

@section('maincontent')
<div class="product-container">
    <!-- Enhanced Product Image Gallery -->
    <div class="product-image">
        <div class="main-image-container">
            <img id="mainProductImage" src="/product/{{ $product->image }}" alt="{{ $product->name }}">
        </div>
        
        <!-- Image Thumbnails -->
        <div class="image-thumbnails">
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
            @if($product->model_image)
                <img src="/product/{{ $product->model_image }}" 
                     alt="Model view" 
                     class="thumbnail"
                     onclick="changeMainImage('/product/{{ $product->model_image }}', this)">
            @endif
        </div>
    </div>

    <!-- Product Details -->
    <div class="product-info">
        <h1 class="product-title">{{ $product->name }}</h1>

        @if (!is_null($product->discount_price) && $product->discount_price > 0)
            <!-- Discounted price -->
            <p class="discount-price">₱ {{ number_format($product->discount_price, 2) }}</p>
            <!-- Original crossed-out price -->
            <p class="original-price"><s>₱ {{ number_format($product->price, 2) }}</s></p>
        @else
            <!-- Regular price only -->
            <p class="price">₱ {{ number_format($product->price, 2) }}</p>
        @endif

        <!-- Size Selection with Stock Integration -->
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
                @endphp

                @foreach($sizes as $size => $stock)
                    <button type="button" 
                            class="size-btn {{ $stock > 0 ? ($size == $firstAvailableSize ? 'active' : '') : 'disabled' }}" 
                            data-size="{{ $size }}"
                            data-stock="{{ $stock }}"
                            {{ $stock <= 0 ? 'disabled' : '' }}
                            onclick="selectSize('{{ $size }}', {{ $stock }})">
                        {{ $size }}
                        @if($stock <= 0)
                            <span class="out-of-stock">(Out of Stock)</span>
                        @else
                            <span class="stock-info">({{ $stock }} left)</span>
                        @endif
                    </button>
                @endforeach
            </div>
            <p id="stock-message" class="stock-message"></p>
        </div>

        <!-- Quantity with Stock Validation -->
        <div class="product-section">
            <h3>Quantity</h3>
            <div class="quantity-box">
                <button class="qty-btn" id="decrease">−</button>
                <input type="number" id="quantity" value="1" min="1" max="{{ $firstAvailableSize ? $sizes[$firstAvailableSize] : 1 }}">
                <button class="qty-btn" id="increase">+</button>
            </div>
        </div>

        <!-- Enhanced Action Forms -->
        <div class="product-actions">
            <form action="{{ route('cart-page', $product->id) }}" method="POST" id="add-to-cart-form">
                @csrf
                <!-- Send quantity -->
                <input type="hidden" name="quantity" id="cart-quantity" value="1">
                
                <!-- Send selected size -->
                <input type="hidden" name="size" id="selected-size" value="{{ $firstAvailableSize }}">
                
                <!-- Send correct price -->
                <input type="hidden" name="price" value="{{ $product->discount_price && $product->discount_price > 0 ? $product->discount_price : $product->price }}">
                
                <button type="submit" class="btn-cart" id="add-to-cart-btn">Add to Cart</button>
            </form>

            <form action="{{ route('cart-page', $product->id) }}" method="POST" id="buy-now-form">
                @csrf
                <input type="hidden" name="quantity" id="cart-quantity-buy" value="1">
                <input type="hidden" name="size" id="selected-size-buy" value="{{ $firstAvailableSize }}">
                <input type="hidden" name="price" value="{{ $product->discount_price && $product->discount_price > 0 ? $product->discount_price : $product->price }}">
                <input type="hidden" name="buy_now" value="1">
                <button type="submit" class="btn-buy" id="buy-now-btn">Buy it now</button>
            </form>
        </div>

        <!-- Extra Info -->
        <p class="product-note">
           {{ $product->description }}
        </p>

        <!-- Accordion -->
        <details>
            <summary>Sizing and Information</summary>
            <img src="{{ asset('img/model/BYOND SIZE CHART.jpg') }}" width="300px" height="300px" alt="">
        </details>
        <details>
            <summary>Shipping and Returns</summary>
            <p>Details about shipping...</p>
        </details>
    </div>
</div>


<script>
    let selectedSize = '{{ $firstAvailableSize }}';
    let maxStock = {{ $firstAvailableSize ? $sizes[$firstAvailableSize] : 1 }};
</script>
@endsection
