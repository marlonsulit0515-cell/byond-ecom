<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/font-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/screen-behavior.css') }}" rel="stylesheet" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Login</title>
    <style>
        .error-message {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        .input-cont.error input {
            border-color: #e74c3c;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="login-hero">
    <div class="login-head"></div>

    <div class="login-cont">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/logo/logo-name.png') }}" alt="Byond Logo">
        </a>

        <form method="POST" class="form-login" action="{{ route('login') }}" id="loginForm">
            @csrf
            <h2>Login</h2>

            <!-- General Error Messages -->
            @if ($errors->any())
                <div class="error-message" style="background: #fee; padding: 0.75rem; border-radius: 0.25rem; margin-bottom: 1rem;">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span><br>
                    @endforeach
                </div>
            @endif

            <!-- Email -->
            <div class="input-cont @error('email') error @enderror">
                <input id="email" 
                       placeholder="Email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required 
                       autofocus 
                       autocomplete="username">
                @error('email') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="input-cont @error('password') error @enderror">
                <input id="password" 
                       placeholder="Password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password">
                @error('password') <span class="error-message">{{ $message }}</span> @enderror
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
                <li><button type="submit" class="btn signin-btn" id="submitBtn">Sign in</button></li>
                <li><a href="{{ route('register') }}" class="btn create-btn">Create Account</a></li>
            </ul>

        </form>
    </div>
</div>

<script>
    // Prevent double submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing in...';
        
        // Re-enable after 3 seconds in case of error
        setTimeout(function() {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign in';
        }, 3000);
    });
</script>
</body>
</html>