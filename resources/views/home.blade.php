@extends('layouts.default')

{{-- Custom CSS for items styling --}}
<link href="{{ asset('css/shop.css') }}" rel="stylesheet" />

@section('maincontent')

    {{-- ============================================
         HERO SLIDESHOW SECTION
         Auto-rotating image carousel with 3 slides
         ============================================ --}}
    <div class="slideshow">
        <div class="slideshow-container">
            
            {{-- Slide 1 --}}
            <div class="mySlides fade">
                <div class="numbertext">1 / 3</div>
                <img src="{{ asset('img/photos/Slide1.jpg') }}" style="width:100%">
            </div>
            
            {{-- Slide 2 --}}
            <div class="mySlides fade">
                <div class="numbertext">2 / 3</div>
                <img src="{{ asset('img/photos/Slide2.jpg') }}" style="width:100%">
            </div>
            
            {{-- Slide 3 --}}
            <div class="mySlides fade">
                <div class="numbertext">3 / 3</div>
                <img src="{{ asset('img/photos/Slide3.jpg') }}" style="width:100%">
            </div>
        </div>
        
        <br>
        
        {{-- Navigation dots for slideshow --}}
        <div style="text-align:center">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
        
        {{-- 
            JavaScript for automatic slideshow functionality
            - Auto-advances every 5 seconds
            - Updates navigation dots to show current slide
        --}}
    </div>

    {{-- ============================================
         NEW PRODUCTS SECTION
         Displays featured products in a grid layout
         ============================================ --}}
    <div class="product-main">
        <h1>New Drops</h1>
        
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

    {{-- ============================================
         LATEST BLOG PREVIEW SECTION
         Static preview of most recent blog post
         ============================================ --}}
    <div class="latestblog">
        <h1>LATEST BLOG</h1> 

        <div class="blog-cont">
            <ul>
            <li><img src="{{ asset('img/blog/BlogImg.png') }}" alt=""></li>
            <li><span>August 20, 2025</span></li>
            <li><h2>BYOND EVENT IN MALL OF ASIA</h2></li>
            <li>
                <p>LLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam</p>
            </li> 
            <li><a href="{{ url('/blog_content1') }}">READ MORE</a></li>
        </ul>
        
           <ul>
            <li><img src="{{ asset('img/blog/BlogImg.png') }}" alt=""></li>
            <li><span>August 20, 2025</span></li>
            <li><h2>BYOND EVENT IN MALL OF ASIA</h2></li>
            <li>
                <p>LLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam</p>
            </li> 
            <li><a href="{{ url('/blog_content2') }}">READ MORE</a></li>
        </ul>
           <ul>
            <li><img src="{{ asset('img/blog/BlogImg.png') }}" alt=""></li>
            <li><span>August 20, 2025</span></li>
            <li><h2>BYOND EVENT IN MALL OF ASIA</h2></li>
            <li>
                <p>LLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam</p>
            </li> 
            <li><a href="{{ url('/blog_content3') }}">READ MORE</a></li>
        </ul>
          <ul>
            <li><img src="{{ asset('img/blog/BlogImg.png') }}" alt=""></li>
            <li><span>August 20, 2025</span></li>
            <li><h2>BYOND EVENT IN MALL OF ASIA</h2></li>
            <li>
                <p>LLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam</p>
            </li> 
            <li><a href="{{ url('/blog_content4') }}">READ MORE</a></li>
        </ul>
        </div>
        
        
    </div>



        <script>
            let slideIndex = 0;
            showSlides();
            
            /**
             * Main slideshow function - handles automatic slide progression
             * Cycles through slides every 5 seconds and updates navigation indicators
             */
            function showSlides() {
                let i;
                let slides = document.getElementsByClassName("mySlides");
                let dots = document.getElementsByClassName("dot");
                
                // Hide all slides
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                
                // Increment slide index
                slideIndex++;
                
                // Reset to first slide if we've reached the end
                if (slideIndex > slides.length) {
                    slideIndex = 1;
                }
                
                // Remove active class from all navigation dots
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
                
                // Show current slide and activate corresponding dot
                slides[slideIndex - 1].style.display = "block";
                dots[slideIndex - 1].className += " active";
                
                // Set timeout for next slide transition (5 seconds)
                setTimeout(showSlides, 5000);
            }
        </script>
@endsection