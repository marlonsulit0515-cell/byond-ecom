@extends('layouts.default')
@section('maincontent')
    <div class="slideshow mb-12">
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
        
    </div>

<div class="mx-4 sm:mx-8 lg:mx-16 xl:mx-24"><br>

{{-- =======================
    Recent Drops
======================== --}}
<section class="mb-16">
    <div class="my-8 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-wider">
            Recent Drops
        </h2><br>
    </div>

    <div class="grid gap-6 sm:gap-8 lg:gap-10 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 mb-8">
        @foreach ($recentProducts as $item)
            {{-- Product card --}}
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
                    <h3 class="text-xs sm:text-sm text-gray-700 text-left uppercase tracking-wide group-hover:underline group-hover:underline-offset-4">
                        {{ $item->name }}
                    </h3>

                    @if (!is_null($item->discount_price) && $item->discount_price > 0)
                        <div class="mt-2 flex items-center justify-start gap-2">
                            <span class="text-sm tracking-wider text-black font-bold">₱{{ number_format($item->discount_price, 2) }}</span>
                            <span class="text-xs text-gray-400 line-through">₱{{ number_format($item->price, 2) }}</span>
                        </div>
                    @else
                        <p class="mt-2 text-left">
                            <span class="text-sm tracking-wider text-gray-900 font-semibold">₱{{ number_format($item->price, 2) }}</span>
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        <a href="{{ route('shop-page', ['sort' => 'latest']) }}" 
           id="main-button"
           class="text-sm font-medium text-gray-700 border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-100 transition">
            View All
        </a>
    </div>
</section>
<section class="mb-16">
    <div class="my-8 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-wider">
            Sale
        </h2>
    </div>

    <div class="grid gap-6 sm:gap-8 lg:gap-10 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 mb-8">
        @foreach ($salesProducts as $item)
            {{-- Same product card code --}}
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
                    <h3 class="text-xs sm:text-sm text-gray-700 text-left uppercase tracking-wide group-hover:underline group-hover:underline-offset-4">
                        {{ $item->name }}
                    </h3>

                    @if (!is_null($item->discount_price) && $item->discount_price > 0)
                        <div class="mt-2 flex items-center justify-start gap-2">
                            <span class="text-sm tracking-wider text-black font-bold">₱{{ number_format($item->discount_price, 2) }}</span>
                            <span class="text-xs text-gray-400 line-through">₱{{ number_format($item->price, 2) }}</span>
                        </div>
                    @else
                        <p class="mt-2 text-left">
                            <span class="text-sm tracking-wider text-gray-900 font-semibold">₱{{ number_format($item->price, 2) }}</span>
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        <x-redirect-button><a href="{{ route('shop-sale') }}">
            View All</a>
        </x-redirect-button> 
    </div>
</section>

{{-- =======================
    Tees
======================== --}}
<section class="mb-16">
    <div class="my-8 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-wider">
            Tees
        </h2>
    </div>

    <div class="grid gap-6 sm:gap-8 lg:gap-10 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($teesProducts as $item)
            {{-- Same product card code --}}
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
                    <h3 class="text-xs sm:text-sm text-gray-700 text-left uppercase tracking-wide group-hover:underline group-hover:underline-offset-4">
                        {{ $item->name }}
                    </h3>

                    @if (!is_null($item->discount_price) && $item->discount_price > 0)
                        <div class="mt-2 flex items-center justify-start gap-2">
                            <span class="text-sm tracking-wider text-black font-bold">₱{{ number_format($item->discount_price, 2) }}</span>
                            <span class="text-xs text-gray-400 line-through">₱{{ number_format($item->price, 2) }}</span>
                        </div>
                    @else
                        <p class="mt-2 text-left">
                            <span class="text-sm tracking-wider text-gray-900 font-semibold">₱{{ number_format($item->price, 2) }}</span>
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div><br>
    <div class="text-center">
        <x-redirect-button>
            <a href="{{ route('shop-page', ['category' => 'Tees']) }}" 
           id="main-button"></a>
            View All
        </x-redirect-button>
    </div>         
</section>


{{-- =======================
    Shop All CTA
======================== --}}
<section class="mb-16 text-center">
    <h1 class="text-xl font-semibold text-gray-800 mb-4">Can't Find What you're looking for?</h1>
    <a href="{{ route('shop-page') }}" 
       class="inline-block text-sm font-medium text-white bg-gray-800 border border-gray-800 rounded-md px-6 py-2 hover:bg-gray-900 transition">
        SHOP ALL PRODUCTS HERE
    </a>
</section>

