 <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
 <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">



 <div class="login-hero">
     <div class="login-head"></div>

     <div class="login-cont">
       <a href="{{ route('home') }}"> <img src="{{ asset('img/logo/Byond-Logo.png') }}" alt=""></a>
            


         <!-- Session Status -->
         <x-auth-session-status class="mb-4" :status="session('status')" />

         <form method="POST" action="{{ route('password.email') }}" class="form-login">
             @csrf
             <div>
                 <h2>Reset your password</h2>
                 <p>We will send you an email to reset your password</p>
             </div>

             <!-- Email Address -->
             <div class="input-cont">
                 <!-- <x-input-label for="email" :value="__('Email')" /> -->
                 <x-text-input id="email" placeholder="Email" type="email" name="email" :value="old('email')" required autofocus />
                 <x-input-error :messages="$errors->get('email')" />
             </div>


             <ul class="action">
                 <li> <x-primary-button>
                         Email Password Reset Link
                     </x-primary-button></li>

                 <li><a href="{{ route ('login') }}">Cancel</a></li>


             </ul>


         </form>
     </div>



 </div>