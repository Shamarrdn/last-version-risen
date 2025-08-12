<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/dashboard');
        }

        $user = Auth::user();
        
        // تحقق من أن المستخدم لديه دور superadmin
        if (!$user->hasRole('superadmin')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
