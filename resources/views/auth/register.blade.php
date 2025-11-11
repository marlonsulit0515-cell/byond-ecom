<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account Registration Page</title>
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

    <title>Account Registration</title>
</head>
<body>
    <div class="login-hero">
        <div class="login-cont">

            <!-- LOGO -->
            <div class="login-header">
                <a href="{{ route('home') }}" class="logo-link">
                    <img src="{{ asset('img/logo/Byond.Co_Primary_Logo_Red Mud.webp') }}" alt="Byond Logo" loading="lazy">
                </a>
            </div>

            <!-- FORM -->
            <form method="POST" class="form-login" action="{{ route('register') }}">
                @csrf

                <div class="form-header">
                    <h2>Create Account</h2>
                </div>

                <!-- FIELDS -->
                <div class="form-fields">

                    <!-- Username -->
                    <div class="form-group @error('name') has-error @enderror">
                        <x-text-input id="name" type="text" name="name" placeholder="Username"
                            :value="old('name')" required autofocus autocomplete="name" />
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group @error('email') has-error @enderror">
                        <x-text-input id="email" type="email" name="email" placeholder="Email"
                            :value="old('email')" required autocomplete="username" />
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group @error('password') has-error @enderror">
                        <x-text-input id="password" type="password" name="password" placeholder="Password"
                            required autocomplete="new-password" />
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm -->
                    <div class="form-group @error('password_confirmation') has-error @enderror">
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="Confirm Password" required autocomplete="new-password" />
                        @error('password_confirmation')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <!-- ACTIONS -->
                <div class="form-actions">
                    <button class="btn-primary-color btn-lg" type="submit">
                        Submit
                    </button>

                        <a href="{{ route('login') }}">Already registered? Sign in</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
