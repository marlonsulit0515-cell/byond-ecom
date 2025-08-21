<!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Byond</title>

    <link href="{{ asset('css/header.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/menumain.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/slideshow.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    
 </head>
 <body>

<style>
*{
     font-family: century-gothic, sans-serif !important;
}
   
</style>
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

 

 </body>
 </html>