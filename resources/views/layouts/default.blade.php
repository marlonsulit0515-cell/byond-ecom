<!DOCTYPE html>
 <html lang="en">
 <head>
   <title>Byond Co.</title>
   @include('layouts.head')
   <script src="https://cdn.tailwindcss.com"></script>
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

   <script src="{{ asset('script/app.js') }}" defer ></script>
   <script src="{{ asset('script/cart.js') }}" defer ></script>

</body>
</html>