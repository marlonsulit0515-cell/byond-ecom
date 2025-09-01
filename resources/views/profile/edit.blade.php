@extends('layouts.user-dash-layout')

@section('dashboard-content')
<div class="flex min-h-screen">
    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-50 dark:bg-gray-800">
        <div class="orders-dashboard">
            <div class="mb-6">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    <span class="font-medium">{{ Auth::user()->name }}</span><br>
                    <span class="text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</span>
                </h2>
            </div>

            <div class="space-y-6">
                <!-- Update Profile -->
                <div class="p-6 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="p-6 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="p-6 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
