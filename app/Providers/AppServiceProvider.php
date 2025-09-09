<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load the auth.php routes manually
        Route::middleware('web')
            ->group(base_path('routes/auth.php'));

        View::composer('layouts.menumain', function ($view) {
        $view->with('categories', Category::all());
    });
    }
}
