<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="icon" type="image/png" href="{{ asset('img/logo/ByondLogo-Brown.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="{{ asset('css/universal-style.css') }}" rel="stylesheet" />

    <title>Payment Success - {{ config('app.name') }}</title>
</head>
<body class="bg-gray-50">

    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- Success Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-slate-900 mb-3">Payment Successful!</h1>
                <p class="text-slate-600 mb-8">
                    Your order has been placed successfully. Thank you for shopping with us!
                </p>

                <div class="space-y-3">
                    <a href="{{ route('user.orders') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-md transition">
                        View My Orders
                    </a>
                    <a href="{{ route('shop-page') }}" class="block w-full bg-white hover:bg-gray-50 text-slate-900 font-medium px-6 py-3 rounded-md border border-gray-300 transition">
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 text-center text-sm text-slate-600">
                <p>A confirmation email has been sent to your email address.</p>
            </div>
        </div>
    </div>
</body>
</html>