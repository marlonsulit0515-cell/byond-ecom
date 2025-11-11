<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    
    <!-- Fonts -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/labor-union" rel="stylesheet">
    <!-- Stylesheets -->
    <link href="{{ asset('css/universal-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/screen-behavior.css') }}" rel="stylesheet" />
    
    <title>Account Login</title>
</head>
<body>
    <div class="login-hero">
        <div class="login-cont">

            <!-- Logo Section -->
            <div class="login-header">
                <a href="{{ route('home') }}" class="logo-link">
                    <img src="{{ asset('img/logo/Byond.Co_Primary_Logo_Red Mud.webp') }}"
                         alt="Byond Logo">
                </a>
            </div>

            <!-- Login Form -->
            <form method="POST" class="form-login" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-header">
                    <h2>Login</h2>
                </div>

                @if ($errors->any())
                    <div class="toast">
                        <div>
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span><br>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-fields">
                    <div class="form-group @error('email') has-error @enderror">
                        <x-text-input id="email" type="email" name="email" placeholder="Email"
                                      :value="old('email')" required autofocus autocomplete="username" />
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group @error('password') has-error @enderror">
                        <x-text-input id="password" type="password" name="password" placeholder="Password"
                                      required autocomplete="current-password" />
                        @error('password')
                            <span class="field-error">{{ @message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-options">
                    <label for="remember_me" class="checkbox-label">
                        <input id="remember_me" type="checkbox" name="remember" class="checkbox-input">
                        <span class="checkbox-text">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <div class="form-divider">
                    <span class="divider-text">or</span>
                </div>

                <div class="social-login-section">
                    <a href="{{ route('login.google') }}" class="social-btn google-btn">
                        <i class="fa-brands fa-google"></i>
                        <span>Sign in with Google</span>
                    </a>
                </div>

                <div class="form-actions">
                    <button class="btn-primary-color btn-lg" type="submit" id="submitBtn">
                        Sign in
                    </button>

                    <a href="{{ route('register') }}">
                        Create Account
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function(e) {
                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return false;
                    }

                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Signing in...';

                    setTimeout(function() {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Sign in';
                    }, 3000);
                });
            }
        })();
    </script>
</body>
</html>