</div>

    <div class="latestblog mt-16 border-t-4 border-b-4 border-black py-10 bg-gray-50">
  <h1 class="text-center text-3xl font-extrabold text-gray-900 mb-8 tracking-wide">
    LATEST BLOG
  </h1>

  <div class="relative overflow-hidden">
    <!-- Carousel container -->
    <div id="blogCarousel" class="flex transition-transform duration-500 ease-in-out">
      
      <!-- Blog Item -->
      <div class="min-w-full flex flex-col items-center text-center px-6">
        <img src="{{ asset('img/blog/BlogImg.png') }}" alt="Blog Image" class="w-full max-w-sm rounded-xl shadow-md">
        <span class="text-gray-600 text-sm mt-3">August 20, 2025</span>
        <h2 class="text-xl font-semibold mt-2 text-gray-800">BYOND EVENT IN MALL OF ASIA</h2>
        <p class="text-gray-600 mt-2 text-base leading-relaxed">
          Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam.
        </p>
        <a href="{{ url('/blog_content1') }}" 
           class="mt-4 inline-block bg-black text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-gray-800 transition">
           READ MORE
        </a>
      </div>

      <!-- Blog Item -->
      <div class="min-w-full flex flex-col items-center text-center px-6">
        <img src="{{ asset('img/blog/BlogImg.png') }}" alt="Blog Image" class="w-full max-w-sm rounded-xl shadow-md">
        <span class="text-gray-600 text-sm mt-3">August 20, 2025</span>
        <h2 class="text-xl font-semibold mt-2 text-gray-800">BYOND EVENT IN MALL OF ASIA</h2>
        <p class="text-gray-600 mt-2 text-base leading-relaxed">
          Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam.
        </p>
        <a href="{{ url('/blog_content2') }}" 
           class="mt-4 inline-block bg-black text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-gray-800 transition">
           READ MORE
        </a>
      </div>

      <!-- Blog Item -->
      <div class="min-w-full flex flex-col items-center text-center px-6">
        <img src="{{ asset('img/blog/BlogImg.png') }}" alt="Blog Image" class="w-full max-w-sm rounded-xl shadow-md">
        <span class="text-gray-600 text-sm mt-3">August 20, 2025</span>
        <h2 class="text-xl font-semibold mt-2 text-gray-800">BYOND EVENT IN MALL OF ASIA</h2>
        <p class="text-gray-600 mt-2 text-base leading-relaxed">
          Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam.
        </p>
        <a href="{{ url('/blog_content3') }}" 
           class="mt-4 inline-block bg-black text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-gray-800 transition">
           READ MORE
        </a>
      </div>

      <!-- Blog Item -->
      <div class="min-w-full flex flex-col items-center text-center px-6">
        <img src="{{ asset('img/blog/BlogImg.png') }}" alt="Blog Image" class="w-full max-w-sm rounded-xl shadow-md">
        <span class="text-gray-600 text-sm mt-3">August 20, 2025</span>
        <h2 class="text-xl font-semibold mt-2 text-gray-800">BYOND EVENT IN MALL OF ASIA</h2>
        <p class="text-gray-600 mt-2 text-base leading-relaxed">
          Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam.
        </p>
        <a href="{{ url('/blog_content4') }}" 
           class="mt-4 inline-block bg-black text-white text-sm font-semibold px-5 py-2 rounded-full hover:bg-gray-800 transition">
           READ MORE
        </a>
      </div>

    </div>

    <!-- Navigation Buttons -->
    <button id="prevBtn" 
      class="absolute left-3 top-1/2 transform -translate-y-1/2 bg-black text-white rounded-full p-2 hover:bg-gray-800">
      &#10094;
    </button>
    <button id="nextBtn" 
      class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-black text-white rounded-full p-2 hover:bg-gray-800">
      &#10095;
    </button>
  </div>

  <!-- Dots Navigation -->
  <div class="flex justify-center mt-6 space-x-2">
    <span class="dot w-3 h-3 bg-gray-400 rounded-full cursor-pointer" data-index="0"></span>
    <span class="dot w-3 h-3 bg-gray-300 rounded-full cursor-pointer" data-index="1"></span>
    <span class="dot w-3 h-3 bg-gray-300 rounded-full cursor-pointer" data-index="2"></span>
    <span class="dot w-3 h-3 bg-gray-300 rounded-full cursor-pointer" data-index="3"></span>
  </div>
</div>
    <div class="my-20 text-center max-w-3xl mx-auto px-6">
        <h2 class="text-4xl font-bold tracking-wide mb-6 text-gray-900">
            BYOND CO.
        </h2>
        <p class="text-lg text-gray-700 leading-relaxed">
            BYOND CO. is a collective founded on independence and creativity, inviting builders, makers, and dreamers to forge their own paths. The name — Build Your Own Next Destination — embodies a spirit of exploration, resilience, and pride in both process and outcome. At its heart, BYOND CO. champions craftsmanship, curiosity, and connection, aiming to create meaningful experiences and products that reflect purposeful, authentic movement through the world.
        </p>
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