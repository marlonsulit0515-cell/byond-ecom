  <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">



  <div class="login-hero">
      <div class="login-head"></div>

      <div class="login-cont">
       <a href="{{ route('home') }}"> <img src="{{ asset('img/logo/Byond-Logo.png') }}" alt=""></a>
            






          <form method="POST" class="form-login" action="{{ route('login') }}">
              @csrf
              <!-- Email Address -->
              <h2>Login</h2>
              <div class="input-cont">
                  <!-- <label for="email">Email</label> -->
                  <input id="email" placeholder="Email" type="email" name="email" value="{{ old('email') }}"
                      required autofocus autocomplete="username">
                  @error('email')
                  <span>{{ $message }}</span>
                  @enderror
              </div>

              <!-- Password -->
              <div class="input-cont">
                  <!-- <label for="password">Password</label> -->
                  <input id="password" placeholder="Password" type="password" name="password" required autocomplete="current-password">
                  @error('password')
                  <span>{{ $message }}</span>
                  @enderror
              </div>

              <!-- Remember Me -->
             

              <ul class="rememberme">
                <li> <label for="remember_me">
                      <input id="remember_me" type="checkbox" name="remember">
                      <span>Remember me</span>
                  </label></li>
                <li>  @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}">
                      Forgot password?
                  </a>
                  @endif</li>
              </ul>
                 
          

              <!-- Actions -->

              <ul class="action">
                <li>
                     <button type="submit">Sign in</button>
                </li>
                <li>
                      <a href="{{ route('register') }}">
                          Create Account
                      </a>
                </li>
              </ul>
           
          </form>
      </div>

  </div>