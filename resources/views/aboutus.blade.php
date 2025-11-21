@extends('layouts.default')
<style>
    @media (max-width: 500px) {
        /* Hide all images */
        .number1img, .number2img, .number3img, .number5img, .number6img, .number7img {
            display: none !important;
        }

        /* Adjust the video container for mobile */
        .relative.w-full.h-\[700px\].mx-auto {
            height: 250px !important; /* Smaller height on mobile */
            margin-bottom: 100px !important;
            width: 100%;
        }

        /* Make the image static and full width/height of its container */
        .number4vid {
            position: static !important;
            margin: 0 !important;
            width: 100% !important;
            height: 100% !important;
            transform: none !important;
            border: none !important;
            box-shadow: none !important;
        }
    }

    /* Tablet Order Reversal */
    @media (max-width: 1024px) {
        .order-2.md\:order-1 {
            order: 2; /* Text moves to bottom */
        }
        .order-1.md\:order-2 {
            order: 1; /* Media moves to top */
            margin: 0 auto 70px auto;
        }
    }
</style>

@section('maincontent')
<section class="py-16 px-6" style="background-color: var(--bg-page);">
    <div class="max-w-7xl mx-auto">

        <div class="grid md:grid-cols-2 gap-12 items-center">
            
            {{-- LEFT SIDE TEXT --}}
            <div class="space-y-6 order-2 md:order-1"> 
                <div>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-semibold">About Us</h1><br>
                    
                    <p class="leading-relaxed">
                        <strong class="title" style="color: var(--color-primary);">BYOND CO.</strong> was born from the spirit of independence — a collective 
                        for builders, makers, and dreamers. Our name, <strong class="title" style="color: var(--color-primary);">Build Your Own Next Destination</strong>, 
                        is both a challenge and an invitation: to carve your own path, chase what fuels 
                        you, and create something that lasts. It's about pushing past boundaries, 
                        embracing the unknown, and finding pride in the process as much as the outcome.
                    </p>
                </div>

                <div>
                    <p class="leading-relaxed">
                        At its core, <strong class="title" style="color: var(--color-primary);">BYOND CO.</strong> celebrates craftsmanship, curiosity, and connection. 
                        Whether it's a product, a journey, or a story, every piece reflects a shared 
                        pursuit of going further — together. We're not just building destinations; we're 
                        building a way of moving through the world with purpose and authenticity.
                    </p>
                </div>
            </div>

            {{-- RIGHT SIDE IMAGE COLLAGE --}}
            <div class="relative w-full h-[700px] mx-auto md:w-[675px] order-1 md:order-2 md:mb-0 mb-20 overflow-hidden" style="min-height: auto;">
                
                {{-- 1. Image 1 - Scattered Top Left (Polaroid style) --}}
                <img class="number1img absolute hidden sm:block transition-transform duration-500 hover:scale-105" 
                    src={{ asset('img/photos/Mockup_1.png') }}
                    loading="lazy"
                    style="margin: 3% 0% 0% 5%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(-8deg); z-index: 4; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 8px solid white;">

                {{-- 2. Image 2 - Overlapping Upper Center --}}
                <img class="number2img absolute hidden sm:block transition-transform duration-500 hover:scale-105" 
                    src={{ asset('img/model/model1.jpg') }}
                    loading="lazy"
                    style="margin: 10% 0% 0% 35%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(4deg); z-index: 6; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 8px solid white;">

                {{-- 3. Image 3 - Tucked Behind Left --}}
                <img class="number3img absolute hidden sm:block transition-transform duration-500 hover:scale-105" 
                   src={{ asset('img/photos/image1.png') }}
                    loading="lazy"
                    style="margin: 25% 0% 0% 2%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(-3deg); z-index: 2; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 8px solid white;">

                {{-- 4. Center Image - Hero Focal Point --}}
                <img 
                    class="number4vid absolute sm:relative md:absolute mx-auto transition-transform duration-500 hover:scale-105"
                    src={{ asset('img/photos/image1.png') }}
                    loading="lazy"
                    style="margin: 32% 0% 0% 28%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(-2deg); z-index: 7; box-shadow: 0 6px 16px rgba(0,0,0,0.4); border: 8px solid white;">

                {{-- 5. Image 5 - Peeking Right Side --}}
                <img class="number5img absolute hidden sm:block transition-transform duration-500 hover:scale-105" 
                    src={{ asset('img/blog/BlogImg.png') }}
                    loading="lazy"
                    style="margin: 18% 0% 0% 62%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(6deg); z-index: 3; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 8px solid white;">

                {{-- 6. Image 6 - Bottom Scattered Left --}}
                <img class="number6img absolute hidden sm:block transition-transform duration-500 hover:scale-105" 
                    src={{ asset('img/photos/image1.jpg') }}
                    loading="lazy"
                    style="margin: 58% 0% 0% 8%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(5deg); z-index: 5; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 8px solid white;">

                {{-- 7. Image 7 - Bottom Right Layered --}}
                <img class="number7img absolute hidden sm:block transition-transform duration-500 hover:scale-105" 
                    src={{ asset('img/blog/BlogImg.png') }}
                    loading="lazy"
                    style="margin: 62% 0% 0% 56%; width: 34%; height: auto; top: 0; left: 0; transform: rotate(-4deg); z-index: 1; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 8px solid white;">
            </div>
            
        </div>
    </div>
</section>
@endsection