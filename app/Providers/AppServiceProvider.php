<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

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
        // Set Carbon locale to Indonesian
        Carbon::setLocale('id');
        
        // Share global cart count across all views
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->isCustomer()) {
                $cartCount = collect(session('cart', []))->sum('quantity');
                $view->with('globalCartCount', $cartCount);
            } else {
                $view->with('globalCartCount', 0);
            }
        });
    }
}
