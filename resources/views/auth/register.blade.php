<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/font-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/screen-behavior.css') }}" rel="stylesheet" />
    <title>Account Registration</title>
</head>
<body>
     <div class="login-hero">
         <div class="login-head"></div>

         <div class="login-cont">
            <a href="{{ route('home') }}"> <img src="{{ asset('img/logo/Byond-Logo.png') }}" alt=""></a>
             <form method="POST" class="form-login" action="{{ route('register') }}">
                 @csrf
                 <h1>Create Account</h1>
                 <!-- Email Address -->
                 <div class="input-cont">
                     <input id="name" placeholder="Username" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                     @error('name')
                     <div class="mt-2">{{ $message }}</div>
                     @enderror
                     <input id="email" placeholder="Email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                     @error('email')
                     <div class="mt-2">{{ $message }}</div>
                     @enderror
                 <!-- Password -->
                     <input id="password" placeholder="Password" type="password" name="password" required autocomplete="new-password">
                     @error('password')
                     <div class="mt-2">{{ $message }}</div>
                     @enderror
  <!-- Confirm Password -->
                     <input id="password_confirmation" placeholder="Confirm Password" type="password" name="password_confirmation" required autocomplete="new-password">
                     @error('password_confirmation')
                     <div class="mt-2">{{ $message }}</div>
                     @enderror
                 </div>




                <ul class="action">

                <li style> <a href="{{ route('login') }}">
                         Already register? Log in here
                     </a></li>
                <li>   <button type="submit" class="ms-4">
                         Submit
                     </button></li>
                </ul>
 
             </form>
         </div>
     </div>
</body>
</html>