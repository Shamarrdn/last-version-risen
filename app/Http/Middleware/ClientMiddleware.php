<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('superadmin')) {
                return redirect('/superadmin/dashboard');
            } elseif ($user->hasRole('admin')) {
                return redirect('/admin/dashboard');
            }
        }

        return $next($request);
    }
}
