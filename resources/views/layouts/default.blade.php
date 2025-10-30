<!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Byond Co.</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
   <script src="https://cdn.tailwindcss.com"></script>
    
    <!--Mobile Design for the Header and menumain-->
   <link href="{{ asset('css/mobile-view/dimension.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/mobile-view/menumain.css') }}" rel="stylesheet" />


   <link href="{{ asset('css/header.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/menumain.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/slideshow.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/footer.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/user-dashboard.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/font-style.css') }}" rel="stylesheet" />
   
   <link href="{{ asset('css/screen-behavior.css') }}" rel="stylesheet" />

   <link href="{{ asset('css/product-card.css') }}" rel="stylesheet" />
   <link href="{{ asset('css/button-design.css') }}" rel="stylesheet" />

 </head>
 <body>

   <header>
      @include('layouts.header')
      @include('layouts.menumain')
   </header>

      <main>
      @yield('maincontent')
      </main>

   <footer>
   @include('layouts.footer') 
   </footer>

 
<script src="{{ asset('script/screen-behavior.js') }}"></script>
 </body>
 </html>