<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\View\Composers\CustomerLayoutComposer;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

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
        // Register view composer for admin layout detection
        View::composer('admin.*', function ($view) {
            $routeName = request()->route() ? request()->route()->getName() : '';
            $layout = str_starts_with($routeName, 'superadmin.') ? 'layouts.superadmin' : 'layouts.admin';
            $view->with('adminLayout', $layout);
        });
        
        // Register view composers
        View::composer('layouts.customer', CustomerLayoutComposer::class);

        View::composer('parts.navbar', function ($view) {
            $cartCount = 0;
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cartCount = $cart->items()->sum('quantity');
                }
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
