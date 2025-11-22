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

        
            <div class="relative w-full h-[700px] mx-auto md:w-[600px] order-1 md:order-2 md:mb-0 mb-20 overflow-hidden">
                <img 
                    class="about-us-img absolute hidden sm:block transition-transform duration-500 hover:scale-105 shadow-xl"
                    src="{{ asset('img/photos/image.jpg') }}"
                    loading="lazy">
            </div>

        </div>
    </div>
</section>
@endsection