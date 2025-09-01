<link href="{{ asset('css/login.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="login-hero">
    <div class="login-head"></div>

    <div class="login-cont">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/logo/Byond-Logo.png') }}" alt="Byond Logo">
        </a>

        <form method="POST" class="form-login" action="{{ route('login') }}">
            @csrf
            <h2>Login</h2>

            <!-- Email -->
            <div class="input-cont">
                <input id="email" placeholder="Email" type="email" name="email" value="{{ old('email') }}"
                       required autofocus autocomplete="username">
                @error('email') <span>{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="input-cont">
                <input id="password" placeholder="Password" type="password" name="password" required autocomplete="current-password">
                @error('password') <span>{{ $message }}</span> @enderror
            </div>

             <!-- Divider -->
            <div class="divider">
                <span>or</span>
            </div>

            <!-- Social Login Buttons -->
            <div class="social-login">
                <a href="{{ route('login.google') }}" class="google-btn">
                    <i class="fa-brands fa-google"></i> Sign in with Google
                </a>
            </div>
            <!-- Remember + Forgot -->
            <ul class="rememberme">
                <li>
                    <label for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </li>
                <li>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="btn forgot-btn">Forgot password?</a>
                    @endif
                </li>
            </ul>

            <!-- Actions -->
            <ul class="action">
                <li><button type="submit" class="btn signin-btn">Sign in</button></li>
                <li><a href="{{ route('register') }}" class="btn create-btn">Create Account</a></li>
            </ul>

        </form>
    </div>
</div>
