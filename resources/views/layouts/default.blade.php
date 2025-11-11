<!DOCTYPE html>
 <html lang="en">
 <head>
   <title>Byond Co.</title>
   @include('layouts.head')
 </head>
 <body>
   <header>
      @include('layouts.navigation-bar')
   </header>

      <main>
      @yield('maincontent')
      </main>

   <footer>
   @include('layouts.footer') 
   </footer>


<script src="{{ asset('script/slideshow.js') }}"></script>
<script src="{{ asset('script/screen-behavior.js') }}"></script>
<script src="{{ asset('script/navigation-bar.js') }}"></script>
<script src="{{ asset('script/shop-page.js') }}"></script>
<script src="{{ asset('script/toast-notif.js') }}"></script>
<script src="{{ asset('script/cart.js') }}"></script>
<script src="{{ asset('script/product-details.js') }}"></script>
<script src="{{ asset('script/prdct-details.js') }}"></script>
<script src="https://cdn.tailwindcss.com"></script>
 </body>
 </html>