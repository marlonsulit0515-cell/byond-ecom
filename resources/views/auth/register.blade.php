     <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
     <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
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