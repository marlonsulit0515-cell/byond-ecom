@extends('layouts.default')
@section('maincontent')
    <div class="slideshow">
        <div class="slideshow-container">

            <div class="mySlides slide">
                <img loading="lazy" src="{{ asset('img/photos/SlideShow_1.webp') }}" alt="">
            </div>

            <div class="mySlides slide">
                <img loading="lazy" src="{{ asset('img/photos/SlideImage.png') }}" alt="">
            </div>
            <div class="mySlides slide">
                <img loading="lazy" src="{{ asset('img/photos/Slide3.jpg') }}" alt="">
            </div>
            
        </div>
        
    </div>

    <section class="content-container">
        <div class="content-border">
            <span class="logo animatable bounceInLeft">
                <img src="{{ asset('img/logo/Byond.Co_Secondary_Logo_Red Mud.webp') }}" alt="logo" loading="lazy">
            </span>

            <strong class="about animatable fadeInUp">
                Born from a vision to create handcrafted quality garments, our brand is more than just fashion, Each piece is thoughtfully designed to embody the spirit of exploration, encouraging you to step out of your comfort zone and <span style="color: #762c21">BUILD YOUR OWN NEXT DESTINATION.</span>
            </strong>
        </div>

       <div class="image-collage">
            <span data-text="Shop now">
                <a href="{{ route('shop-page') }}">
                    <img src="{{ asset('img/photos/Mockup_1.webp') }}" alt="Shop now">
                </a>
            </span>
            
            <span data-text="About Us">
                <a href="{{ route('aboutus') }}">
                    <img src="{{ asset('img/photos/Mockup_2.webp') }}" alt="View more">
                </a>
            </span>

            <span data-text="Hoddies Collection">
                <a href="{{ route('shop-category', ['category' => 'Hoodies']) }}">
                    <img src="{{ asset('img/photos/Mockup_3.webp') }}" alt="Byond Tees Collection">
                </a>
            </span>

            <span data-text="Tees Collection">
                <a href="{{ route('shop-category', ['category' => 'Tees']) }}">
                    <img src="{{ asset('img/photos/Mockup_4.webp') }}" alt="Image">
                </a>
            </span>
        </div>


        <div class="content-border">
            <strong class="about" id="bottom">BYOND CO. was born from the spirit of independence — a collective for builders, makers, and dreamers. Our name, Build Your Own Next Destination, is both a challenge and an invitation: to carve your own path, chase what fuels you, and create something that lasts. It's about.....<a class="--font-content" href="{{ route('aboutus') }}">read more</a></strong>
            <span class="logo" id="secondary"><img src="{{ asset('img/logo/Byond.Co_Logo_Lockup Bonus_Red Mud.webp') }}" alt="logo" loading="lazy"></span>
        </div>
    </section>
@endsection